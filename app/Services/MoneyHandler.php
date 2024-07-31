<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Money;
use NumberFormatter;

class MoneyHandler
{
    public static function fromCents(int $cents): Money
    {
        return new Money(
            amount: self::calculateAmountFromCents($cents),
            cents: self::calculateRemainderCents($cents),
            currency: 'EUR',
        );
    }

    public static function moneyToWords(Money $money, $lang = 'lt'): string
    {
        $formatter = new NumberFormatter($lang, NumberFormatter::SPELLOUT);
        
        $amountWords = $formatter->formatCurrency($money->getAmount(), $money->getCurrency());

        return sprintf('%s %s ir %s ct', $amountWords, ucfirst(strtolower($money->getCurrency())), $money->getCents());
    }

    private static function calculateAmountFromCents(int $cents): int
    {
        return (int) floor($cents / 100);
    }

    private static function calculateRemainderCents(int $cents): int
    {
        return $cents % 100;
    }
}
