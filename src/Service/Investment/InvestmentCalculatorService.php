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
}
