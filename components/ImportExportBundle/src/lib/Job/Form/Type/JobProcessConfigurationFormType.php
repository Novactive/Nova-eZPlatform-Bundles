<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Job\Form\Type;

use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentRegistry;
use AlmaviaCX\Bundle\IbexaImportExport\Job\Job;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\Form\Type\WorkflowProcessConfigurationFormType;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobProcessConfigurationFormType extends AbstractType
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
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $form = $event->getForm();
            /* @var \AlmaviaCX\Bundle\IbexaImportExport\Job\Job $job */
            $job = $event->getData();
            if (!$job) {
                return;
            }
            $workflowIdentifier = $job->getWorkflowIdentifier();
            $workflowDefaultConfiguration = $this->workflowRegistry::getWorkflowDefaultConfiguration(
                $this->workflowRegistry->getWorkflowClassName($workflowIdentifier)
            );

            $optionsForm = $form->add('options', WorkflowProcessConfigurationFormType::class, [
                'label' => /* @Desc("Workflow options") */ 'workflow.options',
                'required' => false,
                'show_initialized' => $options['show_initialized'],
                'default_configuration' => $workflowDefaultConfiguration->getProcessConfiguration(),
            ]);
        });

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            if ($event->getForm()->has('options')) {
                $optionsForm = $event->getForm()->get('options');
                $this->removeEmptyChildren($optionsForm);
            }
        });
    }

    protected function removeEmptyChildren(FormInterface $form)
    {
        foreach ($form->all() as $child) {
            if ($child->getConfig()->getType()->getInnerType() instanceof FormType) {
                $this->removeEmptyChildren($child);
            }
            if (empty($child->all())) {
                $form->remove($child->getName());
            }
        }
    }

    /**
     * @param string|callable $componentClassName
     */
    protected function getComponentOptionsFormType($componentClassName): ?string
    {
        return $this->componentRegistry::getComponentOptionsFormType($componentClassName);
    }

    /**
     * @param string|callable $componentClassName
     *
     * @return string|\Symfony\Component\Translation\TranslatableMessage|null
     */
    protected function getComponentName($componentClassName)
    {
        return $this->componentRegistry::getComponentName($componentClassName);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => Job::class,
                                   'show_initialized' => false,
                                   'translation_domain' => 'forms',
                                   'workflow_configuration' => null,
                               ]);
    }
}
