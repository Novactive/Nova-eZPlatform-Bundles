<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Component;

class ComponentBuilder
{
    protected ComponentRegistry $componentRegistry;

    /**
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentRegistry $componentRegistry
     */
    public function __construct(ComponentRegistry $componentRegistry)
    {
        $this->componentRegistry = $componentRegistry;
    }

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
            $options->replaceComponentReferences($this, $runtimeProcessConfiguration);
            $component->setOptions(
                $options
            );
        }

        return $component;
    }
}
