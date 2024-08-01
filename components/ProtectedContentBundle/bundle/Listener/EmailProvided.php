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
use Ibexa\Contracts\Core\Repository\ContentService;
use Novactive\Bundle\eZProtectedContentBundle\Entity\ProtectedAccess;
use Novactive\Bundle\eZProtectedContentBundle\Entity\ProtectedTokenStorage;
use Novactive\Bundle\eZProtectedContentBundle\Form\RequestEmailProtectedAccessType;
use Novactive\Bundle\eZProtectedContentBundle\Repository\ProtectedAccessRepository;
use Psr\Log\LoggerInterface;
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
     * @var Swift_Message
     */
    protected $messageInstance;

    public function __construct(
        protected readonly FormFactoryInterface $formFactory,
        protected readonly Swift_Mailer $mailer,
        protected readonly EntityManagerInterface $entityManager,
        protected readonly TranslatorInterface $translator,
        protected readonly ParameterBagInterface $parameterBag,
        protected readonly ProtectedAccessRepository $protectedAccessRepository,
        protected readonly ContentService $contentService,
        protected readonly LoggerInterface $logger,
    ) {
        $this->messageInstance = new Swift_Message();
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$event->isMainRequest()) {
            return;
        }

        if (!$request->isMethod('POST')) {
            return;
        }

        $contentId = (int) $request->attributes->get('contentId');

        if (!$contentId) {
            return;
        }

        try {
            $content = $this->contentService->loadContent($contentId);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), [
                'here' => __METHOD__ . ' ' . __LINE__,
                '$contentId' => $contentId,
            ]);
            return;
        }

        $protections = $this->protectedAccessRepository->findByContent($content);

        if (0 === count($protections)) {
            return;
        }

        $form = $this->formFactory->create(RequestEmailProtectedAccessType::class);

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
