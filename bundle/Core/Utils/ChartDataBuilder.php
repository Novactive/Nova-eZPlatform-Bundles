<?php
/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMailingBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Core\Utils;

/**
 * Class ChartDataBuilder.
 */
class ChartDataBuilder
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var array
     */
    private $options;

    /**
     * @var array
     */
    private $dataSets;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $colorsSets;

    /**
     * ChartDataBuilder constructor.
     *
     * @param string $title
     * @param array  $options
     * @param string $type
     */
    public function __construct(string $title, string $type, array $options = [])
    {
        $this->title      = $title;
        $this->type       = $type;
        $this->options    = $options;
        $this->dataSets   = [];
        $this->colorsSets = [
            '#ff6384', '#36a2eb', '#cc65fe', '#ffce56',
            // @todo: add more
        ];
    }

    /**
     * @param array      $data
     * @param array      $labels
     * @param array|null $colors
     *
     * @return ChartDataBuilder
     */
    public function addDataSet(array $data, array $labels, ?array $colors = null): self
    {
        $this->dataSets[] = [
            'data'   => $data,
            'labels' => $labels,
            'colors' => $colors,
        ];

        return $this;
    }

    /**
     * @return array
     */
    public function __invoke()
    {
        $datasets = [];
        $labels   = [];
        foreach ($this->dataSets as $dataSet) {
            $datasets[] = [
                'data'            => $dataSet['data'],
                'backgroundColor' => $dataSet['colors'] ?? $this->colorsSets,
            ];
            $labels     = $dataSet['labels'];
        }

        $options                     = $this->options;
        $options['title']['display'] = true;
        $options['title']['text']    = $this->title;

        return [
            'type'    => $this->type,
            'options' => $options,
            'data'    => [
                'datasets' => $datasets,
                'labels'   => $labels,
            ],
        ];
    }
}
