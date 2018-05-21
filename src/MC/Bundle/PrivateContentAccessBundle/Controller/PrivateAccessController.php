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

class PrivateAccessController extends Controller
{
    /**
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function privateAccessAction(Request $request, Location $location = null)
    {
        $session = $request->getSession();

        $privateAccess = new PrivateAccess();
        if($location) {
            $session->set('locationpath', $location->pathString);
            $session->set('locationid', $location->getContentInfo()->mainLocationId);
            $this->container->set('location', $location);
            $result = $this->getDoctrine()->getRepository('MCPrivateContentAccessBundle:PrivateAccess')->findOneBy(['location_path' => $location->pathString, 'activate' => 1]);
            if($result != NULL){

                $privateAccess->setActivate($result->activate);
            }
        }

        /**
         * @var Form
         */
        $form = $this->createForm(PrivateAccessForm::class, $privateAccess, array(
            'action' => $this->generateUrl('private_access'),
            'method' => 'POST',
        ));

        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            $data = $request->request->get('private_access_form');

            $repository = $this->container->get('ezpublish.api.repository');
            $contentService = $repository->getLocationService(); //getContentService();
            $locationInfo = $contentService->loadLocation($session->get('locationid'));

            //$password = $passwordEncoder->encodePassword($privateAccessForm, $privateAccessForm->getPlainPassword());

            $date = new \DateTime();
            $privateAccess->setCreated($date);
            $privateAccess->setPassword($data['plainPassword']['first']);
            $privateAccess->setLocationPath($session->get('locationpath'));
            $privateAccess->setActivate($data['activate']);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($privateAccess);
            $entityManager->flush();

            return $this->redirectToLocation($locationInfo,'/content/location/');
        }

        return $this->render(
            '@MCPrivateContentAccess/tabs/private_content_tab_form.html.twig',
            array('form' => $form->createView())
        );
    }

    public function askPasswordAction(Location $location = null, Request $request)
    {
        $node_path = '';
        $result = $this->getDoctrine()->getRepository('MCPrivateContentAccessBundle:PrivateAccess')->findOneBy(['location_path' => $node_path, 'activate' => 1]);
        $form = $this->createFormBuilder()
            ->add('password', PasswordType::class, array(
                'constraints' => array(
                    new NotBlank()
                ),
                'label' => false
            ))
            ->add('location_path', HiddenType::class)
            ->add('Valider', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $repository = $this->container->get('ezpublish.api.repository');
            $contentService = $repository->getLocationService(); //getContentService();
            $locationInfo = $contentService->loadLocation($session->get('locationid')); //loadContentInfo( $session->get('locationid') );

            $result = $this->getDoctrine()->getRepository('MCPrivateContentAccessBundle:PrivateAccess')->findOneBy(['location_path' => $location->pathString, 'password' => $data['password'] ,'activate' => 1]);
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