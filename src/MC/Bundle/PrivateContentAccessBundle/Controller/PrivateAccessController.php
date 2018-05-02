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

        // 1) build the form
        $privateAccessForm = new PrivateAccess();
        $form = $this->createForm(PrivateAccessForm::class, $privateAccessForm);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
//$form->isSubmitted() && $form->isValid() &&
            //var_dump($form);die();
            // 3) Encode the password (you could also do this via Doctrine listener)
            //$password = $passwordEncoder->encodePassword($privateAccessForm, $privateAccessForm->getPlainPassword());

            //$privateAccessForm->setPassword($password);
//var_dump($form->getData()->getPlainPassword());die();
            $privateAccessForm->setLocationPath('1/2/4/384');
            $privateAccessForm->setPassword($form->getData()->getPlainPassword());
$privateAccessForm->setCreated((new \DateTime()));

            // 4) save the User!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($privateAccessForm);
            $entityManager->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->templating->renderResponse($template, ['form' => $form->createView()], new Response());

            //return $this->redirectToRoute('replace_with_some_route');
        }

        //return $this->templating->renderResponse($template, ['form' => $form->createView()], new Response());

        //var_dump($form);die();

        //return new ContentView("@MCPrivateAccessBundle::tabs/private_content_tab_form.html.twig" , ['form' => $form]);
        return $this->render(
            '@MCPrivateContentAccess/tabs/private_content_tab_form.html.twig',
            array('form' => $form->createView())
        );
    }
}