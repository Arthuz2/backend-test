<?php

namespace App\Tests\Service;

use App\Service\Investment\InvestmentCalculatorService;
use PHPUnit\Framework\TestCase;

class InvestmentCalculatorServicePhpTest extends TestCase
{
    private InvestmentCalculatorService $service;

    protected function setUp(): void
    {
        $this->service = new InvestmentCalculatorService();
    }

    public function testCalculateMonths()
    {
        $start = new \DateTimeImmutable('2024-01-01');
        $end = new \DateTimeImmutable('2024-03-01');

        $months = $this->service->calculateMonths($start, $end);

        $this->assertEquals(2, $months);
    }

    public function testCalculateBalance()
    {
        $investedAmount = 1000;
        $months = 12;

        $balance = $this->service->calculateBalance($investedAmount, $months);

        $this->assertGreaterThan(1000, $balance);
    }

    public function testCalculateTax()
    {
        $invested = 1000;
        $balance = 1100;

        $start = new \DateTimeImmutable('2020-01-01');
        $end = new \DateTimeImmutable('2024-01-01');

        $final = $this->service->calculateTax(
            $invested,
            $balance,
            $start,
            $end
        );

        $this->assertLessThan($balance, $final);
    }
}
