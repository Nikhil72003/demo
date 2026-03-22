<?php

namespace App\Filament\Widgets;

use App\Models\Blog\Post;
use Filament\Widgets\ChartWidget;

class TopViewedPostsChart extends ChartWidget
{
    protected ?string $heading = 'Top 5 Most Viewed Posts';

    protected static ?int $sort = 1;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $posts = Post::query()
            ->orderByDesc('view_count')
            ->limit(5)
            ->get(['title', 'view_count']);

        return [
            'datasets' => [
                [
                    'label' => 'Views',
                    'data' => $posts->pluck('view_count')->all(),
                    'backgroundColor' => '#6366f1',
                ],
            ],
            'labels' => $posts->pluck('title')->map(fn (string $title): string => strlen($title) > 30 ? substr($title, 0, 30) . '...' : $title)->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => ['display' => false],
            ],
        ];
    }
}
