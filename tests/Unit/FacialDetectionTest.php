<?php

namespace  Tests\Unit;

use Stanliwise\CompreParkway\Adaptors\AwsFacialAdaptor;
use Stanliwise\CompreParkway\Adaptors\File\ImageFile;
use Stanliwise\CompreParkway\Exceptions\NoFaceWasDetected;
use Stanliwise\CompreParkway\Services\ParkwayFaceTechService;
use Tests\TestCase;

class FacialDetectionTest extends TestCase
{

    public function test_a_user_cannot_use_an_image_with_no_face()
    {
        $this->expectException(NoFaceWasDetected::class);
        ParkwayFaceTechService::driver(AwsFacialAdaptor::class)->facialDetectionService()->detectFileImage(new ImageFile(base_path('Images/7.jpeg')));
    }

    public function test_face_detection_works_with_image_with_face()
    {
        $response = ParkwayFaceTechService::driver(AwsFacialAdaptor::class)->facialDetectionService()->detectFileImage(new ImageFile(base_path('Images/1.png')));
        $this->assertIsArray($response);
    }
}
