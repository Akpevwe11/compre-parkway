<?php

namespace  Tests\Unit;

use Aws\CommandInterface;
use Aws\Result;
use GuzzleHttp\Promise\Create;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Psr\Http\Message\RequestInterface;
use Stanliwise\CompreParkway\Adaptors\File\ImageFile;
use Stanliwise\CompreParkway\Services\ParkwayFaceTechService;
use Tests\App\Models\User;
use Tests\TestCase;

class ParkwayFaceTechServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_that_user_able_enroll_and_have_an_example_record()
    {
        $recognitionService = ParkwayFaceTechService::facialRecognitionService();

        $mock_payload = '{"FaceRecords":[{"Face":{"FaceId":"1f076875-0b0f-42ea-991e-bb2d8491864e","BoundingBox":{"Width":0.31575268507003784,"Height":0.31331467628479004,"Left":0.35431528091430664,"Top":0.15663686394691467},"ImageId":"c5e3086e-51b5-3c23-896b-fdce20ad7002","ExternalImageId":"8.jpg1","Confidence":99.99942779541016},"FaceDetail":{"BoundingBox":{"Width":0.31575268507003784,"Height":0.31331467628479004,"Left":0.35431528091430664,"Top":0.15663686394691467},"AgeRange":{"Low":30,"High":38},"Smile":{"Value":false,"Confidence":92.66717529296875},"Eyeglasses":{"Value":false,"Confidence":99.91494750976562},"Sunglasses":{"Value":false,"Confidence":99.97491455078125},"Gender":{"Value":"Male","Confidence":100},"Beard":{"Value":true,"Confidence":96.98352813720703},"Mustache":{"Value":false,"Confidence":80.9249496459961},"EyesOpen":{"Value":false,"Confidence":76.64505004882812},"MouthOpen":{"Value":false,"Confidence":97.85908508300781},"Emotions":[{"Type":"CALM","Confidence":100},{"Type":"CONFUSED","Confidence":0.001341104507446289},{"Type":"HAPPY","Confidence":0.001188119174912572},{"Type":"SAD","Confidence":0.0005483627319335938},{"Type":"DISGUSTED","Confidence":1.1920928955078125e-5},{"Type":"ANGRY","Confidence":0},{"Type":"FEAR","Confidence":0},{"Type":"SURPRISED","Confidence":0}],"Landmarks":[{"Type":"eyeLeft","X":0.4458776116371155,"Y":0.2757624089717865},{"Type":"eyeRight","X":0.5912837982177734,"Y":0.27771660685539246},{"Type":"mouthLeft","X":0.46105730533599854,"Y":0.3849743604660034},{"Type":"mouthRight","X":0.5816850662231445,"Y":0.38643667101860046},{"Type":"nose","X":0.527555525302887,"Y":0.31030699610710144},{"Type":"leftEyeBrowLeft","X":0.3890499770641327,"Y":0.25751620531082153},{"Type":"leftEyeBrowRight","X":0.4782029986381531,"Y":0.23453626036643982},{"Type":"leftEyeBrowUp","X":0.4346678853034973,"Y":0.23264695703983307},{"Type":"rightEyeBrowLeft","X":0.5614701509475708,"Y":0.23552392423152924},{"Type":"rightEyeBrowRight","X":0.6412588953971863,"Y":0.260568767786026},{"Type":"rightEyeBrowUp","X":0.6021976470947266,"Y":0.2346564680337906},{"Type":"leftEyeLeft","X":0.4191872477531433,"Y":0.2779815196990967},{"Type":"leftEyeRight","X":0.47459620237350464,"Y":0.27703019976615906},{"Type":"leftEyeUp","X":0.44571754336357117,"Y":0.26890528202056885},{"Type":"leftEyeDown","X":0.4468020796775818,"Y":0.28040364384651184},{"Type":"rightEyeLeft","X":0.5624244213104248,"Y":0.27821022272109985},{"Type":"rightEyeRight","X":0.6157258152961731,"Y":0.2804342210292816},{"Type":"rightEyeUp","X":0.5915619730949402,"Y":0.270779013633728},{"Type":"rightEyeDown","X":0.5904352068901062,"Y":0.28220874071121216},{"Type":"noseLeft","X":0.4955739378929138,"Y":0.337234765291214},{"Type":"noseRight","X":0.5489159822463989,"Y":0.33777138590812683},{"Type":"mouthUp","X":0.5234885215759277,"Y":0.3613932132720947},{"Type":"mouthDown","X":0.5231003165245056,"Y":0.39786937832832336},{"Type":"leftPupil","X":0.4458776116371155,"Y":0.2757624089717865},{"Type":"rightPupil","X":0.5912837982177734,"Y":0.27771660685539246},{"Type":"upperJawlineLeft","X":0.34901759028434753,"Y":0.31278184056282043},{"Type":"midJawlineLeft","X":0.3826228082180023,"Y":0.4282442033290863},{"Type":"chinBottom","X":0.5207304954528809,"Y":0.4658108949661255},{"Type":"midJawlineRight","X":0.6382737755775452,"Y":0.43095850944519043},{"Type":"upperJawlineRight","X":0.6652990579605103,"Y":0.31644243001937866}],"Pose":{"Roll":-1.1336493492126465,"Yaw":3.416773557662964,"Pitch":22.695974349975586},"Quality":{"Brightness":69.48908996582031,"Sharpness":95.51618957519531},"Confidence":99.99942779541016,"FaceOccluded":{"Value":false,"Confidence":99.71582794189453},"EyeDirection":{"Yaw":-5.23605489730835,"Pitch":-24.537891387939453,"Confidence":99.52096557617188}}}],"FaceModelVersion":"7.0","UnindexedFaces":[],"@metadata":{"statusCode":200,"effectiveUri":"https:\/\/rekognition.us-east-1.amazonaws.com","headers":{"x-amzn-requestid":"c5766a31-a9eb-4245-8fe1-d1a4158d17fa","content-type":"application\/x-amz-json-1.1","content-length":"3806","date":"Wed, 15 May 2024 02:16:36 GMT"},"transferStats":[]}}';

        $mock_payload2 = '{"AssociatedFaces":[{"FaceId":"1f076875-0b0f-42ea-991e-bb2d8491864e"}],"UnsuccessfulFaceAssociations":[],"UserStatus":"UPDATING","@metadata":{"statusCode":200,"effectiveUri":"https:\/\/rekognition.us-east-1.amazonaws.com","headers":{"x-amzn-requestid":"a75100b4-6ea2-452a-b37d-2bb43927d9b3","content-type":"application\/x-amz-json-1.1","content-length":"129","date":"Wed, 15 May 2024 02:16:37 GMT"},"transferStats":[]}}';

        /** @var \Aw */
        $client = $recognitionService->getHttpClient()->getHandlerList()->setHandler(function (CommandInterface $cmd, RequestInterface $request) use ($mock_payload, $mock_payload2) {
            $mock_to_use = str_contains($request->getBody()->__toString(), '"Image":{"Bytes"') ? $mock_payload : $mock_payload2;
            $mock_to_use = json_decode($mock_to_use, true);

            if (isset($mock_to_use['FaceRecords'][0]['Face']['FaceId']))
                $mock_to_use['FaceRecords'][0]['Face']['FaceId'] = (string) \Illuminate\Support\Str::uuid();

            $result = new Result($mock_to_use);
            return Create::promiseFor($result);
        });

        /** @var \Tests\App\Models\User */
        $user = User::factory()->create();

        /** @var \Stanliwise\CompreParkway\Services\ParkwayFaceTechService */
        $service = ParkwayFaceTechService::instance();

        $service->enroll($user, new ImageFile(base_path('images/1.png')));
        $this->assertDatabaseCount('examples', 1);
        $user->refresh();

        //
        $service->addSecondaryExample($user, new ImageFile(base_path('images/1.png')));

        $this->assertDatabaseCount('examples', 2);
    }
    public function test_same_user_face_can_be_compared()
    {
        $response = (new ParkwayFaceTechService())->compareTwoFileImages(new ImageFile(base_path("Images/2.png")), new ImageFile(base_path("Images/8.jpg")));
        $this->assertIsArray($response);
    }
}
