<?php

/**
 * NovaeZProtectedContentBundle.
 *
 * @package   Novactive\Bundle\eZProtectedContentBundle
 *
 * @author    Novactive
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/eZProtectedContentBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZProtectedContentBundle\Listener;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Novactive\Bundle\eZProtectedContentBundle\Entity\ProtectedAccess;
use Novactive\Bundle\eZProtectedContentBundle\Entity\ProtectedTokenStorage;
use Novactive\Bundle\eZProtectedContentBundle\Form\RequestEmailProtectedAccessType;
use Ramsey\Uuid\Uuid;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmailProvided
{
    protected const SENDMAIL_ERROR = 'Impossible d\'envoyer le lien formaté à l\'adresse mail %s';
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var Swift_Mailer
     */
    private $mailer;
    /**
     * @var Swift_Message
     */
    protected $messageInstance;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(
        FormFactoryInterface $formFactory,
        Swift_Mailer $mailer,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        ParameterBagInterface $parameterBag
    ) {
        $this->formFactory = $formFactory;
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->parameterBag = $parameterBag;
        $this->messageInstance = new Swift_Message();
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }
        if (!$event->getRequest()->isMethod('POST')) {
            return;
        }
        $form = $this->formFactory->create(RequestEmailProtectedAccessType::class);

        $request = $event->getRequest();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $contentId = intval($data['content_id']);
            $token = Uuid::uuid4()->toString();
            $access = new ProtectedTokenStorage();

            $access->setMail($data['email']);
            $access->setContentId($contentId);
            $access->setCreated(new DateTime());
            $access->setToken($token);

            $this->entityManager->persist($access);
            $this->entityManager->flush();

            $currentUrl = sprintf(
                '%s://%s%s%s',
                $request->getScheme(),
                $request->getHost(),
                $request->getBaseUrl(),
                $request->getRequestUri()
            );
            $accessUrl = $currentUrl.'?mail='.$data['email'].'&token='.$token;
            $this->sendMail($contentId, $data['email'], $accessUrl);
            $response = new RedirectResponse($request->getRequestUri().'?waiting_validation='.$data['email']);
            $response->setPrivate();
            $event->setResponse($response);
        }
    }

    /**
     * @throws Exception
     */
    private function sendMail(int $contentId, string $receiver, string $link): void
    {
        /** @var ProtectedAccess $protectedAccess */
        $protectedAccess = $this->entityManager->getRepository(ProtectedAccess::class)
                                               ->findOneBy(['contentId' => $contentId]);

        $mailLink = "<a href='$link'>".$this->translator->trans('mail.link', [], 'ezprotectedcontent').'</a>';
        $bodyMessage = str_replace('{{ url }}', $mailLink, $protectedAccess->getEmailMessage());

        $message = $this->messageInstance
            ->setSubject($this->translator->trans('mail.subject', [], 'ezprotectedcontent'))
            ->setFrom($this->parameterBag->get('default_sender_email'))
            ->setTo($receiver)
            ->setContentType('text/html')
            ->setBody(
                $bodyMessage
            );

        try {
            $this->mailer->send($message);
        } catch (Exception $exception) {
            throw new Exception(sprintf(self::SENDMAIL_ERROR, $receiver));
        }
    }
}
