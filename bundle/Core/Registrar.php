<?php
/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMailingBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Core;

use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use Novactive\Bundle\eZMailingBundle\Core\DataHandler\Registration;
use Novactive\Bundle\eZMailingBundle\Core\DataHandler\Unregistration;
use Novactive\Bundle\eZMailingBundle\Core\Mailer\Simple as SimpleMailer;
use Novactive\Bundle\eZMailingBundle\Entity\ConfirmationToken;
use Novactive\Bundle\eZMailingBundle\Entity\MailingList;
use Novactive\Bundle\eZMailingBundle\Entity\Registration as RegistrationEntity;
use Novactive\Bundle\eZMailingBundle\Entity\User;
use RuntimeException;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigResolver;

/**
 * Class Registrar.
 */
class Registrar
{
    /**
     * 5 hours.
     */
    const TOKEN_EXPIRATION_HOURS = 5;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SiteAccess
     */
    private $siteAccess;

    /**
     * @var SimpleMailer
     */
    private $mailer;

    /**
     * @var ConfigResolver
     */
    protected $configResolver;

    /**
     * Registrar constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param SiteAccess             $siteAccess
     * @param SimpleMailer           $mailer
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        SiteAccess $siteAccess,
        SimpleMailer $mailer,
        ConfigResolver $configResolver
    ) {
        $this->entityManager  = $entityManager;
        $this->siteAccess     = $siteAccess;
        $this->mailer         = $mailer;
        $this->configResolver = $configResolver;
    }

    /**
     * @param Registration $registration
     */
    public function askForConfirmation(Registration $registration): void
    {
        $user = $registration->getUser();
        if (null === $user) {
            throw new RuntimeException('User cannot be empty.');
        }
        $userRepo  = $this->entityManager->getRepository(User::class);
        $fetchUser = $userRepo->findOneByEmail($user->getEmail());

        if (!$fetchUser instanceof User) {
            $user->setStatus(User::PENDING);
            $user->setOrigin($this->siteAccess->name);
            $fetchUser = $user;
            $this->entityManager->persist($fetchUser);
            $this->entityManager->flush();
        }

        $token = $this->createConfirmationToken(
            ConfirmationToken::REGISTER,
            $fetchUser,
            $registration->getMailingLists()
        );
        $this->mailer->sendRegistrationConfirmation($registration, $token);
    }

    /**
     * @param Unregistration $unregistration
     */
    public function askForUnregisterConfirmation(Unregistration $unregistration): bool
    {
        $user = $unregistration->getUser();
        if (null === $user) {
            throw new RuntimeException('User cannot be empty.');
        }
        $userRepo  = $this->entityManager->getRepository(User::class);
        $fetchUser = $userRepo->findOneByEmail($user->getEmail());

        if (!$fetchUser instanceof User) {
            return false;
        }

        $token = $this->createConfirmationToken(
            ConfirmationToken::UNREGISTER,
            $fetchUser,
            $unregistration->getMailingLists()
        );

        $this->mailer->sendUnregistrationConfirmation($unregistration, $token);

        return true;
    }

    /**
     * @param string          $action
     * @param User            $user
     * @param ArrayCollection $mailingLists
     *
     * @return ConfirmationToken
     */
    private function createConfirmationToken(
        string $action,
        User $user,
        ArrayCollection $mailingLists
    ): ConfirmationToken {
        /** @var ArrayCollection $mailingListIds */
        $mailingListIds = $mailingLists->map(
            function (MailingList $mailingList) {
                return $mailingList->getId();
            }
        );

        $confirmationToken = new ConfirmationToken();
        $confirmationToken->setPayload(
            [
                'action'         => $action,
                'userId'         => $user->getId(),
                'mailingListIds' => $mailingListIds->toArray(),
            ]
        );
        $this->entityManager->persist($confirmationToken);
        $this->entityManager->flush();

        return $confirmationToken;
    }

    /**
     * @param ConfirmationToken $token
     */
    public function confirm(ConfirmationToken $token): bool
    {
        $created = Carbon::instance($token->getCreated());
        $expired = Carbon::now()->subHours(static::TOKEN_EXPIRATION_HOURS);
        if ($created->lessThan($expired)) {
            return false;
        }

        ['action' => $action, 'userId' => $userId, 'mailingListIds' => $mailingListIds] = $token->getPayload();
        if (!\in_array($action, [ConfirmationToken::REGISTER, ConfirmationToken::UNREGISTER])) {
            return false;
        }
        $mailingListRepo = $this->entityManager->getRepository(MailingList::class);
        $userRepo        = $this->entityManager->getRepository(User::class);
        $user            = $userRepo->findOneById($userId);
        if (!$user instanceof User) {
            return false;
        }

        foreach ($mailingListIds as $id) {
            $mailingList = $mailingListRepo->findOneById($id);
            if (!$mailingList instanceof MailingList) {
                continue;
            }

            if (ConfirmationToken::REGISTER == $action) {
                $registration = new RegistrationEntity();
                $registration->setApproved(!$mailingList->isWithApproval());
                $registration->setMailingList($mailingList);
                $user->addRegistration($registration);
            }

            if (ConfirmationToken::UNREGISTER == $action) {
                $currentRegistrations = $user->getRegistrations();
                foreach ($currentRegistrations as $registration) {
                    if ($registration->getMailingList()->getId() === $id) {
                        $user->removeRegistration($registration);
                    }
                }
            }
        }

        // in any case we can confirm the email here
        if ($user->isPending()) {
            $user->setStatus(User::CONFIRMED);
        }

        // if no more registration then we remove the user
        if (0 == $user->getRegistrations()->count()) {
            $this->entityManager->remove($user);
        }

        $this->entityManager->remove($token);
        $this->entityManager->flush();

        return true;
    }

    /**
     * Clean the ConfirmationToken expired records.
     */
    public function cleanup(): void
    {
        $repo     = $this->entityManager->getRepository(ConfirmationToken::class);
        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->lt('created', Carbon::now()->subHours(static::TOKEN_EXPIRATION_HOURS)));
        $results = $repo->matching($criteria);

        foreach ($results as $result) {
            $this->entityManager->remove($result);
        }
        $this->entityManager->flush();
    }

    public function getDefaultMailingList(): ArrayCollection
    {
        $mailingListId = null;
        if ($this->configResolver->hasParameter('default_mailinglist_id', 'nova_ezmailing')) {
            $mailingListId = $this->configResolver->getParameter('default_mailinglist_id', 'nova_ezmailing');
        }
        $mailingList = $this->entityManager->getRepository(MailingList::class)->findOneBy(
            ['id' => $mailingListId]
        );

        return new ArrayCollection([$mailingList]);
    }
}
