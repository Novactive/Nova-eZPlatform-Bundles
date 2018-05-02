<?php
namespace  MC\Bundle\PrivateContentAccessBundle\Controller;

use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use MC\Bundle\PrivateContentAccessBundle\Entity\PrivateAccess;
use MC\Bundle\PrivateContentAccessBundle\Form\PrivateAccessForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PrivateAccessController extends Controller
{
    public function privateAccessAction(Request $request)
    {
        $privateAccessForm = new PrivateAccess();
        $form = $this->createForm(PrivateAccessForm::class, $privateAccessForm);

        $form->handleRequest($request);
        if ($request->isMethod('POST')) {

            //$password = $passwordEncoder->encodePassword($privateAccessForm, $privateAccessForm->getPlainPassword());

            $privateAccessForm->setCreated((new \DateTime()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($privateAccessForm);
            $entityManager->flush();

            return $this->templating->renderResponse($template, ['form' => $form->createView()], new Response());
        }

        return $this->render(
            '@MCPrivateContentAccess/tabs/private_content_tab_form.html.twig',
            array('form' => $form->createView())
        );
    }
}