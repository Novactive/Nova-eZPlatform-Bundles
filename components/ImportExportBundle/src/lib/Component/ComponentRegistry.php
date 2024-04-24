<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Component;

use Psr\Container\ContainerInterface;
use ReflectionClass;

class ComponentRegistry
{
    protected ContainerInterface $typeContainer;

    public function __construct(ContainerInterface $typeContainer)
    {
        $this->typeContainer = $typeContainer;
    }

    public function getComponent(string $type): ComponentInterface
    {
        return $this->typeContainer->get($type);
    }

    public static function getComponentOptionsFormType(string $componentClassName): ?string
    {
        try {
            /** @var ReflectionClass<\AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentInterface> $componentClass */
            $componentClass = new ReflectionClass($componentClassName);

            return $componentClass->getMethod('getOptionsFormType')->invoke(null);
        } catch (\ReflectionException $e) {
            return null;
        }
    }

    /**
     * @return string|\Symfony\Component\Translation\TranslatableMessage|null
     */
    public static function getComponentName(string $componentClassName)
    {
        try {
            /** @var ReflectionClass<\AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentInterface> $componentClass */
            $componentClass = new ReflectionClass($componentClassName);

            return $componentClass->getMethod('getName')->invoke(null);
        } catch (\ReflectionException $e) {
            return null;
        }
    }
}
