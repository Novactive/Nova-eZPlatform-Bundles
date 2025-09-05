<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\AdminUi\Menu;

use AlmaviaCX\Bundle\IbexaImportExport\AdminUi\Menu\Event\ConfigureMenuEvent;
use Craue\FormFlowBundle\Form\FormFlow;
use Ibexa\Contracts\AdminUi\Menu\AbstractBuilder;
use Ibexa\Contracts\Core\Repository\Exceptions as ApiExceptions;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;

class JobCreateRightSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    /* Menu items */
    public const ITEM__FINISH = 'job_create__sidebar_right__finish';
    public const ITEM__NEXT = 'job_create__sidebar_right__next';
    public const ITEM__BACK = 'job_create__sidebar_right__back';
    public const ITEM__RESET = 'job_create__sidebar_right__reset';
    public const ITEM__CANCEL = 'job_create__sidebar_right__cancel';

    protected function getConfigureEventName(): string
    {
        return ConfigureMenuEvent::JOB_CREATE_SIDEBAR_RIGHT;
    }

    /**
     * @throws \InvalidArgumentException
     * @throws ApiExceptions\BadStateException
     * @throws \InvalidArgumentException
     */
    public function createStructure(array $options): ItemInterface
    {
        /** @var ItemInterface|\Knp\Menu\ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');

        $childrens = [];
        /** @var FormFlow $flow */
        $flow = $options['flow'];
        $renderBackButton = $flow->getFirstStepNumber() < $flow->getLastStepNumber()
                            && in_array(
                                $flow->getCurrentStepNumber(),
                                range($flow->getFirstStepNumber() + 1, $flow->getLastStepNumber())
                            );
        $renderResetButton = $options['render_reset'] ?? true;
        $isLastStep = $flow->getCurrentStepNumber() == $flow->getLastStepNumber();

        $buttons = [
            [
                'id' => self::ITEM__FINISH,
                'label' => self::ITEM__FINISH,
                'render' => $isLastStep,
                'attributes' => [
                    'class' => 'ibexa-btn--trigger',
                    'data-click' => '#form_flow_next',
                ],
            ],
            [
                'id' => self::ITEM__NEXT,
                'label' => self::ITEM__NEXT,
                'render' => !$isLastStep,
                'attributes' => [
                    'class' => 'ibexa-btn--trigger',
                    'data-click' => '#form_flow_next',
                ],
            ],
            [
                'id' => self::ITEM__BACK,
                'label' => self::ITEM__BACK,
                'render' => $renderBackButton,
                'attributes' => [
                    'class' => 'ibexa-btn--trigger',
                    'data-click' => '#form_flow_back',
                ],
            ],
            [
                'id' => self::ITEM__RESET,
                'label' => self::ITEM__RESET,
                'render' => $renderResetButton,
                'attributes' => [
                    'class' => 'ibexa-btn--trigger',
                    'data-click' => '#form_flow_reset',
                ],
            ],
        ];

        foreach ($buttons as $button) {
            if ($button['render']) {
                $childrens[$button['id']] = $this->createMenuItem(
                    $button['id'],
                    [
                        'attributes' => $button['attributes'],
                    ]
                );
            }
        }

        $childrens[self::ITEM__CANCEL] = $this->createMenuItem(
            self::ITEM__CANCEL,
            [
                'route' => 'import_export.job.list',
            ]
        );
        $menu->setChildren($childrens);

        return $menu;
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__FINISH, 'ibexa_menu'))->setDesc('Create'),
            (new Message(self::ITEM__NEXT, 'ibexa_menu'))->setDesc('Next'),
            (new Message(self::ITEM__BACK, 'ibexa_menu'))->setDesc('Back'),
            (new Message(self::ITEM__RESET, 'ibexa_menu'))->setDesc('Reset'),
            (new Message(self::ITEM__CANCEL, 'ibexa_menu'))->setDesc('Discard changes'),
        ];
    }
}
