<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Delivery\Area;
use App\Models\JobLog;

class GetDeliveryAreas implements ShouldQueue
{
    use Queueable;

    protected $apiKey;
    protected $modelName;
    protected $calledMethod;
    protected $log;


    /**
     * Create a new job instance.
     */
    public function __construct(string $apiKey, string $modelName, string $calledMethod, JobLog $log)
    {
        $this->apiKey = $apiKey;
        $this->modelName = $modelName;
        $this->calledMethod = $calledMethod;
        $this->log = $log;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $response = Http::post('https://api.novaposhta.ua/v2.0/json/', [
                'apiKey' => $this->apiKey,
                'modelName' => $this->modelName,
                'calledMethod' => $this->calledMethod,
            ]);

            $data = $response->json();

            if ($data['success']) {
                foreach ($data['data'] as $item) {
                    Area::updateOrCreate(
                        ['ref' => $item['Ref']],
                        ['description' => $item['Description']]
                    );
                }
                $this->log->update([
                    'status' => 'success',
                    'message' => 'Areas downloaded successfully',
                ]);
            } else {
                $this->log->update([
                    'status' => 'error',
                    'message' => json_encode($data['errors'])
                ]);
                throw new \Exception(json_encode($data['errors']));
            }
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
            'message' => 'Job GetAreas failed: ' . $exception->getMessage(),
        ]);

        Log::error('Job GetAreas failed', [
            'error' => $exception->getMessage()
        ]);
    }
}
