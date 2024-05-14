<?php

namespace AlmaviaCX\Ibexa\Bundle\FieldTypes\Service;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;

class SelectionService implements SelectionInterface
{
    public function __construct(protected readonly ConfigResolverInterface $configResolver)
    {
    }

    public function getChoices(?string $choiceEntry): array
    {
        if (empty($choiceEntry)) {
            $choiceEntry = 'default';
        }
        return (array) ($this->configResolver->getParameter('acx_selection', 'acx_field_types')[$choiceEntry]?? []);
    }
}