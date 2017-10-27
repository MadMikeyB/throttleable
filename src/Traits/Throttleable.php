<?php
namespace MadMikeyB\Throttleable\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;
use MadMikeyB\Throttleable\Models\Throttle;

trait Throttleable 
{
    /**
     * Check if the IP address exists in the throttles table
     *
     * @return bool
     */
    public function check()
    {
        $throttle = $this->get();

        if (is_bool($throttle)) {
            return $this->check();
        }

        if ($this->clearExpired($throttle)) {
            return true;
        }
        
        if ($throttle->attempts > $this->attemptLimit) {
            return false;
        }

        return true;
    }

    /**
     * Get or Create the Throttle Record
     *
     * @return \MadMikeyB\Throttleable\Models\Throttle
     */
    public function get()
    {
        $throttle = $this->where('ip', $this->request->ip())->first();
        if (!$throttle) {
            $throttle = $this->createThrottle();
        } else {
            $throttle = $this->hitThrottle($throttle);
        }

        return $throttle;
    }

    /**
     * Create a new Throttle Row
     *
     * @return \MadMikeyB\Throttleable\Models\Throttle
     */
    protected function createThrottle()
    {
        $this->ip = $this->request->ip();
        $this->attempts = 0;
        switch ($this->expiryMetric)
        {
            case 'hour':
                $this->expires_at = Carbon::now()->addHours($this->expiryTimeLimit);
                break;

            case 'day':
                $this->expires_at = Carbon::now()->addDays($this->expiryTimeLimit);
                break;

            default:
            case 'week':
                $this->expires_at = Carbon::now()->addWeeks($this->expiryTimeLimit);
                break;
        }
        $this->created_at = Carbon::now();
        $throttle = $this->save();

        return $throttle;
    }

    /**
     * Hit the Throttle Row and increment the attempt counter
     *
     * @param  \MadMikeyB\Throttleable\Models\Throttle $throttle
     * @return \MadMikeyB\Throttleable\Models\Throttle
     */
    public function hitThrottle(Throttle $throttle)
    {
        $throttle->increment('attempts');
        $throttle->save();
        return $throttle;
    }

    /**
     * Clear Expired Throttles from the Database
     *
     * @param  \MadMikeyB\Throttleable\Models\Throttle $throttle
     * @return bool
     */
    public function clearExpired(Throttle $throttle)
    {
        $now = Carbon::now();
        if ($now->timestamp > $throttle->expires_at->timestamp) {
            $throttle->delete();
            return true;
        }
        return false;
    }
}
