<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Component;

class ComponentBuilder
{
    /**
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentRegistry $componentRegistry
     */
    public function __construct(
        protected ComponentRegistry $componentRegistry
    ) {
    }

    /**
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentReference    $componentReference
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentOptions|null $runtimeProcessConfiguration
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     * @return \AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentInterface<ComponentOptions>
     */
    public function __invoke(
        ComponentReference $componentReference,
        ?ComponentOptions $runtimeProcessConfiguration = null
    ): ComponentInterface {
        $component = $this->componentRegistry->getComponent($componentReference->getType());
        $options = $componentReference->getOptions();
        if ($options) {
            if ($runtimeProcessConfiguration) {
                $options->merge($runtimeProcessConfiguration);
            }
            $options->replaceComponentReferences(
                $this,
                $runtimeProcessConfiguration
            );
            $component->setOptions(
                $options
            );
        }

        return $component;
    }
}
