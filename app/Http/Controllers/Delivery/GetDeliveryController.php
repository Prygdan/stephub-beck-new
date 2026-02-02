<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Jobs\GetDeliveryAreas;
use App\Jobs\GetDeliveryBranches;
use App\Jobs\GetDeliveryCities;
use App\Jobs\GetDeliveryPostomates;
use App\Models\Delivery\NovaPoshtaKey;
use App\Models\JobLog;

class GetDeliveryController extends Controller
{
    private function getApiKey()
    {
        $apiKey = NovaPoshtaKey::first();

        if (!$apiKey) {
            throw new \App\Exceptions\NovaPoshtaApiKeyMissingException();
        } else {
            return $apiKey->value;
        }
    }

    public function getAreas()
    {
        $log = JobLog::create([
            'name' => 'GetDeliveryAreas',
            'status' => 'processing',
        ]);

        $modelName = 'AddressGeneral';
        $calledMethod = 'getAreas';
        $apiKey = $this->getApiKey(); 

        GetDeliveryAreas::dispatch($apiKey, $modelName, $calledMethod, $log);

        return response()->json($log, 200);
    }

    public function getCities()
    {
        $log = JobLog::create([
            'name' => 'GetDeliveryCities',
            'status' => 'processing',
        ]);

        $modelName = 'AddressGeneral';
        $calledMethod = 'getCities';
        $apiKey = $this->getApiKey();

        GetDeliveryCities::dispatch($apiKey, $modelName, $calledMethod, $log);

        return response()->json($log, 200);
    }

    public function getBranches()
    {
        ini_set('max_execution_time', 36000);
        
        $log = JobLog::create([
            'name' => 'GetDeliveryBranches',
            'status' => 'processing',
        ]);

        $apiKey = $this->getApiKey();
        $modelName = 'AddressGeneral';
        $calledMethod = 'getWarehouses';
        $limit = 1000;
        $page = 1;

        GetDeliveryBranches::dispatch($apiKey, $modelName, $calledMethod, $limit, $page, $log);

        return response()->json($log, 200);
    }

    public function getPostomates()
    {
        ini_set('max_execution_time', 36000);

        $log = JobLog::create([
            'name' => 'GetDeliveryPostomates',
            'status' => 'processing',
        ]);

        $apiKey = $this->getApiKey();
        $modelName = 'AddressGeneral';
        $calledMethod = 'getWarehouses';
        $limit = 1000;
        $page = 1;

        GetDeliveryPostomates::dispatch($apiKey, $modelName, $calledMethod, $limit, $page, $log);

        return response()->json($log, 200);
    }

    public function getJobStatus($log)
    {
        $log = JobLog::findOrFail($log);

        if (!$log) {
            return response()->json(['status' => 'not_found'], 404);
        }

        return response()->json($log);
    }
}
