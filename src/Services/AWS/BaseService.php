<?php

namespace Stanliwise\CompreParkway\Services\AWS;

use Aws\CommandInterface;
use Aws\Middleware;
use Aws\Result;
use Aws\ResultInterface;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\Promise;
use Psr\Http\Message\RequestInterface;

abstract class BaseService
{
    /** @var \Aws\Rekognition\RekognitionClient */
    protected static $awsClient;

    public function getHttpClient()
    {
        if (!self::$awsClient) {
            self::$awsClient = new \Aws\Rekognition\RekognitionClient([
                'version' => 'latest',
                'region' => 'us-east-1',
                'credentials' => config('compreFace.aws_credentials')
            ]);

            self::$awsClient->getHandlerList()->appendSign(
                Middleware::mapResult(function (ResultInterface $result) {
                    if (app()->environment('testing'))
                        dump(json_encode($result->toArray()));
                    return $result;
                })
            );
        }

        return self::$awsClient;
    }
}
