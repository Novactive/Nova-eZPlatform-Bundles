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

use Carbon\Carbon;
use DateTime;
use LogicException;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing;

class Clock
{
    /**
     * @var Carbon
     */
    private $time;

    public function __construct(DateTime $time)
    {
        $this->time = Carbon::instance($time);
    }

    public function getHourOfDay(): int
    {
        return $this->time->hour;
    }

    public function getDayOfWeek(): int
    {
        return $this->time->dayOfWeekIso;
    }

    public function getDayOfMonth(): int
    {
        return $this->time->day;
    }

    public function getDayOfYear(): int
    {
        return $this->time->dayOfYear;
    }

    public function getWeekOfMonth(): int
    {
        return $this->time->weekOfMonth;
    }

    public function getMonthOfYear(): int
    {
        return $this->time->month;
    }

    public function getWeekOfYear(): int
    {
        return $this->time->weekOfYear;
    }

    public function match(Mailing $mailing): bool
    {
        // everything must match unless it is * which is a wilcard
        $testMethods = [
            'getHourOfDay' => 'getHoursOfDay',
            'getDayOfWeek' => 'getDaysOfWeek',
            'getDayOfMonth' => 'getDaysOfMonth',
            'getDayOfYear' => 'getDaysOfYear',
            'getWeekOfMonth' => 'getWeeksOfMonth',
            'getMonthOfYear' => 'getMonthsOfYear',
            'getWeekOfYear' => 'getWeeksOfYear',
        ];

        foreach ($testMethods as $testMethodClock => $testMethodMailing) {
            $possibilities = $mailing->$testMethodMailing();
            $countPossibilities = count($possibilities);
            if (
                0 === $countPossibilities ||
                (1 === $countPossibilities && '' === $possibilities[0])
            ) { // which means nothing then *
                continue;
            }
            if (!\in_array($this->$testMethodClock(), $possibilities)) {
                return false;
            }
        }

        return true;
    }

    public function nextTick(Mailing $mailing): DateTime
    {
        // Not sure that is great but it is a loop of 365 max, then might be the simplest and the best perf
        $now = $this->time;
        $tick = clone $now;
        $hours = $mailing->getHoursOfDay();

        for ($i = 0; $i < 365; ++$i) {
            $testClock = new static($tick);
            if (!$testClock->match($mailing)) {
                // set the first hours
                $tick->setTime((int) $hours[0], 0, 0);
                $testClock = new static($tick);
            }

            if ($testClock->match($mailing)) {
                if ($tick->timestamp == $now->timestamp) {
                    foreach ($hours as $hour) {
                        if ($hour > $now->hour) {
                            $tick->setTime((int) $hour, 0, 0);

                            return $tick;
                        }
                    }
                    $tick->addDay();
                    continue;
                }
                $tick->setTime((int) $hours[0], 0, 0);

                return $tick;
            }
            $tick->addDay();
        }
        throw new LogicException("There is not next tick for Mailing {$mailing->getName()}");
    }
}
