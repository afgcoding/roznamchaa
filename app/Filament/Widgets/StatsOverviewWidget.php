<?php

namespace App\Filament\Widgets;

use App\Models\CustomerLedger;
use App\Models\Expense;
use App\Models\Sale;
use App\Models\User;
use App\Models\Wholesaler;
use App\Support\NumberFormat;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '60s';

    protected ?string $heading = 'Shop Overview';

    protected function getDescription(): ?string
    {
        return auth()->user()?->isAdmin()
            ? 'Sales, debts, and expenses at a glance.'
            : 'Today\'s sales and customer dues.';
    }

    protected function getStats(): array
    {
        /** @var User|null $user */
        $user = auth()->user();
        $isAdmin = (bool) $user?->isAdmin();

        $todaySales = (float) Sale::query()
            ->whereDate('created_at', today())
            ->sum('payable_amount');

        $yesterdaySales = (float) Sale::query()
            ->whereDate('created_at', today()->subDay())
            ->sum('payable_amount');

        $salesDiff = $todaySales - $yesterdaySales;
        $salesChart = $this->lastSevenDaysSales();

        $stats = [
            Stat::make('Today\'s Sales', $this->money($todaySales))
                ->description($this->salesComparisonDescription($salesDiff, $yesterdaySales))
                ->descriptionIcon($this->salesComparisonIcon($salesDiff))
                ->color($this->salesComparisonColor($salesDiff))
                ->chart($salesChart)
                ->chartColor($this->salesComparisonColor($salesDiff))
                ->icon(Heroicon::OutlinedShoppingCart),
            Stat::make('Total Customer Debt', $this->money($this->totalCustomerDebt()))
                ->description('Unpaid customer qarz')
                ->descriptionIcon(Heroicon::OutlinedUsers)
                ->color($this->totalCustomerDebt() > 0 ? 'warning' : 'success')
                ->icon(Heroicon::OutlinedBanknotes),
        ];

        if ($isAdmin) {
            $supplierDebt = (float) Wholesaler::query()->sum('total_debt');
            $todayExpenses = (float) Expense::query()
                ->whereDate('date', today())
                ->sum('amount');

            $stats[] = Stat::make('Total Supplier Debt', $this->money($supplierDebt))
                ->description('Owed to wholesalers')
                ->descriptionIcon(Heroicon::OutlinedTruck)
                ->color($supplierDebt > 0 ? 'danger' : 'success')
                ->icon(Heroicon::OutlinedTruck);

            $stats[] = Stat::make('Today\'s Expenses', $this->money($todayExpenses))
                ->description('Shop expenses for today')
                ->descriptionIcon(Heroicon::OutlinedReceiptPercent)
                ->color($todayExpenses > 0 ? 'danger' : 'gray')
                ->icon(Heroicon::OutlinedReceiptPercent);
        }

        return $stats;
    }

    protected function totalCustomerDebt(): float
    {
        $credits = (float) CustomerLedger::query()
            ->where('type', 'credit')
            ->sum('amount');

        $payments = (float) CustomerLedger::query()
            ->where('type', 'payment')
            ->sum('amount');

        return max(0, $credits - $payments);
    }

    /**
     * @return array<int, float>
     */
    protected function lastSevenDaysSales(): array
    {
        $start = Carbon::today()->subDays(6);

        $rows = Sale::query()
            ->selectRaw('DATE(created_at) as day, SUM(payable_amount) as total')
            ->where('created_at', '>=', $start->copy()->startOfDay())
            ->groupBy('day')
            ->pluck('total', 'day');

        $chart = [];

        for ($i = 0; $i < 7; $i++) {
            $day = $start->copy()->addDays($i)->toDateString();
            $chart[] = (float) ($rows[$day] ?? 0);
        }

        return $chart;
    }

    protected function salesComparisonDescription(float $diff, float $yesterdaySales): string
    {
        if ($yesterdaySales <= 0 && $diff === 0.0) {
            return 'No sales yesterday';
        }

        $amount = $this->money(abs($diff));

        if ($diff > 0) {
            return "{$amount} more than yesterday";
        }

        if ($diff < 0) {
            return "{$amount} less than yesterday";
        }

        return 'Same as yesterday';
    }

    protected function salesComparisonIcon(float $diff): Heroicon
    {
        if ($diff > 0) {
            return Heroicon::ArrowTrendingUp;
        }

        if ($diff < 0) {
            return Heroicon::ArrowTrendingDown;
        }

        return Heroicon::OutlinedShoppingCart;
    }

    protected function salesComparisonColor(float $diff): string
    {
        if ($diff > 0) {
            return 'success';
        }

        if ($diff < 0) {
            return 'danger';
        }

        return 'gray';
    }

    protected function money(float $amount): string
    {
        return 'AFN '.NumberFormat::trim($amount, 2);
    }
}
