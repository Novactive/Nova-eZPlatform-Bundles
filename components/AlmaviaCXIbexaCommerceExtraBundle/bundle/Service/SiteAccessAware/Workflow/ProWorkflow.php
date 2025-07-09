<?php

namespace AlmaviaCX\Ibexa\Commerce\Extra\Service\SiteAccessAware\Workflow;

use AlmaviaCX\Ibexa\Commerce\Extra\Service\SiteAccessAware\Traits\ConfigResolverTrait;
use Ibexa\Checkout\Value\Workflow\Workflow;
use Ibexa\Contracts\Cart\Value\CartInterface;
use Ibexa\Contracts\Checkout\Value\Workflow\WorkflowInterface;
use Ibexa\Contracts\Checkout\Workflow\WorkflowStrategyInterface;

final class ProWorkflow implements WorkflowStrategyInterface
{
    use ConfigResolverTrait;
    public const CUSTOM_WORKFLOW_NAME = 'named_workflow_name';
    public function getWorkflow(CartInterface $cart): WorkflowInterface
    {
        $customValue = $this->getConfigParameter(self::CUSTOM_WORKFLOW_NAME);
        return new Workflow($customValue);
    }

    public function supports(CartInterface $cart): bool
    {
        $customValue = $this->getConfigParameter(self::CUSTOM_WORKFLOW_NAME);
        if (empty($customValue)) {
            // null value means no custom workflow, using other strategy
            return false;
        }
        return true;
    }
}
