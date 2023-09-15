<?php

namespace Larawatch\Checks\Stores;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Larawatch\Models\LarawatchCheck;

class DatabaseStore 
{
    public function __construct()
    {


    }

    public function save(Collection $checkResults): bool
    {
        foreach ($checkResults->toArray() as $key => $data)
        {
            foreach ($data as $key1 => $data1)
            {

                if (is_array($data1) && isset($data1[0]) && $data1[0] instanceof \Larawatch\Checks\CheckResult)
                {
                    LarawatchCheck::create([
                        'check_name' => $key1, 
                        'result_data' => $data1[0]->resultData ?? [],
                        'check_data' => $data1[0],
                        'result_message' => $data1[0]->resultMessage ?? null,
                        'started_at' => $data1[0]->started_at ?? now(),
                        'finished_at' => $data1[0]->ended_at ?? now(),
                        'result_status' => $data1[0]->resultStatus ?? null,

                    ]);
                }
                else
                {
                    LarawatchCheck::create(['check_name' => $key1, 'result_status' => 'skipped']);

                }
            }
        }
        return true;
    }

}
