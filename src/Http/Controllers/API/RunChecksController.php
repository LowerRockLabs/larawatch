<?php

namespace Larawatch\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Larawatch\Traits\Checks\RunsChecks;

class RunChecksController extends Controller
{
    use RunsChecks;

    protected Request $request;

    protected $validated = false;

    public function verify(Request $request)
    {
        $this->request = $request;
    }

    protected function runChecks()
    {
        if ($validated)
        {
            $this->generateChecklist();
            $this->executeChecks();
        }
    }

}
