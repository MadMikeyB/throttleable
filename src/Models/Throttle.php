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

    /** @var int Number of weeks before throttle expires */
    public $expiryWeeks;

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
        $this->expiryWeeks = $expiryWeeks ?? config('throttleable.expiry_weeks');
    }

}
