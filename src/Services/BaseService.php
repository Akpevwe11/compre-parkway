<?php

namespace Stanliwise\CompreParkway\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

abstract class BaseService
{
    protected function getHttpClient()
    {
        return Http::acceptJson()->baseUrl(config('compreFace.base_url'));
    }

    protected function getPlugings()
    {
        return implode(',', ['age', 'gender', 'landmarks', 'mask', 'pose']);
    }

    protected function handleFaceHttpResponse(Response $response): array
    {
        try {
            $response->throwIf($response->failed());

            return $response->json();
        } catch (ConnectionException $e) {
            throw $e;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
