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
use Novactive\Bundle\eZMailingBundle\Entity\ConfirmationToken;
use Novactive\Bundle\eZMailingBundle\Entity\MailingList;
use Novactive\Bundle\eZMailingBundle\Entity\Registration as RegistrationEntity;
use Novactive\Bundle\eZMailingBundle\Entity\User;
use RuntimeException;

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
     * Registrar constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, SiteAccess $siteAccess)
    {
        $this->entityManager = $entityManager;
        $this->siteAccess    = $siteAccess;
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

        $this->createConfirmationToken(ConfirmationToken::REGISTER, $fetchUser, $registration->getMailingLists());

        //@todo: send the email to get confirmation
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

        $this->createConfirmationToken(ConfirmationToken::UNREGISTER, $fetchUser, $unregistration->getMailingLists());

        //@todo: send the email to get confirmation

        return true;
    }

    /**
     * @param string          $action
     * @param User            $user
     * @param ArrayCollection $mailingLists
     */
    private function createConfirmationToken(string $action, User $user, ArrayCollection $mailingLists): void
    {
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
        if ($user->getRegistrations()->count() == 0) {
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
}
