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

namespace Novactive\Bundle\eZMailingBundle\Tests\Tests\Unit;

use Carbon\Carbon;
use Novactive\Bundle\eZMailingBundle\Core\Utils\Clock;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing;
use PHPUnit\Framework\TestCase;

class ClockTest extends TestCase
{
    /**
     * $hoursOfDay $daysOfWeek $daysOfMonth $daysOfYear $weeksOfMonth $monthsOfYear $weeksOfYear.
     */
    public function timesMatchProvider(): array
    {
        return [
            // TRUE CASES
            [
                new Carbon('first day of December 2008'),
                [
                    ['', '', '', '', '', '', ''],
                    ['', '', '', '', '1', '', ''],
                    ['', '', '', '', '1', '12', ''],
                    ['', '', '', '', '1,5', '', ''],
                    ['', '', '', '', '1,5', '10,12', ''],
                ],
                true,
            ],
            [
                Carbon::create(2018, 3, 12, 15, 8, 55),
                [
                    ['', '', '', '', '', '', ''],
                    ['', '', '12', '', '', '', ''],
                    ['15', '', '12', '', '', '', ''],
                    ['15', '', '', '', '', '', ''],
                    ['15', '', '', '', '', '3', ''],
                ],
                true,
            ],
            [
                Carbon::create(2018, 12, 30, 2, 8, 55),
                [
                    ['', '', '', '', '', '', ''],
                    ['', '', '30', '', '', '', ''],
                    ['2,15', '', '30', '', '', '', ''],
                    ['2,15', '', '', '', '', '', ''],
                    ['2,15', '', '', '', '', '12', ''],
                    ['2,15', '', '30', '', '', '12', '52'],
                    ['2,15', '7', '30', '', '', '12', '52'],
                    ['2,15', '7', '30', '', '5', '12', '52'],
                    ['2,15', '7', '30', '364', '5', '12', '52'],
                ],
                true,
            ],
            // annee bissextile
            [
                Carbon::create(2020, 12, 30, 2, 8, 55),
                [
                    ['', '', '', '', '', '', ''],
                    ['', '', '30', '', '', '', ''],
                    ['2,15', '', '30', '', '', '', ''],
                    ['2,15', '', '', '', '', '', ''],
                    ['2,15', '', '', '', '', '12', ''],
                    ['2,15', '', '30', '', '', '12', '53'],
                    ['2,15', '3', '30', '', '', '12', '53'],
                    ['2,15', '3', '30', '', '5', '12', '53'],
                    ['2,15', '3', '30', '365', '5', '12', '53'],
                ],
                true,
            ],
            // FALSE CASES
            [
                Carbon::create(2018, 12, 30, 2, 8, 55),
                [
                    ['1', '', '', '', '', '', ''],
                    ['', '1', '30', '', '', '', ''],
                    ['2,15', '', '30', '', '', '1', ''],
                    ['2,15', '', '', '', '', '1', ''],
                    ['2,15', '', '', '', '1', '12', ''],
                    ['2,15', '1', '30', '', '', '12', '52'],
                    ['2,15', '7', '31', '', '', '12', '52'],
                    ['2,15', '1', '30', '', '5', '12', '52'],
                    ['2,15', '7', '30', '364', '1', '12', '52'],
                ],
                false,
            ],
        ];
    }

