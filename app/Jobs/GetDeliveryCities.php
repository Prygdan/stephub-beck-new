<?php

namespace App\Jobs;

use App\Models\Delivery\City;
use App\Models\JobLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GetDeliveryCities implements ShouldQueue
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
                    City::updateOrCreate(
                        [
                            'areaRef' => $item['Area'],
                            'ref' => $item['Ref'],
                            'description' => isset($item['Description']) && !empty($item['Description']) 
                                             ? $item['Description'] 
                                             : ''
                        ]
                    );
                }
                $this->log->update([
                    'status' => 'success',
                    'message' => 'Cities downloaded successfully',
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
            'message' => 'Job GetCities failed: ' . $exception->getMessage(),
        ]);

        Log::error('Job GetCities failed', [
            'error' => $exception->getMessage()
        ]);
    }
}
