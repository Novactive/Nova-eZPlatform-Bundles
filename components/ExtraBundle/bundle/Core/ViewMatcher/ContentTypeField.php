<?php

/**
 * NovaeZExtraBundle ViewMatcher ContentTypeField.
 *
 * @package   Novactive\Bundle\eZExtraBundle
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZExtraBundle\Core\ViewMatcher;

use Ibexa\Core\MVC\RepositoryAware;
use Ibexa\Core\MVC\Symfony\Matcher\ViewMatcherInterface;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Ibexa\Core\MVC\Symfony\View\View;

final class ContentTypeField extends RepositoryAware implements ViewMatcherInterface
{
    /**
     * @var string
     */
    private $fieldIdentifier;

    /**
     * @var string
     */
    private $attribute;

    public function setMatchingConfig($matchingConfig): void
    {
        $this->attribute = $matchingConfig;
    }

    public function __construct(string $fieldIdentifier)
    {
        $this->fieldIdentifier = $fieldIdentifier;
    }

    public function match(View $view): bool
    {
        if (!$view instanceof ContentView) {
            return false;
        }
        $content = $view->getContent();

        $field = $content->getField($this->fieldIdentifier);
        if (null === $field) {
            return false;
        }
        $options = $content->getContentType()->getFieldDefinition($field->fieldDefIdentifier);
        if (null === $options) {
            return false;
        }
        $list = $options->getFieldSettings();
        $index = $content->getFieldValue($field->fieldDefIdentifier)->selection;
        $value = null;
        if (isset($index[0], $list['options'][$index[0]])) {
            $value = $list['options'][$index[0]];
        }

        return $value === $this->attribute;
    }
}