    /**
     * $hoursOfDay $daysOfWeek $daysOfMonth $daysOfYear $weeksOfMonth $monthsOfYear $weeksOfYear.
     */
    public function nextTimesMatchProvider(): array
    {
        return [
            [
                Carbon::create(2018, 12, 30, 2, 8, 55),
                ['2', '', '', '', '', '', ''],
                Carbon::create(2018, 12, 31, 2, 0, 0),
            ],
            [
                Carbon::create(2018, 5, 15, 14, 8, 55),
                ['14,16', '', '', '', '', '', ''],
                Carbon::create(2018, 5, 15, 16, 0, 0),
            ],
            [
                Carbon::create(2018, 5, 15, 14, 8, 55),
                ['14,16', '4', '', '', '', '', ''],
                Carbon::create(2018, 5, 17, 14, 0, 0),
            ],
            [
                Carbon::create(2018, 5, 15, 14, 8, 55),
                ['14,16', '4', '', '', '5', '', ''],
                Carbon::create(2018, 5, 31, 14, 0, 0),
            ],
            [
                Carbon::create(2018, 5, 15, 14, 8, 55),
                ['14', '4', '', '', '', '6', ''],
                Carbon::create(2018, 6, 7, 14, 0, 0),
            ],

            [
                Carbon::create(2018, 5, 15, 14, 8, 55),
                ['7', '4', '', '', '', '6', ''],
                Carbon::create(2018, 6, 7, 7, 0, 0),
            ],
            [
                Carbon::create(2018, 5, 15, 14, 8, 55),
                ['5,7', '4', '', '', '', '6', ''],
                Carbon::create(2018, 6, 7, 5, 0, 0),
            ],

            [
                Carbon::create(2018, 5, 15, 14, 8, 55),
                ['18', '3', '', '', '', '', '21'],
                Carbon::create(2018, 5, 23, 18, 0, 0),
            ],
            [
                Carbon::create(2019, 5, 15, 14, 8, 55),
                ['18', '3', '', '', '', '', '1'],
                Carbon::create(2020, 1, 1, 18, 0, 0),
            ],
            [
                Carbon::create(2018, 9, 20, 20, 45, 0),
                ['12', '', '', '18', '', '', ''],
                Carbon::create(2019, 1, 18, 12, 0, 0),
            ],
        ];
    }

    private function createMailing(array $data): Mailing
    {
        $mailing = new Mailing();

        list($hoursOfDay, $daysOfWeek, $daysOfMonth, $daysOfYear, $weeksOfMonth, $monthsOfYear, $weeksOfYear) = $data;

        $mailing->setNames(['eng-GB' => 'Test Mailing']);
        $mailing
            ->setHoursOfDay(explode(',', $hoursOfDay))
            ->setDaysOfWeek(explode(',', $daysOfWeek))
            ->setDaysOfMonth(explode(',', $daysOfMonth))
            ->setDaysOfYear(explode(',', $daysOfYear))
            ->setWeeksOfMonth(explode(',', $weeksOfMonth))
            ->setMonthsOfYear(explode(',', $monthsOfYear))
            ->setWeeksOfYear(explode(',', $weeksOfYear));

        return $mailing;
    }

    /**
     * @dataProvider timesMatchProvider
     */
    public function testMatchClock(Carbon $dateReference, array $datas, bool $expected): void
    {
        foreach ($datas as $data) {
            $dataForLog = array_map(
                function ($value) {
                    return '' === $value ? '*' : $value;
                },
                $data
            );




            $clock = new Clock($dateReference);
            $mailing = $this->createMailing($data);

            if ($expected !== $clock->match($mailing)) {
                dump($dateReference->format('Y-m-d H:i:s'));
                dump($dataForLog);
                dump($expected);
                dump($mailing);
                dd($clock->match($mailing));
            }

            $this->assertEquals(
                $expected,
                $clock->match($mailing),
                implode(' ', $dataForLog)." does not match with {$dateReference->format('Y-m-d H:i:s')}"
            );
        }
    }

    /**
     * @dataProvider nextTimesMatchProvider
     */
    public function testNextTickClock(Carbon $dateReference, array $data, Carbon $expected): void
    {
        $clock = new Clock($dateReference);
        $mailing = $this->createMailing($data);
        $nextTick = $clock->nextTick($mailing);
        $dataForLog = array_map(
            function ($value) {
                return '' === $value ? '*' : $value;
            },
            $data
        );

        $this->assertEquals(
            $expected,
            $nextTick,
            "Next tick for {$dateReference->format('Y-m-d H:i:s')} using ".implode(' ', $dataForLog).
            " is not {$expected->format('Y-m-d H:i:s')}"
        );
    }
}
