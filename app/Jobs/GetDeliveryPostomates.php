<?php

namespace App\Jobs;

use App\Models\Delivery\Postomat;
use App\Models\JobLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GetDeliveryPostomates implements ShouldQueue
{
    use Queueable;

    public $timeout = 3600;

    protected $apiKey;
    protected $modelName;
    protected $calledMethod;
    protected $page;
    protected $limit;
    protected $log;

    public function __construct(string $apiKey, string $modelName, string $calledMethod, int $limit, int $page, JobLog $log)
    {
        $this->apiKey = $apiKey;
        $this->modelName = $modelName;
        $this->calledMethod = $calledMethod;
        $this->limit = $limit;
        $this->page = $page;
        $this->log = $log;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            do {
                $response = Http::post('https://api.novaposhta.ua/v2.0/json/', [
                    'apiKey' => $this->apiKey,
                    'modelName' => $this->modelName,
                    'calledMethod' => $this->calledMethod,
                    'methodProperties' => [
                        'Page' => $this->page,
                        'Limit' => $this->limit
                    ]
                ]);

                if (!$response->successful()) {
                    $this->log->update([
                        'status' => 'error',
                        'message' => json_encode($response->body())
                    ]);
                    throw new \Exception('API response error: ' . $response->body());
                }

                $data = $response->json()['data'] ?? [];

                foreach ($data as $item) {
                    if ($item['CategoryOfWarehouse'] === 'Postomat') {
                        Postomat::updateOrCreate(
                            ['ref' => $item['Ref']],
                            [
                                'cityRef' => $item['CityRef'],
                                'description' => $item['Description'],
                            ]
                        );
                    }
                }

                $this->page++;

            } while (!empty($data));

            $this->log->update([
                'status' => 'success',
                'message' => 'Postomats downloaded successfully!',
            ]);

        } catch (\Exception $e) {
            $this->log->update([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);

            Log::error('Job GetDeliveryPostomates failed', [
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    public function failed(\Exception $exception): void
    {
        $this->log->update([
            'status' => 'error',
            'message' => 'Job GetDeliveryPostomates failed: ' . $exception->getMessage(),
        ]);

        Log::error('Job GetDeliveryPostomates failed', [
            'error' => $exception->getMessage()
        ]);
    }
}
