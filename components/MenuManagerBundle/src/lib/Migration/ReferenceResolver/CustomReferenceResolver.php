<?php

/**
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

namespace Novactive\EzMenuManager\Migration\ReferenceResolver;

use Doctrine\ORM\EntityManagerInterface;
use eZ\Publish\API\Repository\LocationService;
use Kaliop\eZMigrationBundle\Core\ReferenceResolver\AbstractResolver;
use Novactive\EzMenuManagerBundle\Entity\MenuItem\ContentMenuItem;

class CustomReferenceResolver extends AbstractResolver
{
    /**
     * Defines the prefix for all reference identifier strings in definitions.
     */
    protected $referencePrefixes = ['menu_items:'];

    /** @var EntityManagerInterface */
    protected $em;

    /** @var LocationService */
    protected $locationService;

    /**
     * CustomReferenceResolver constructor.
     */
    public function __construct(EntityManagerInterface $em, LocationService $locationService)
    {
        parent::__construct();
        $this->em = $em;
        $this->locationService = $locationService;
    }

    /**
     * @param string $stringIdentifier format: menu_items:<location_remote_id>:<value>
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     *
     * @return array|ContentMenuItem[]
     */
    public function getReferenceValue($stringIdentifier)
    {
        $prefixMatchRegexp = '/^(menu_items):(location_remote_id):(.*)$/';
        $matches = null;
        preg_match($prefixMatchRegexp, $stringIdentifier, $matches);
        list(, , $refType, $value) = $matches;
        switch ($refType) {
            case 'location_remote_id':
                $location = $this->locationService->loadLocationByRemoteId($value);

                return $this->em->getRepository(ContentMenuItem::class)->findBy(
                    [
                    'url' => ContentMenuItem::URL_PREFIX.$location->contentId,
                    ]
                );
        }

        return [];
    }
}
