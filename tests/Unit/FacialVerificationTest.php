<?php

namespace  Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\File;
use Stanliwise\CompreParkway\Adaptors\AwsFacialAdaptor;
use Stanliwise\CompreParkway\Exceptions\FaceDoesNotMatch;
use Stanliwise\CompreParkway\Services\ParkwayFaceTechService;
use Tests\TestCase;

class FacialVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_same_user_face_can_be_compared()
    {
        $response = ParkwayFaceTechService::driver(AwsFacialAdaptor::class)->facialVerificationService()->compareTwoFileImages(new File(base_path("Images/1.png")), new File(base_path("Images/8.jpg")));
        $this->assertIsArray($response);
    }

    public function test_two_difference_face_throws_error()
    {
        $this->expectException(FaceDoesNotMatch::class);
        ParkwayFaceTechService::driver(AwsFacialAdaptor::class)->facialVerificationService()->compareTwoFileImages(new File(base_path("Images/1.png")), new File(base_path("Images/3.jpeg")));
    }
}
