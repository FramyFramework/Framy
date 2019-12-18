<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\StdLib\StdObject\DateTimeObject;

use app\framework\Component\StdLib\StdObject\StringObject\StringObject;

/**
 * DateTimeObject manipulator trait.
 *
 * @package app\framework\Component\StdLib\StdObject\DateTimeObject
 */
trait ManipulatorTrait
{
    /**
     * Adds an amount of days, months, years, hours, minutes and seconds to a DateTimeObject.
     *
     * @param string $amount You can specify the amount in ISO8601 format (example: 'P14D' = 14 days; 'P1DT12H' = 1 day 12 hours),
     *                       or as a date string (example: '1 day', '2 months', '3 year', '2 days + 10 minutes').
     *
     * @return $this
     */
    public function add($amount)
    {
        try {
            $interval = $this->parseDateInterval($amount);
            $this->getDateObject()->add($interval);
        } catch (\Exception $e) {
            handle(new DateTimeObjectException($e->getMessage()));
        }


        return $this;
    }

    /**
     * Set the date on current object.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     *
     * @return $this
     */
    public function setDate($year, $month, $day)
    {
        try {
            $this->getDateObject()->setDate($year, $month, $day);
        } catch (\Exception $e) {
            handle(new DateTimeObjectException($e->getMessage()));
        }

        return $this;
    }

    /**
     * Set the time on current object.
     *
     * @param int $hour
     * @param int $minute
     * @param int $second
     *
     * @return $this
     */
    public function setTime($hour, $minute, $second = 0)
    {
        try {
            $this->getDateObject()->setTime($hour, $minute, $second);
        } catch (\Exception $e) {
            handle(new DateTimeObjectException($e->getMessage()));
        }

        return $this;
    }

    /**
     * Set the timestamp on current object.
     *
     * @param int $timestamp UNIX timestamp.
     *
     * @return $this
     */
    public function setTimestamp($timestamp)
    {
        try {
            $this->getDateObject()->setTimestamp($timestamp);
        } catch (\Exception $e) {
            handle(new DateTimeObjectException($e->getMessage()));
        }

        return $this;
    }

    /**
     * Subtracts an amount of days, months, years, hours, minutes and seconds from current DateTimeObject.
     *
     * @param string $amount You can specify the amount in ISO8601 format (example: 'P14D' = 14 days; 'P1DT12H' = 1 day 12 hours),
     *                       or as a date string (example: '1 day', '2 months', '3 year', '2 days + 10 minutes').
     *
     * @return $this
     */
    public function sub($amount)
    {
        try {
            $interval = $this->parseDateInterval($amount);
            $this->getDateObject()->sub($interval);
        } catch (\Exception $e) {
            handle(new DateTimeObjectException($e->getMessage()));
        }

        return $this;
    }


    /**
     * Offsets the date object from current timezone to defined $timezone.
     * This is an alias of DateTimeObject::setTimezone.
     *
     * @param string|\DateTimeZone $timezone Timezone to which you wish to offset. You can either pass \DateTimeZone object
     *                                       or a valid timezone string. For timezone string formats
     *                                       visit: http://php.net/manual/en/timezones.php
     *
     * @return $this
     */
    public function offsetToTimezone($timezone)
    {
        try {
            $this->setTimezone($timezone);
        } catch (\Exception $e) {
            handle(new DateTimeObjectException($e->getMessage()));
        }

        return $this;
    }

    /**
     * Returns the difference between two dates.
     * Returns value in user friendly way:<br>
     * example: 1 years, 0 months, 3 days, 18 hours, 10 minutes, 50 seconds
     * @param $datetime
     * @return string
     */
    public function diff($datetime): string
    {
        // Declare and define two dates
        $date1 = strtotime($this);
        $date2 = strtotime($datetime);

        // Formulate the Difference between two dates
        $diff = abs($date2 - $date1);

        // To get the year divide the resultant date into
        // total seconds in a year (365*60*60*24)
        $years = floor($diff / (365*60*60*24));

        // To get the month, subtract it with years and
        // divide the resultant date into
        // total seconds in a month (30*60*60*24)
        $months = floor(($diff - $years * 365*60*60*24)
            / (30*60*60*24));

        // To get the day, subtract it with years and
        // months and divide the resultant date into
        // total seconds in a days (60*60*24)
        $days = floor(($diff - $years * 365*60*60*24 -
            $months*30*60*60*24)/ (60*60*24));

        // To get the hour, subtract it with years,
        // months & seconds and divide the resultant
        // date into total seconds in a hours (60*60)
        $hours = floor(($diff - $years * 365*60*60*24
            - $months*30*60*60*24 - $days*60*60*24)
            / (60*60));

        // To get the minutes, subtract it with years,
        // months, seconds and hours and divide the
        // resultant date into total seconds i.e. 60
        $minutes = floor(($diff - $years * 365*60*60*24
            - $months*30*60*60*24 - $days*60*60*24
            - $hours*60*60)/ 60);

        // To get the minutes, subtract it with years,
        // months, seconds, hours and minutes
        $seconds = floor(($diff - $years * 365*60*60*24
            - $months*30*60*60*24 - $days*60*60*24
            - $hours*60*60 - $minutes*60));

        return sprintf("%d years, %d months, %d days, %d hours, %d minutes, %d seconds",
            $years, $months, $days, $hours, $minutes, $seconds);
    }

    /**
     * @param $interval
     *
     * @return \DateInterval
     */
    private function parseDateInterval($interval)
    {
        try {
            if (!$this->isInstanceOf($interval, 'DateInterval')) {
                $interval = new StringObject($interval);
                if ($interval->startsWith('P')) {
                    $interval = new \DateInterval($interval);
                } else {
                    $interval = \DateInterval::createFromDateString($interval);
                }
            }
        } catch (\Exception $e) {
            handle(new DateTimeObjectException(DateTimeObjectException::MSG_INVALID_DATE_INTERVAL, [$interval])) ;
        }

        return $interval;
    }
}
