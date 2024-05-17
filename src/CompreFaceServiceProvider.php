<?php

namespace Stanliwise\CompreParkway;

use Illuminate\Support\ServiceProvider;
use Stanliwise\CompreParkway\Services\AWS\FaceDetectionService as AWSFaceDetectionService;
use Stanliwise\CompreParkway\Services\AWS\FaceRecognitionService as AWSFaceRecognitionService;
use Stanliwise\CompreParkway\Services\AWS\FaceVerificationService as AWSFaceVerificationService;
use Stanliwise\CompreParkway\Services\FaceDetectionService;
use Stanliwise\CompreParkway\Services\FaceRecognitionService;
use Stanliwise\CompreParkway\Services\FaceVerificationService;
use Stanliwise\CompreParkway\Services\ParkwayFaceTechService;

class CompreFaceServiceProvider extends ServiceProvider
{
    protected static $shouldMigrate = true;

    public function register()
    {
        if (! app()->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/config/compreFace.php', 'compreFace');
        }

        $this->app->singleton('compreFace.faceDetection', fn () => new FaceDetectionService);
        $this->app->singleton('compreFace.faceRecognition', fn () => new FaceRecognitionService);
        $this->app->singleton('compreFace.faceVerification', fn () => new FaceVerificationService);
        $this->app->singleton('compreFace.aws.faceDetection', fn () => new AWSFaceDetectionService);
        $this->app->singleton('compreFace.aws.faceRecognition', fn () => new AWSFaceRecognitionService);
        $this->app->singleton('compreFace.aws.faceVerification', fn () => new AWSFaceVerificationService);
        $this->app->singleton('parkwayFaceService', fn () => new ParkwayFaceTechService());
    }

    public function boot()
    {
        if (app()->runningInConsole()) {
            $this->publishes([__DIR__.'/config/compreFace.php' => config_path('compreFace.php')], 'compreface-config');

            if (self::$shouldMigrate) {
                return $this->loadMigrationsFrom(__DIR__.'/database/migrations');
            }
        }
    }

    public static function ignoreMigrations()
    {
        self::$shouldMigrate = false;
    }
}
