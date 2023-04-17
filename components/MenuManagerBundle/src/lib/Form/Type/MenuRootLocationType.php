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

namespace Novactive\EzMenuManager\Form\Type;

use Ibexa\Contracts\Core\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class MenuRootLocationType extends AbstractType
{
    /** @var LocationService */
    protected $locationService;

    /** @var ContentService */
    protected $contentService;

    /**
     * MenuEditType constructor.
     */
    public function __construct(LocationService $locationService, ContentService $contentService)
    {
        $this->locationService = $locationService;
        $this->contentService = $contentService;
    }

    public function getParent()
    {
        return IntegerType::class;
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['rootLocation'] = null;
        try {
            /** @var int $rootLocationId */
            $rootLocationId = $form->getData();
            if ($rootLocationId) {
                $location = $this->locationService->loadLocation($rootLocationId);
                $contentInfo = $this->contentService->loadContentInfo($location->contentId);

                $view->vars['rootLocation'] = [
                    'location' => $location,
                    'contentInfo' => $contentInfo,
                ];
            }
        } catch (NotFoundException $e) {
            $view->vars['rootLocation'] = null;
        }
    }
}
