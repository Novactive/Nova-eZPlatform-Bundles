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

namespace Novactive\Bundle\eZMailingBundle\Security\Voter;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use LogicException;
use Novactive\Bundle\eZMailingBundle\Entity\Campaign as CampaignEntity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class Campaign extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var SiteAccess
     */
    private $siteAccess;

    public function __construct(Repository $repository, SiteAccess $siteAccess)
    {
        $this->repository = $repository;
        $this->siteAccess = $siteAccess;
    }

    protected function supports($attribute, $subject): bool
    {
        if (!\in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        if (null !== $subject && !$subject instanceof CampaignEntity) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        /* @var CampaignEntity $subject */

        // all create
        if (null === $subject) {
            return true;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($subject, $user);
            case self::EDIT:
                return $this->canEdit($subject, $user);
        }

        throw new LogicException('This code should not be reached!');
    }

    private function canView(CampaignEntity $subject, $user): bool
    {
        $siteaccessLimist = $subject->getSiteaccessLimit();
        // if no limit then we vote OK
        if (null === $siteaccessLimist || 0 === count($siteaccessLimist)) {
            return true;
        }

        if (\in_array($this->siteAccess->name, $siteaccessLimist)) {
            return true;
        }

        // @improvment: maybe we should add a module/function for that specific purpose
        return $this->repository->getPermissionResolver()->hasAccess('setup', 'administrate');
    }

    private function canEdit(CampaignEntity $subject, $user): bool
    {
        return $this->canView($subject, $user);
    }
}
