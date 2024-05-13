<?php

namespace  Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\File;
use Stanliwise\CompreParkway\Exceptions\NoFaceWasDetected;
use Stanliwise\CompreParkway\Services\ParkwayFaceTechService;
use Tests\App\Models\User;
use Tests\TestCase;

class FacialRecognitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_cannot_enroll_with_a_non_facial_image()
    {
        $this->expectException(NoFaceWasDetected::class);

        /** @var \Tests\App\Models\User */
        $user = User::factory()->create();

        ParkwayFaceTechService::instance()->enroll($user, new File(base_path('Images/1.png')));
    }

    public function test_a_user_can_enroll()
    {
    }
}
