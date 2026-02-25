<?php

namespace App\Service\Investment;

use App\Exception\EndDateBeforeStartDateException;

class InvestmentCalculatorService
{
    public function calculateMonths(
        \DateTimeImmutable $startDate,
        \DateTimeImmutable $endDate,
    ): int {
        if ($endDate < $startDate) {
            throw new EndDateBeforeStartDateException();
        }

        $interval = $startDate->diff($endDate);
        $months = ($interval->y * 12) + $interval->m;

        return $months;
    }

    public function calculateBalance(
        float $investedAmount,
        int $months,
    ): float {
        $rate = 0.0052;

        $balance = $investedAmount * pow(1 + $rate, $months);

        return round($balance, 2);
    }

    public function calculateTax(
        float $investedAmount,
        float $balance,
        \DateTimeImmutable $startDate,
        \DateTimeImmutable $endDate,
    ): float {
        $gain = max(0, $balance - $investedAmount);

        $years = $startDate->diff($endDate)->y;

        if ($years < 1) {
            $taxRate = 0.225;
        } elseif (1 <= $years && $years <= 2) {
            $taxRate = 0.185;
        } elseif ($years > 2) {
            $taxRate = 0.15;
        }

        $discount = $gain - ($gain * $taxRate);
        $finalBalance = $investedAmount + $discount;

        return round($finalBalance, 2);
    }
}
