<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Delivery\Branch;
use App\Models\Delivery\Postomat;
use App\Models\JobLog;

class GetDeliveryBranchesPostomates implements ShouldQueue
{
    use Queueable;

    protected $apiKey;
    protected $modelName;
    protected $calledMethod;
    protected $page;
    protected $limit;

    public function __construct(string $apiKey, string $modelName, string $calledMethod, $limit, $page)
    {
        $this->apiKey = $apiKey;
        $this->modelName = $modelName;
        $this->calledMethod = $calledMethod;
        $this->limit = $limit;
        $this->page = $page;
    }


    public function handle() :void
    {
        $log = JobLog::create([
            'name' => 'GetDeliveryCities',
            'status' => 'processing',
        ]);

        do{
            $response = Http::post('https://api.novaposhta.ua/v2.0/json/', [
                'apiKey'            =>  $this->apiKey,
                'modelName'         =>  $this->modelName,
                'calledMethod'      =>  $this->calledMethod,
                'methodProperties'  =>  [
                    'Page'  =>  $this->page,
                    'Limit' =>  $this->limit
                ]
            ]);

            if($response->successful()) {
                $data = $response->json()['data'];

                foreach ($data as $item) {
                    // Розділяємо на поштомати і відділення
                    if ($item['CategoryOfWarehouse'] === 'Postomat') {
                        Postomat::updateOrCreate(
                            ['ref' => $item['Ref']],
                            [
                                'cityRef' => $item['CityRef'],
                                'description' => $item['Description'],
                            ]
                        );
                    } else {
                        Branch::updateOrCreate(
                            ['ref' => $item['Ref']],
                            [
                                'cityRef' => $item['CityRef'],
                                'description' => $item['Description'],
                            ]
                        );
                    }
                }
                Log::info($this->page);
                $this->page++;
                
            } else {
                Log::info($response);
            }

        } while (!empty($data));

        $log->update([
            'status' => 'success',
            'message' => 'Warehouses as Postomats downloaded successfully!',
        ]);
    }
}
