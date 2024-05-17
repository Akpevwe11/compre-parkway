<?php

namespace Stanliwise\CompreParkway\Services\AWS;

use Aws\Middleware;
use Aws\ResultInterface;

abstract class BaseService
{
    /** @var \Aws\Rekognition\RekognitionClient */
    protected static $awsClient;

    public function getHttpClient()
    {
        if (! self::$awsClient) {
            self::$awsClient = new \Aws\Rekognition\RekognitionClient([
                'version' => 'latest',
                'region' => 'us-east-1',
                'credentials' => config('compreFace.aws_credentials'),
            ]);

            self::$awsClient->getHandlerList()->appendSign(
                Middleware::mapResult(function (ResultInterface $result) {
                    if (app()->environment('testing')) {
                        dump(json_encode($result->toArray()));
                    }

                    return $result;
                })
            );
        }

        return self::$awsClient;
    }
}
