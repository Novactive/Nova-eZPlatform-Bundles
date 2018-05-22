<?php
namespace  MC\Bundle\PrivateContentAccessBundle\Controller;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use eZ\Publish\API\Repository\Values\Content\Location;
use MC\Bundle\PrivateContentAccessBundle\Entity\PrivateAccess;
use MC\Bundle\PrivateContentAccessBundle\Form\PrivateAccessForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FrontPrivateAccessController extends Controller
{
    public function askPasswordAction(Location $location = null, Request $request)
    {
        //$result = $this->getDoctrine()->getRepository('MCPrivateContentAccessBundle:PrivateAccess')->findOneBy(['locationId' => $location->contentInfo->mainLocationId, 'activate' => 1]);
        $form = $this->createFormBuilder()
            ->add('password', PasswordType::class, array(
                'constraints' => array(
                    new NotBlank()
                ),
                'label' => false
            ))
            ->add('locationId', HiddenType::class, array(
                'data' => $location->contentInfo->mainLocationId
            ))
            ->add('Valider', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $repository = $this->container->get('ezpublish.api.repository');
            $contentService = $repository->getLocationService(); //getContentService();
            $locationInfo = $contentService->loadLocation($session->get('locationid')); //loadContentInfo( $session->get('locationid') );

            $result = $this->getDoctrine()->getRepository('MCPrivateContentAccessBundle:PrivateAccess')->findOneBy(['locationId' => $data['locationId'], 'password' => $data['password'] ,'activate' => 1]);
            if($result != NULL){

                return $this->redirectToLocation($locationInfo,'');
            }
        }

        return $this->render(
            '@MCPrivateContentAccess/full/ask_password_form.html.twig',
            array('noLayout' => false, 'form' => $form->createView())
        );
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param string $uriFragment
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToLocation(Location $location, string $uriFragment = ''): RedirectResponse
    {
        return $this->redirectToRoute('_ezpublishLocation', [
            'locationId' => $location->id,
            '_fragment' => $uriFragment,
        ]);
    }
}