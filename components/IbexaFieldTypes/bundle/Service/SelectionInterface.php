<?php

namespace AlmaviaCX\Ibexa\Bundle\FieldTypes\Service;

interface SelectionInterface
{
    public function getChoices(?string $choiceEntry): array;
}