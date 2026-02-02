<?php

namespace App\Jobs;

use App\Models\Delivery\Branch;
use App\Models\JobLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GetDeliveryBranches implements ShouldQueue
{
    use Queueable;

    public $timeout = 3600;

    protected $apiKey;
    protected $modelName;
    protected $calledMethod;
    protected $log;
    protected $page;
    protected $limit;

    public function __construct(string $apiKey, string $modelName, string $calledMethod, int $limit, int $page, JobLog $log)
    {
        $this->apiKey = $apiKey;
        $this->modelName = $modelName;
        $this->calledMethod = $calledMethod;
        $this->limit = $limit;
        $this->page = $page;
        $this->log = $log;
    }

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

                $data = $response->json();

                if ($data['success']) {
                    foreach ($data['data'] as $item) {
                        if ($item['CategoryOfWarehouse'] != 'Postomat') {
                            Branch::updateOrCreate(
                                ['ref' => $item['Ref']],
                                [
                                    'cityRef' => $item['CityRef'],
                                    'description' => $item['Description'] ?? ''
                                ]
                            );
                        }
                    }

                    $this->page++;
                } else {
                    $this->log->update([
                        'status' => 'error',
                        'message' => json_encode($data['errors'])
                    ]);
                    
                    throw new \Exception(json_encode($data['errors']));
                }
            } while (!empty($data['data']));

            $this->log->update([
                'status' => 'success',
                'message' => 'Branches downloaded successfully',
            ]);

        } catch (\Exception $e) {
            $this->log->update([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function failed(\Exception $exception): void
    {
        $this->log->update([
            'status' => 'error',
            'message' => 'Job GetBranches failed: ' . $exception->getMessage(),
        ]);

        Log::error('Job GetBranches failed', [
            'error' => $exception->getMessage()
        ]);
    }
}