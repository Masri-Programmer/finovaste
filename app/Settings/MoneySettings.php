<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class MoneySettings extends Settings
{
    public string $currency_code;
    public string $currency_symbol;
    public string $symbol_position; // 'left' or 'right'
    public string $decimal_separator;
    public string $thousands_separator;

    public static function group(): string
    {
        return 'money';
    }
}
