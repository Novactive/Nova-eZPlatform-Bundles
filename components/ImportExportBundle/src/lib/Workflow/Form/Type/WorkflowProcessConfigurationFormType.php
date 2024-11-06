<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Workflow\Form\Type;

use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentRegistry;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowProcessConfiguration;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkflowProcessConfigurationFormType extends AbstractType
{
    protected WorkflowRegistry $workflowRegistry;
    protected ComponentRegistry $componentRegistry;

    public function __construct(WorkflowRegistry $workflowRegistry, ComponentRegistry $componentRegistry)
    {
        $this->workflowRegistry = $workflowRegistry;
        $this->componentRegistry = $componentRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var WorkflowProcessConfiguration $defaultConfiguration */
        $defaultConfiguration = $options['default_configuration'];
        $showInitialized = $options['show_initialized'];

        $readerFormType = $this->getComponentOptionsFormType($defaultConfiguration->getReader()->getType());
        if ($readerFormType) {
            $readerForm = $builder->create('reader', $readerFormType, [
                'label' => $this->getComponentName($defaultConfiguration->getReader()->getType()),
                'show_initialized' => $showInitialized,
                'default_configuration' => $defaultConfiguration->getReader()->getOptions(),
            ]);
            $builder->add($readerForm);
        }

        $processorsForm = $builder->create('processors', FormType::class, [
            'label' => false,
        ]);
        foreach ($defaultConfiguration->getProcessors() as $index => $processorConfig) {
            $processorFormType = $this->getComponentOptionsFormType($processorConfig->getType());
            if ($processorFormType) {
                $processorForm = $processorsForm->create($index, $processorFormType, [
                    'label' => $this->getComponentName($processorConfig->getType()),
                    'show_initialized' => $showInitialized,
                    'default_configuration' => $processorConfig->getOptions(),
                ]);
                $processorsForm->add($processorForm);
            }
        }
        $builder->add($processorsForm);
    }

    protected function getComponentOptionsFormType(string $componentClassName): ?string
    {
        return $this->componentRegistry::getComponentOptionsFormType($componentClassName);
    }

    /**
     * @return string|\Symfony\Component\Translation\TranslatableMessage|null
     */
    protected function getComponentName(string $componentClassName)
    {
        return $this->componentRegistry::getComponentName($componentClassName);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
                                    'default_configuration' => null,
                                    'show_initialized' => false,
                                    'translation_domain' => 'forms',
                                ]);
    }
}
