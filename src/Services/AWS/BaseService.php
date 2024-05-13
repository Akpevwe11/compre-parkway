<?php

namespace Stanliwise\CompreParkway\Services\AWS;

abstract class BaseService
{
    /** @var \Aws\Rekognition\RekognitionClient */
    public static $awsClient;

    public function getHttpClient()
    {
        if (!self::$awsClient)
            self::$awsClient = new \Aws\Rekognition\RekognitionClient([
                'version' => 'latest',
                'region' => 'us-east-1',
                'credentials' => config('compreFace.aws_credentials')
            ]);

        return self::$awsClient;
    }
}
