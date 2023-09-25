<?php

namespace Larawatch\Http\Controllers\API;

use Illuminate\Http\Request;
use Larawatch\Traits\CheckResults\GetsCheckResults;
use Illuminate\Support\Facades\Log;
use Larawatch\Traits\Core\ProvidesCrypt;

class LarawatchCheckResultsController2
{
    use GetsCheckResults;
    use ProvidesCrypt;

    public $handshakedata;

    public function __construct(string $handshakedata = '')
    {
        $this->handshakedata = $handshakedata;
    }

    public function testHandshake(Request $request)
    {
        $test = $this->decryptPrivate(base64_decode(request()->handshakedata));
        return response()->json('TestHandshake  ' . $test);
    }

    /*public function testEncrypt(Request $request)
    {
        
        openssl_public_encrypt("Test 123123", $crypttext, base64_decode(config('larawatch.larawatch_public_key')));
        return base64_encode($crypttext);
    }

    public function testDecrypt($encrypted_string)
    {
        openssl_public_decrypt($encrypted_string, $decrypted_string, base64_decode(config('larawatch.larawatch_public_key')));
        return $decrypted_string;

    }*/

    public function listruns(Request $request)
    {
        if ($this->getUnsubmittedRunIDs())
        {
            return 'test';
        }
        return response()->json('no-results');

    }

    public function getRunByID(string $runID = '')
    {
        if ($this->getResultsByRunId($runID))
        {
            return response()->json($this->runResults);
        }
        
        return response()->json('no-results');
    }

    public function report(Request $request)
    {
        if ($this->getResultsFromLocalDB())
        {
            return response()->json($this->locallyStoredChecks);
        }
        
        return response()->json('no-results');
    }
}
