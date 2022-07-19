<?php

declare(strict_types=1);

namespace Novactive\StaticTemplates\Faker\Generators;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Novactive\StaticTemplates\Faker\FakerGeneratorTrait;
use Novactive\StaticTemplates\Faker\GeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class LinkGenerator implements GeneratorInterface
{
    use FakerGeneratorTrait;

    /**
     * @var \Knp\Menu\FactoryInterface
     */
    protected $factory;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface
     */
    protected $translator;

    public function __construct(FactoryInterface $factory, TranslatorInterface $translator)
    {
        $this->factory = $factory;
        $this->translator = $translator;
    }

    public function support(string $type): bool
    {
        return 'link' === $type || 'blank_link' === $type;
    }

    public function generate(string $type): ItemInterface
    {
        $faker = $this->getFaker();
        $name = $faker->words(3, true);
        $target = 'blank_link' === $type ? '_blank' : null;
        $options = [
            'uri' => $faker->url(),
            'linkAttributes' => [],
        ];
        if ($target) {
            $options['linkAttributes']['target'] = $target;
            $options['linkAttributes']['title'] = $this->translator->trans('link.blank', ['%title%' => $name]);
        }

        return $this->factory->createItem(
            $name,
            $options
        );
    }
}
