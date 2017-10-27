<?php

return [
    /**
     * Number of attempts permitted to a single
     * IP address before being throttled.
     */
    'attempt_limit' => 10,
    
    /**
     * The datetime metric to use for expirations
     * Available options are hour, day or week.
     */
    'expiry_metric' => 'week',

    /**
     * The number of hours, days or weeks to
     * keep a throttle valid for.
     */
    'expiry_timelimit' => 1
];
