<?php

if (! function_exists('money')) {
    function money($amount, int $decimals = 0): string
    {
        return config('business.currency').' '.number_format((float) $amount, $decimals);
    }
}
