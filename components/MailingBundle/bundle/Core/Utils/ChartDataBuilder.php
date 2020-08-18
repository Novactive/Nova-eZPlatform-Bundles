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
            '#ff6384', '#36a2eb', '#cc65fe', '#ffce56', '#EC644B', '#DB0A5B', '#E26A6A', '#DCC6E0', '#663399',
            '#913D88', '#BF55EC', '#9B59B6', '#446CB3', '#59ABE3', '#19B5FE', '#A2DED0', '#66CC99', '#00B16A',
            '#F4B350',
        ];
    }

    /**
     * @param array       $data
     * @param array       $labels
     * @param array|null  $colors
     * @param string|null $type
     *
     * @return ChartDataBuilder
     */
    public function addDataSet(array $data, array $labels, ?array $colors = null, ?string $type = null): self
    {
        if (null === $type) {
            $this->dataSets[] = [
                'data'   => $data,
                'labels' => $labels,
                'colors' => $colors,
            ];

            return $this;
        }

        $this->dataSets[] = [
            'data'   => $data,
            'labels' => $labels,
            'colors' => $colors,
            'type'   => $type,
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
            $exportedDataset = [
                'data'            => $dataSet['data'],
                'backgroundColor' => $dataSet['colors'] ?? $this->colorsSets,
            ];
            if (isset($dataSet['type'])) {
                $exportedDataset['type']        = $dataSet['type'];
                $exportedDataset['fill']        = false;
                $exportedDataset['borderColor'] = $dataSet['colors'] ?? $this->colorsSets;
            }

            $datasets[] = $exportedDataset;
            $labels     = $dataSet['labels'];
        }

        $options                     = $this->options;
        $options['title']['display'] = true;
        $options['title']['text']    = $this->title;

        if ('bar' === $this->type) {
            $options['legend']          = false;
            $options['scales']['yAxes'] = [
                [
                    'ticks' => [
                        'stepSize'    => 1,
                        'beginAtZero' => true,
                    ],
                ],
            ];
            $options['scales']['xAxes'] = [
                [
                    'barThickness' => 3,
                ],
            ];
        }

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
