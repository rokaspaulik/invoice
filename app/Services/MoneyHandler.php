<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Invoice;
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

    public static function fromAmountString(string $amount): Money
    {
        $cents = (int) (floatval($amount) * 100);

        return self::fromCents($cents);
    }

    public static function calculateInvoiceTotalInCents(Invoice $invoice): int
    {
        $total = 0;

        foreach ($invoice->getItems() as $item) {
            $itemMoney = new Money(
                amount: $item['money']['amount'],
                cents: $item['money']['cents'],
                currency: $item['money']['currency'],
            );

            $itemCents = $itemMoney->getAmount() * 100 + $itemMoney->getCents();
            $total += $itemCents * $item['amount'];
        }

        return $total;
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
