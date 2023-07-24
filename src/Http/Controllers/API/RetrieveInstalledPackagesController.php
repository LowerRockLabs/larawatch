<?php

namespace Larawatch\Larawatch\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Composer\InstalledVersions;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RetrieveInstalledPackagesController extends Controller
{
    protected Request $request;

    protected function userIsVerified(): bool
    {
        $user = $this->request->user();

        return ! is_null($user) ? $user->hasVerifiedEmail() : false;
    }

    public function retrieve(Request $request)
    {

        /*$this->request = $request;
        if (! $this->userIsVerified()) {
            return response()->json([
                'error' => 'This is not an verified account.',
            ])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        }*/
        $composerInstance = new InstalledVersions;

        $dataArray = [
            'project_key' => config('larawatch.project_key'),
            'event_datetime' => now(),
            'installed_packages_rawdata' => $composerInstance->getAllRawData()[0]['versions'],
        ];
        $responseJson = json_encode($dataArray, true);
        $data = gzencode($responseJson, 9);

        return response($data)->withHeaders([
            'Content-type' => 'application/json; charset=utf-8',
            'Content-Length' => strlen($data),
            'Content-Encoding' => 'gzip',
        ]);
    }
}
