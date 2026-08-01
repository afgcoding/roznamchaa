<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Sale;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SalesChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $pollingInterval = '60s';

    protected ?string $maxHeight = '320px';

    protected int | string | array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return auth()->user()?->isAdmin()
            ? 'Sales vs Expenses (30 days)'
            : 'Daily Sales (30 days)';
    }

    public function getDescription(): ?string
    {
        return auth()->user()?->isAdmin()
            ? 'Daily sales compared with expenses for the last 30 days.'
            : 'Your shop sales trend for the last 30 days.';
    }

    protected function getData(): array
    {
        /** @var User|null $user */
        $user = auth()->user();
        $isAdmin = (bool) $user?->isAdmin();

        $start = Carbon::today()->subDays(29);
        $end = Carbon::today();

        $salesByDay = Sale::query()
            ->selectRaw('DATE(created_at) as day, SUM(payable_amount) as total')
            ->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->groupBy('day')
            ->pluck('total', 'day');

        $expensesByDay = $isAdmin
            ? Expense::query()
                ->selectRaw('DATE(date) as day, SUM(amount) as total')
                ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                ->groupBy('day')
                ->pluck('total', 'day')
            : collect();

        $labels = [];
        $salesData = [];
        $expensesData = [];

        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            $key = $day->toDateString();
            $labels[] = $day->format('M j');
            $salesData[] = round((float) ($salesByDay[$key] ?? 0), 2);

            if ($isAdmin) {
                $expensesData[] = round((float) ($expensesByDay[$key] ?? 0), 2);
            }
        }

        $datasets = [
            [
                'label' => 'Sales',
                'data' => $salesData,
                'borderColor' => '#22c55e',
                'backgroundColor' => 'rgba(34, 197, 94, 0.15)',
                'fill' => true,
                'tension' => 0.3,
            ],
        ];

        if ($isAdmin) {
            $datasets[] = [
                'label' => 'Expenses',
                'data' => $expensesData,
                'borderColor' => '#ef4444',
                'backgroundColor' => 'rgba(239, 68, 68, 0.12)',
                'fill' => true,
                'tension' => 0.3,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
        ];
    }
}
