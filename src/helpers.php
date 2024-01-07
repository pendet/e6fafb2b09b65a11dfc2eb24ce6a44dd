<?php

use App\Auth;

if (!function_exists('now')) {
    function now(DateTimeZone|string|null $tz = null)
    {
        $tz = empty($tz) ? $_ENV['TIMEZONE'] : $tz;
        return Carbon\Carbon::now($tz);
    }
}

if (!function_exists('authenticate')) {
    function authenticate()
    {
        $auth = Auth::verifyToken();
        if (isset($auth['status']) && !$auth['status']) {
            return $auth;
        } else {
            return true;
        }
    }
}
