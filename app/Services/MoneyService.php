<?php

namespace App\Services;

use Brick\Money\Money;
use Brick\Math\RoundingMode;
use Brick\Money\Context\CustomContext;
use NumberFormatter;

class MoneyService
{
    /**
     * Create a Money instance from a float/decimal and currency code.
     */
    public static function make($amount, ?string $currency = null): Money
    {
        $currency = $currency ?? config('money.defaults.currency');
        // Brick\Money prefers strings for precision to avoid float drift
        return Money::of((string) $amount, $currency, new CustomContext(2), RoundingMode::HALF_UP);
    }

    /**
     * Format money for display (server-side fallback).
     */
    public static function format($amount, ?string $currency = null, ?string $locale = null): string
    {
        $locale = $locale ?? config('money.defaults.locale');
        $currency = $currency ?? config('money.defaults.currency');

        $money = self::make($amount, $currency);
        
        return $money->formatWith(new NumberFormatter($locale, NumberFormatter::CURRENCY));
    }

    /**
     * Parse a user input string (e.g., "1.234,50") into a clean database decimal.
     */
    public static function parseToDecimal(string $input): string
    {
        // Remove standard thousand separators (dots or commas depending on logic)
        // This is a simplified parser; for robust parsing, use NumberFormatter::parse
        $clean = str_replace([',', ' '], ['.', ''], $input); 
        return (string) floatval($clean); 
    }

    /**
     * Get the currency for the current user or default.
     */
    public static function getCurrency(?\App\Models\User $user = null): string
    {
        if ($user) {
            return $user->currency;
        }

        if (auth()->check()) {
            return auth()->user()->currency;
        }

        return config('money.defaults.currency');
    }
}