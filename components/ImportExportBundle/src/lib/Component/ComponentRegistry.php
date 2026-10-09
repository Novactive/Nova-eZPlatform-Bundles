<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Component;

use Psr\Container\ContainerInterface;
use ReflectionClass;

class ComponentRegistry
{
    public function __construct(
        protected ContainerInterface $typeContainer
    ) {
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     * @return ComponentInterface<ComponentOptions>
     */
    public function getComponent(string $type): ComponentInterface
    {
        return $this->typeContainer->get($type);
    }

    /**
     * @param class-string<ComponentInterface<ComponentOptions>> $componentClassName
     */
    public static function getComponentOptionsFormType(string $componentClassName): ?string
    {
        try {
            /** @var ReflectionClass<ComponentInterface<ComponentOptions>> $componentClass */
            $componentClass = new ReflectionClass($componentClassName);

            return $componentClass->getMethod('getOptionsFormType')->invoke(null);
        } catch (\ReflectionException $e) {
            return null;
        }
    }

    /**
     * @param class-string<ComponentInterface<ComponentOptions>> $componentClassName
     *
     * @return string|\Symfony\Component\Translation\TranslatableMessage|null
     */
    public static function getComponentName(string $componentClassName)
    {
        try {
            /** @var ReflectionClass<ComponentInterface<ComponentOptions>> $componentClass */
            $componentClass = new ReflectionClass($componentClassName);

            return $componentClass->getMethod('getName')->invoke(null);
        } catch (\ReflectionException $e) {
            return null;
        }
    }
}
