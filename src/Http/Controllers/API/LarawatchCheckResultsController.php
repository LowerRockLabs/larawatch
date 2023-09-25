<?php

namespace Larawatch\Http\Controllers\API;

use Illuminate\Http\Request;
use Larawatch\Traits\CheckResults\GetsCheckResults;
use Illuminate\Support\Facades\Log;

class LarawatchCheckResultsController
{
    use GetsCheckResults;

    public function testHandshake(Request $request, $handshakedata)
    {
        Log::error("testHandshake");
        echo $handshakedata;
        return response()->json($request->all());
    }

    public function testEncrypt(Request $request)
    {
        $key = 
        <<<EOF
        -----BEGIN CERTIFICATE-----
        MIIFqjCCA5KgAwIBAgICEAAwDQYJKoZIhvcNAQELBQAwgcoxCzAJBgNVBAYTAkdC
        MRAwDgYDVQQIDAdFbmdsYW5kMRwwGgYDVQQKDBNMb3dlciBSb2NrIExhYnMgTHRk
        MTIwMAYDVQQLDClMb3dlciBSb2NrIExhYnMgTHRkIENlcnRpZmljYXRlIEF1dGhv
        cml0eTEsMCoGA1UEAwwjTG93ZXIgUm9jayBMYWJzIEx0ZCBJbnRlcm1lZGlhdGUg
        Q0ExKTAnBgkqhkiG9w0BCQEWGnNlY3VyaXR5QGxvd2Vycm9ja2xhYnMuY29tMB4X
        DTIzMDkxODE2MjIyM1oXDTI0MDkyNzE2MjIyM1owgZwxCzAJBgNVBAYTAkdCMRAw
        DgYDVQQIDAdFbmdsYW5kMQ8wDQYDVQQHDAZMb25kb24xHDAaBgNVBAoME0xvd2Vy
        IFJvY2sgTGFicyBMdGQxEjAQBgNVBAsMCUxhcmF3YXRjaDESMBAGA1UEAwwJTGFy
        YXdhdGNoMSQwIgYJKoZIhvcNAQkBFhVzdXBwb3J0QGxhcmF3YXRjaC5jb20wggEi
        MA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQDZSbE9EgG+q/HWPXOCXILAgQKt
        tugQXDDNJRPyLOqcvyhnv58EhQ7TnD9agJ3UJy9HrsGCgsNxFF+Oj7I+sFkMPFrq
        f3e7QPhBAxtpHWgr9jhU95VZN65DHEVyQSRHbh0OxN1pcVIpV2laJWPRO0SRMAzs
        j6IYBFP8hXE7vkaulh7uysHWhGaPMwBWfMMGug6NK6h9ikXmbDj+NdGAe7DhosBq
        3wwXt+YTmvOKcRofC2zXneZ7/3CJVFUI47K2IF2u+pxNA0CrNY7DLkNE3C8Va54T
        cOthST53ggy2NAsGi39moFWNdd7LfVusVnCBgiFQC9hCcAgAqSC1Lr/Mib+FAgMB
        AAGjgcUwgcIwCQYDVR0TBAIwADARBglghkgBhvhCAQEEBAMCBaAwMwYJYIZIAYb4
        QgENBCYWJE9wZW5TU0wgR2VuZXJhdGVkIENsaWVudCBDZXJ0aWZpY2F0ZTAdBgNV
        HQ4EFgQUBpmmNmNgT7pEMdjYKZk1sczOd4wwHwYDVR0jBBgwFoAUq5OBDv5hc9li
        aKPjVv1yG0HsFMMwDgYDVR0PAQH/BAQDAgXgMB0GA1UdJQQWMBQGCCsGAQUFBwMC
        BggrBgEFBQcDBDANBgkqhkiG9w0BAQsFAAOCAgEAJOZTIzXPw0vvMnxTVWg/omzU
        s6VgBcU6uwgu44SxsBCdxkhnDSlPYkSauSDLB1nn8D2oZL+QTpnmn33bPnGWSj1e
        2oq2Kyz9Vi4JieSgEA3ZuMI6QO6l8m8dwa8b2zvA5YPewdn1Wuq5L+uLOP8dBZ6F
        rBn0O0mnQqnwkkXT0SrzcP8vhvyJNTMdjFEmLjhi3aiD68sa4kuf0dFdWUaoa41V
        259/yoL9PJPffKs1Eih5PjH6KmG9v5nfPz9r6EUknK4GCuYxwjbyC4dyf5iEStib
        tmrrELKUKuLOCLlGnlw6/pmW3NqV1j/qVEe/91gEQCeZrRl/2xLrGavwEa6qDnwe
        m6Wr+Zqwjk3cpsGK31ptr+ifOhkc1DW48iFbs8hDqtrHVd4TiUBdssjoEtrxbj2e
        M6lJirBaBlYhiZVJdRBqNnooXc2wgt8LtBxE/tCV4gsfrNPXG7c1uXnHmKBCUCyc
        TFBDq/Su0VjSfLzFxfjrkxge4yzPF08ycT8HoqXH/66si9ywSPCw9PdMa+HehUsv
        a7ssVpJY84qo4rPGuS9kNRgycMYaih2M2c+Td9JbLGbWMHlyEKvoDlTkEDNDy8Jq
        V6LXvTQOdMdnB2nwNpd8+8rzxIx7+9ZNAdYyO9mMj2X1C0BxKdiMhH6c5TGSOMhR
        cHzyYNBp6W79MPzNNhE=
        -----END CERTIFICATE-----
        EOF;

        openssl_public_encrypt("Test 123123", $crypttext, $key);
        return base64_encode($crypttext);
    }

    public function listruns(Request $request)
    {        
        if ($this->getUnsubmittedRunIDs())
        {
            return response()->json([...$this->unsubmittedRunIDs, ...['test']]);
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
