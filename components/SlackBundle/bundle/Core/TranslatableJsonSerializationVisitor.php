<?php

/**
 * NovaeZSlackBundle Bundle.
 *
 * @package   Novactive\Bundle\eZSlackBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZSlackBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZSlackBundle\Core;

use Symfony\Component\Translation\TranslatorInterface;

class TranslatableJsonSerializationVisitor
{
    private TranslatorInterface $translator;

    public function visitString($data, array $type): string
    {
        if ('string' !== $type['name']) {
            return $data;
        }

        if (0 === \count($type['params'])) {
            return $data;
        }

        foreach ($type['params'] as $param) {
            if (('translatable' === $param['name']) && 0 === strpos($data, '_t:')) {
                $data = $this->translator->trans(substr($data, 3), [], 'slack');
            }
        }

        return $data;
    }

    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }
}
