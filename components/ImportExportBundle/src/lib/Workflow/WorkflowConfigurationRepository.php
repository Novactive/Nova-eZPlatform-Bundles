<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Workflow;

use Doctrine\ORM\EntityManagerInterface;

class WorkflowConfigurationRepository
{
    public function __construct(
        protected EntityManagerInterface $entityManager
    ) {
    }

    public function find(string $identifier): ?WorkflowConfiguration
    {
        return $this->entityManager->getRepository(WorkflowConfiguration::class)->findOneBy(
            ['identifier' => $identifier]
        );
    }

    public function save(WorkflowConfiguration $configuration): void
    {
        $this->entityManager->persist($configuration);
        $this->entityManager->flush();
    }

    /**
     * @return array<string, string>
     */
    public function getAll(): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('wc.identifier, wc.name')
           ->from(WorkflowConfiguration::class, 'wc');

        $workflows = [];
        foreach ($qb->getQuery()->getArrayResult() as $item) {
            dd($item);
        }

        return $workflows;
    }
}
