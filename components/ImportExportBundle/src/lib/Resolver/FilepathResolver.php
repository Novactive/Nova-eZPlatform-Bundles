<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Resolver;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class FilepathResolver
{
    protected ParameterBag $parameterBag;

    public function __construct(ParameterBag $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function __invoke(string $filepath): string
    {
        $tokens = $this->buildTokens();

        return str_replace(
            array_keys($tokens),
            array_values($tokens),
            $this->parameterBag->resolveString($filepath)
        );
    }

    public function buildTokens(): array
    {
        $datetime = new \DateTimeImmutable();

        return [
            '{date}' => $datetime->format('Y-m-d'),
            '{datetime}' => $datetime->format('Y-m-d-H-i-s'),
        ];
    }
}
