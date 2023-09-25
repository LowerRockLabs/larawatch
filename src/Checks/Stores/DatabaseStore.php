<?php

namespace Larawatch\Checks\Stores;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Larawatch\Models\LarawatchCheck;
use Larawatch\Checks\CheckResult;
use Larawatch\Traits\Core\ProvidesCheckData;

class DatabaseStore 
{
    use ProvidesCheckData;

    public function __construct()
    {


    }
    
    public function storeCheck(CheckResult $checkResult)
    {
        LarawatchCheck::create([
            'check_run_id' => $this->getCheckRunID(),
            'check_name' => $this->getCheckName(), 
            'check_data' => $checkResult->getCheckData(), 
            'result_data' => $checkResult->getResultData(),
            'error_messages' => $checkResult->getErrorMessages(),
            'raw_data' => $checkResult,
            'access_data' => $checkResult->getAccessData(),
            'result_message' => $checkResult->getResultMessage(),
            'started_at' => $checkResult->getStartTime(),
            'finished_at' => $checkResult->getEndTime(),
            'result_status' => $checkResult->getResultStatus(),
            'check_target' => $checkResult->getCheckTarget(),
        ]);
    }

    public function save(Collection $checkResults): bool
    {
        foreach ($checkResults->toArray() as $key => $data)
        {
            foreach ($data as $key1 => $data1)
            {

                if (is_array($data1) && isset($data1[0]) && $data1[0] instanceof \Larawatch\Checks\CheckResult)
                {
                    $this->setCheckName($key1);
                    $this->storeCheck($data1[0]);
                    /*
                    LarawatchCheck::create([
                        'check_name' => $key1, 
                        'result_data' => $data1[0]->resultData ?? [],
                        'raw_data' => $data1[0],
                        'access_data' => $data1[0]->getAccessData() ?? ['server_key' => null, 'project_key' => null],
                        //'access_data' => ['server_key' => $data1[0]->resultData['server_key'] ?? null, 'project_key' => $data1[0]->resultData['project_key'] ?? null],
                        //'check_data' => ,
                        'result_message' => $data1[0]->resultMessage ?? null,
                        'started_at' => $data1[0]->started_at ?? now(),
                        'finished_at' => $data1[0]->ended_at ?? now(),
                        'result_status' => $data1[0]->resultStatus ?? null,

                    ]);*/
                }
                else
                {
                    LarawatchCheck::create([
                        'check_run_id' => $this->getCheckRunID(),
                        'check_name' => $key1, 
                        'check_data' => $data1[0]->getCheckData(), 
                        'access_data' => ['server_key' => $data1[0]->resultData['server_key'] ?? null, 'project_key' => $data1[0]->resultData['project_key'] ?? null],
                        'result_status' => 'skipped',
                        'check_target' => $data1[0]->getCheckTarget(),
                        'started_at' => now(),
                        'finished_at' => now(),
                    ]);

                }
            }
        }
        return true;
    }

}
