<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Stanliwise\CompreParkway\Adaptors\File\ImageFile;
use Stanliwise\CompreParkway\Exceptions\FaceDoesNotMatch;
use Stanliwise\CompreParkway\Facade\FaceTech;
use Tests\TestCase;

class FacialVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_same_user_face_can_be_compared()
    {
        $response = FaceTech::compareTwoFileImages(new ImageFile(base_path('Images/1.png')), new ImageFile(base_path('Images/8.jpg')));
        $this->assertIsArray($response);
    }

    public function test_two_difference_face_throws_error()
    {
        $this->expectException(FaceDoesNotMatch::class);
        FaceTech::compareTwoFileImages(new ImageFile(base_path('Images/1.png')), new ImageFile(base_path('Images/3.jpeg')));
    }
}
