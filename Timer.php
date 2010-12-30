<?php
class App_Timer
{

    /**
     * Timers
     * @var array
     */
    protected $_timers = array(
            'default' => null
    );

    /**
     * Checks whether timer for given label was started
     * @param string $timeLabel
     * @return App_Timer
     */
    public function start($timeLabel = 'default')
    {
        if ($this->wasStarted($timeLabel)) {
            throw new Exception('Timer for ' . $timeLabel . ' was already started');
        }

        list($usec, $sec) = explode(' ', microtime());
        $time = (float) $sec + (float) $usec;
        
        $this->_timers[$timeLabel] = $time;
        return $this;
    }

    /**
     * 
     * @param string $timeLabel defaults to default
     * @return bool
     */
    public function wasStarted($timeLabel = 'default')
    {
        if ((array_key_exists($timeLabel, $this->_timers)) && ($this->_timers[$timeLabel] !== null)) {
            return true;
        }
        return false;
    }

    /**
     * Return the elapsed time in seconds of a given label
     *
     * @param string $timeLabel label to get elapsed time for
     * @param int $round number of decimal digits
     * @return float elapsed time
     */
    public function getElapsedTime($timeLabel = 'default', $round = 2)
    {
        if ($this->wasStarted($timeLabel)) {
            list($usec, $sec) = explode(' ', microtime());
            $end = (float) $sec + (float) $usec;
            $elapsed = round($end - $this->_timers[$timeLabel], $round);
            return $elapsed;
        }
        throw new Exception('Timer for ' . $timeLabel . ' was not started');
    }


}