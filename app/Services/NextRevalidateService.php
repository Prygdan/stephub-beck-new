<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NextRevalidateService
{
  public function tags(array $tags): void
  {
    Http::timeout(3)
        ->post(
            rtrim(config('services.next.frontend_url'), '/') . '/revalidate',
            [
                'secret' => config('services.next.revalidate_secret'),
                'tags'   => $tags,
            ]
        );
  }
}
