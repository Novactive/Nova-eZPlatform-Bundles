<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Translator\Generator;

use Ibexa\Solr\Query\Common\QueryTranslator\Generator\WordVisitor as BaseWordVisitor;
use QueryTranslator\Languages\Galach\Generators\Common\Visitor;
use QueryTranslator\Values\Node;

class WordVisitor extends BaseWordVisitor
{
    public function visit(Node $node, ?Visitor $subVisitor = null, $options = null): string
    {
        $word = parent::visit($node, $subVisitor, $options);
        if (isset($options['wildcard'])) {
            $word .= '*';
        }

        return $word;
    }
}
