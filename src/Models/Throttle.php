<?php

namespace MadMikeyB\Throttleable\Models;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use MadMikeyB\Throttleable\Traits\Throttleable;

class Throttle extends Model
{
    use Throttleable;

    /** @var \Illuminate\Http\Request */
    public $request;

    /** @var int Number of attempts allowed before the user is throttled */
    public $attemptLimit;

    /** @var int The Time Metric to use for expiry time limit */
    public $expiryMetric;

    /** @var int Number of Hours, Days or Weeks before throttle expires */
    public $expiryTimeLimit;

    /** @var bool */
    public $timestamps = false;

    /** @var array */
    protected $fillable = ['ip', 'attempts', 'expires_at', 'created_at'];
    
    /** @var array */
    protected $dates =  ['expires_at', 'created_at'];

    /**
     * @param \Illuminate\Http\Request $request
     */
    public function __construct($request, $attemptLimit = null, $expiryWeeks = null)
    {
        $this->request = $request;
        $this->attemptLimit = $attemptLimit ?? config('throttleable.attempt_limit');
        $this->expiryMetric = $expiryMetric ?? config('throttleable.expiry_metric');
        $this->expiryTimeLimit = $expiryTimeLimit ?? config('throttleable.expiry_timelimit');
    }
}
