<?php

namespace App\Jobs;

use App\Models\View;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class IncrementViewCount implements ShouldQueue
{
    use Queueable;

    public int $productId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $productId)
    {
        $this->productId = $productId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $view = View::updateOrCreate(
            ['product_id' => $this->productId],
            ['count' => 0]
        );

        $view->increment('count');
    }
}
