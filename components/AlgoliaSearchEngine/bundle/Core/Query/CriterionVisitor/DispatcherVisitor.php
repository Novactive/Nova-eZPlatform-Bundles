<?php

/**
 * Nova eZ Algolia Search Engine.
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @licence   "SEE FULL LICENSE OPTIONS IN LICENSE.md"
 *            Nova eZ Algolia Search Engine is tri-licensed, meaning you must choose one of three licenses to use:
 *                - Commercial License: a paid license, meant for commercial use. The default option for most users.
 *                - Creative Commons Non-Commercial No-Derivatives: meant for trial and non-commercial use.
 *                - GPLv3 License: meant for open-source projects.
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\CriterionVisitor;

use eZ\Publish\API\Repository\Exceptions\NotImplementedException;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

final class DispatcherVisitor implements CriterionVisitor
{
    /**
     * @var iterable
     */
    private $visitors;

    public function __construct(iterable $visitors = [])
    {
        $this->visitors = $visitors;
    }

    public function supports(Criterion $criterion): bool
    {
        return null !== $this->findVisitor($criterion);
    }

    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, string $additionalOperators = ''): string
    {
        $visitor = $this->findVisitor($criterion);
        if (null === $visitor) {
            throw new NotImplementedException(
                'No visitor available for: '.\get_class($criterion).' with operator '.$criterion->operator
            );
        }

        return $visitor->visit($dispatcher, $criterion, $additionalOperators);
    }

    private function findVisitor(Criterion $criterion): ?CriterionVisitor
    {
        foreach ($this->visitors as $visitor) {
            if ($visitor->supports($criterion)) {
                return $visitor;
            }
        }

        return null;
    }
}
