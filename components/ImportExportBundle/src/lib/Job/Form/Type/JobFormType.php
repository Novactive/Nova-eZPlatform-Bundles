<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Job\Form\Type;

use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowRegistry;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobFormType extends AbstractType
{
    protected WorkflowRegistry $workflowRegistry;

    public function __construct(WorkflowRegistry $workflowRegistry)
    {
        $this->workflowRegistry = $workflowRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('label', TextType::class, [
            'label' => /* @Desc("Label") */ 'job.label',
        ]);

        $availableWorkflows = $this->workflowRegistry->getAvailableWorkflowServices();
        $builder->add('workflowIdentifier', ChoiceType::class, [
            'label' => /* @Desc("Workflow") */ 'job.workflowIdentifier',
            'choices' => array_flip($availableWorkflows),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
                                    'translation_domain' => 'forms',
                                ]);
    }
}
