<?php

namespace  Tests\Unit;

use Aws\CommandInterface;
use Aws\Result;
use GuzzleHttp\Promise\Create;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Psr\Http\Message\RequestInterface;
use Stanliwise\CompreParkway\Adaptors\AwsFacialAdaptor;
use Stanliwise\CompreParkway\Adaptors\File\ImageFile;
use Stanliwise\CompreParkway\Exceptions\FaceDoesNotMatch;
use Stanliwise\CompreParkway\Exceptions\NoFaceWasDetected;
use Stanliwise\CompreParkway\Services\ParkwayFaceTechService;
use Tests\App\Models\User;
use Tests\TestCase;

class FacialRecognitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_enroll_successful()
    {
        /** @var \Tests\App\Models\User */
        $user = User::factory()->create();

        $response = ParkwayFaceTechService::driver(AwsFacialAdaptor::class)->facialRecognitionService()->enrollSubject($user);

        $this->assertTrue($response);
    }

    public function test_primary_sample_can_be_added_to_enrolled_user()
    {
        /** @var \Tests\App\Models\User */
        $user = User::factory()->create();

        $response = ParkwayFaceTechService::driver(AwsFacialAdaptor::class)
            ->facialRecognitionService()
            ->addImage($user, new ImageFile(base_path('Images/1.png')));

        $this->assertIsArray($response);
    }

    public function test_a_non_face_cannot_be_added()
    {
        $this->expectException(NoFaceWasDetected::class);

        $mock_payload = '{"FaceRecords":[],"FaceModelVersion":"7.0","UnindexedFaces":[],"@metadata":{"statusCode":200,"effectiveUri":"https:\/\/rekognition.us-east-1.amazonaws.com","headers":{"x-amzn-requestid":"f08e708b-5adf-4e62-aa9b-1850243493a4","content-type":"application\/x-amz-json-1.1","content-length":"63","date":"Tue, 14 May 2024 23:50:26 GMT"},"transferStats":{"http":[[]]}}}';

        /** @var \Tests\App\Models\User */
        $user = User::factory()->create();

        /** @var \Stanliwise\CompreParkway\Services\AWS\FaceRecognitionService */
        $service = ParkwayFaceTechService::driver(AwsFacialAdaptor::class)->facialRecognitionService();

        $service->getHttpClient()->getHandlerList()->setHandler(function (CommandInterface $cmd, RequestInterface $request) use ($mock_payload) {
            $result = new Result(json_decode($mock_payload, true));
            return Create::promiseFor($result);
        });

        $service->addImage($user, new ImageFile(base_path('Images/2.png')));
    }

    public function test_a_different_face_cannot_be_added_to_a_user_image()
    {

        $this->expectException(FaceDoesNotMatch::class);

        $mock_payload = '{"FaceRecords":[{"Face":{"FaceId":"4dc3a127-84f6-4364-849c-8862dee7e38a","BoundingBox":{"Width":0.23503491282463074,"Height":0.38967081904411316,"Left":0.566633403301239,"Top":0.17438524961471558},"ImageId":"6c14659d-bd20-3418-8f11-73209fb863c5","ExternalImageId":"3.jpeg1","Confidence":99.99736785888672},"FaceDetail":{"BoundingBox":{"Width":0.23503491282463074,"Height":0.38967081904411316,"Left":0.566633403301239,"Top":0.17438524961471558},"AgeRange":{"Low":19,"High":27},"Smile":{"Value":false,"Confidence":99.2002944946289},"Eyeglasses":{"Value":false,"Confidence":99.98595428466797},"Sunglasses":{"Value":false,"Confidence":100},"Gender":{"Value":"Female","Confidence":99.96739196777344},"Beard":{"Value":false,"Confidence":99.53368377685547},"Mustache":{"Value":false,"Confidence":99.99979400634766},"EyesOpen":{"Value":true,"Confidence":97.7037582397461},"MouthOpen":{"Value":false,"Confidence":98.8211441040039},"Emotions":[{"Type":"SAD","Confidence":98.1689453125},{"Type":"CONFUSED","Confidence":4.7698974609375},{"Type":"FEAR","Confidence":1.97296142578125},{"Type":"CALM","Confidence":0.5512237548828125},{"Type":"ANGRY","Confidence":0.020396709442138672},{"Type":"DISGUSTED","Confidence":0.011986494064331055},{"Type":"SURPRISED","Confidence":0.010356307029724121},{"Type":"HAPPY","Confidence":0.00031391781521961093}],"Landmarks":[{"Type":"eyeLeft","X":0.6584072709083557,"Y":0.3280337154865265},{"Type":"eyeRight","X":0.7374258041381836,"Y":0.3799661099910736},{"Type":"mouthLeft","X":0.6164429783821106,"Y":0.45263975858688354},{"Type":"mouthRight","X":0.6824250221252441,"Y":0.49535730481147766},{"Type":"nose","X":0.6834875345230103,"Y":0.43856799602508545},{"Type":"leftEyeBrowLeft","X":0.635363757610321,"Y":0.2727711498737335},{"Type":"leftEyeBrowRight","X":0.6926341652870178,"Y":0.31020891666412354},{"Type":"leftEyeBrowUp","X":0.6700581908226013,"Y":0.28477540612220764},{"Type":"rightEyeBrowLeft","X":0.7378426194190979,"Y":0.34045013785362244},{"Type":"rightEyeBrowRight","X":0.7732095122337341,"Y":0.36379194259643555},{"Type":"rightEyeBrowUp","X":0.7612972855567932,"Y":0.34561869502067566},{"Type":"leftEyeLeft","X":0.6427684426307678,"Y":0.31534746289253235},{"Type":"leftEyeRight","X":0.6733537316322327,"Y":0.3390374779701233},{"Type":"leftEyeUp","X":0.6610032320022583,"Y":0.3227911591529846},{"Type":"leftEyeDown","X":0.656447172164917,"Y":0.333371102809906},{"Type":"rightEyeLeft","X":0.7210690379142761,"Y":0.37050020694732666},{"Type":"rightEyeRight","X":0.7501024007797241,"Y":0.3859199285507202},{"Type":"rightEyeUp","X":0.7404036521911621,"Y":0.3750866949558258},{"Type":"rightEyeDown","X":0.734879732131958,"Y":0.3848509192466736},{"Type":"noseLeft","X":0.6561309099197388,"Y":0.428436279296875},{"Type":"noseRight","X":0.68561851978302,"Y":0.44790253043174744},{"Type":"mouthUp","X":0.6605344414710999,"Y":0.46834850311279297},{"Type":"mouthDown","X":0.6445984840393066,"Y":0.5009847283363342},{"Type":"leftPupil","X":0.6584072709083557,"Y":0.3280337154865265},{"Type":"rightPupil","X":0.7374258041381836,"Y":0.3799661099910736},{"Type":"upperJawlineLeft","X":0.5921534895896912,"Y":0.26512715220451355},{"Type":"midJawlineLeft","X":0.5591059923171997,"Y":0.4081633985042572},{"Type":"chinBottom","X":0.6151939630508423,"Y":0.5530391335487366},{"Type":"midJawlineRight","X":0.7003102898597717,"Y":0.5019208788871765},{"Type":"upperJawlineRight","X":0.7653341293334961,"Y":0.3802599012851715}],"Pose":{"Roll":20.85784149169922,"Yaw":22.41314697265625,"Pitch":-25.047109603881836},"Quality":{"Brightness":85.96786499023438,"Sharpness":26.1773681640625},"Confidence":99.99736785888672,"FaceOccluded":{"Value":true,"Confidence":99.99897766113281},"EyeDirection":{"Yaw":1.2609881162643433,"Pitch":-4.213007926940918,"Confidence":99.85258483886719}}}],"FaceModelVersion":"7.0","UnindexedFaces":[],"@metadata":{"statusCode":200,"effectiveUri":"https:\/\/rekognition.us-east-1.amazonaws.com","headers":{"x-amzn-requestid":"3ff0152f-2daf-4875-8efe-e63678cf0c84","content-type":"application\/x-amz-json-1.1","content-length":"3836","date":"Wed, 15 May 2024 01:55:53 GMT"},"transferStats":[]}}';

        $mock_payload2 = '{"AssociatedFaces":[],"UnsuccessfulFaceAssociations":[{"FaceId":"4dc3a127-84f6-4364-849c-8862dee7e38a","Confidence":0.07481203973293304,"Reasons":["LOW_MATCH_CONFIDENCE"]}],"UserStatus":"ACTIVE","@metadata":{"statusCode":200,"effectiveUri":"https:\/\/rekognition.us-east-1.amazonaws.com","headers":{"x-amzn-requestid":"199c77f3-d7ff-437b-8e08-7c20d6300e08","content-type":"application\/x-amz-json-1.1","content-length":"195","date":"Wed, 15 May 2024 01:55:54 GMT"},"transferStats":[]}}';



        /** @var \Tests\App\Models\User */
        $user = User::factory()->create();

        /** @var \Stanliwise\CompreParkway\Services\AWS\FaceRecognitionService */
        $service = ParkwayFaceTechService::driver(AwsFacialAdaptor::class)->facialRecognitionService();

        $service->getHttpClient()->getHandlerList()->setHandler(function (CommandInterface $cmd, RequestInterface $request) use ($mock_payload, $mock_payload2) {
            $mock_to_use = str_contains($request->getBody()->__toString(), 'ExternalImageId') ? $mock_payload : $mock_payload2;
            $result = new Result(json_decode($mock_to_use, true));
            return Create::promiseFor($result);
        });

        $service->addImage($user, new ImageFile(base_path('Images/3.jpeg')));
    }

    public function test_user_same_image_can_be_associated_to_existing_user()
    {
        $mock_payload = '{"FaceRecords":[{"Face":{"FaceId":"1f076875-0b0f-42ea-991e-bb2d8491864e","BoundingBox":{"Width":0.31575268507003784,"Height":0.31331467628479004,"Left":0.35431528091430664,"Top":0.15663686394691467},"ImageId":"c5e3086e-51b5-3c23-896b-fdce20ad7002","ExternalImageId":"8.jpg1","Confidence":99.99942779541016},"FaceDetail":{"BoundingBox":{"Width":0.31575268507003784,"Height":0.31331467628479004,"Left":0.35431528091430664,"Top":0.15663686394691467},"AgeRange":{"Low":30,"High":38},"Smile":{"Value":false,"Confidence":92.66717529296875},"Eyeglasses":{"Value":false,"Confidence":99.91494750976562},"Sunglasses":{"Value":false,"Confidence":99.97491455078125},"Gender":{"Value":"Male","Confidence":100},"Beard":{"Value":true,"Confidence":96.98352813720703},"Mustache":{"Value":false,"Confidence":80.9249496459961},"EyesOpen":{"Value":false,"Confidence":76.64505004882812},"MouthOpen":{"Value":false,"Confidence":97.85908508300781},"Emotions":[{"Type":"CALM","Confidence":100},{"Type":"CONFUSED","Confidence":0.001341104507446289},{"Type":"HAPPY","Confidence":0.001188119174912572},{"Type":"SAD","Confidence":0.0005483627319335938},{"Type":"DISGUSTED","Confidence":1.1920928955078125e-5},{"Type":"ANGRY","Confidence":0},{"Type":"FEAR","Confidence":0},{"Type":"SURPRISED","Confidence":0}],"Landmarks":[{"Type":"eyeLeft","X":0.4458776116371155,"Y":0.2757624089717865},{"Type":"eyeRight","X":0.5912837982177734,"Y":0.27771660685539246},{"Type":"mouthLeft","X":0.46105730533599854,"Y":0.3849743604660034},{"Type":"mouthRight","X":0.5816850662231445,"Y":0.38643667101860046},{"Type":"nose","X":0.527555525302887,"Y":0.31030699610710144},{"Type":"leftEyeBrowLeft","X":0.3890499770641327,"Y":0.25751620531082153},{"Type":"leftEyeBrowRight","X":0.4782029986381531,"Y":0.23453626036643982},{"Type":"leftEyeBrowUp","X":0.4346678853034973,"Y":0.23264695703983307},{"Type":"rightEyeBrowLeft","X":0.5614701509475708,"Y":0.23552392423152924},{"Type":"rightEyeBrowRight","X":0.6412588953971863,"Y":0.260568767786026},{"Type":"rightEyeBrowUp","X":0.6021976470947266,"Y":0.2346564680337906},{"Type":"leftEyeLeft","X":0.4191872477531433,"Y":0.2779815196990967},{"Type":"leftEyeRight","X":0.47459620237350464,"Y":0.27703019976615906},{"Type":"leftEyeUp","X":0.44571754336357117,"Y":0.26890528202056885},{"Type":"leftEyeDown","X":0.4468020796775818,"Y":0.28040364384651184},{"Type":"rightEyeLeft","X":0.5624244213104248,"Y":0.27821022272109985},{"Type":"rightEyeRight","X":0.6157258152961731,"Y":0.2804342210292816},{"Type":"rightEyeUp","X":0.5915619730949402,"Y":0.270779013633728},{"Type":"rightEyeDown","X":0.5904352068901062,"Y":0.28220874071121216},{"Type":"noseLeft","X":0.4955739378929138,"Y":0.337234765291214},{"Type":"noseRight","X":0.5489159822463989,"Y":0.33777138590812683},{"Type":"mouthUp","X":0.5234885215759277,"Y":0.3613932132720947},{"Type":"mouthDown","X":0.5231003165245056,"Y":0.39786937832832336},{"Type":"leftPupil","X":0.4458776116371155,"Y":0.2757624089717865},{"Type":"rightPupil","X":0.5912837982177734,"Y":0.27771660685539246},{"Type":"upperJawlineLeft","X":0.34901759028434753,"Y":0.31278184056282043},{"Type":"midJawlineLeft","X":0.3826228082180023,"Y":0.4282442033290863},{"Type":"chinBottom","X":0.5207304954528809,"Y":0.4658108949661255},{"Type":"midJawlineRight","X":0.6382737755775452,"Y":0.43095850944519043},{"Type":"upperJawlineRight","X":0.6652990579605103,"Y":0.31644243001937866}],"Pose":{"Roll":-1.1336493492126465,"Yaw":3.416773557662964,"Pitch":22.695974349975586},"Quality":{"Brightness":69.48908996582031,"Sharpness":95.51618957519531},"Confidence":99.99942779541016,"FaceOccluded":{"Value":false,"Confidence":99.71582794189453},"EyeDirection":{"Yaw":-5.23605489730835,"Pitch":-24.537891387939453,"Confidence":99.52096557617188}}}],"FaceModelVersion":"7.0","UnindexedFaces":[],"@metadata":{"statusCode":200,"effectiveUri":"https:\/\/rekognition.us-east-1.amazonaws.com","headers":{"x-amzn-requestid":"c5766a31-a9eb-4245-8fe1-d1a4158d17fa","content-type":"application\/x-amz-json-1.1","content-length":"3806","date":"Wed, 15 May 2024 02:16:36 GMT"},"transferStats":[]}}';

        $mock_payload2 = '{"AssociatedFaces":[{"FaceId":"1f076875-0b0f-42ea-991e-bb2d8491864e"}],"UnsuccessfulFaceAssociations":[],"UserStatus":"UPDATING","@metadata":{"statusCode":200,"effectiveUri":"https:\/\/rekognition.us-east-1.amazonaws.com","headers":{"x-amzn-requestid":"a75100b4-6ea2-452a-b37d-2bb43927d9b3","content-type":"application\/x-amz-json-1.1","content-length":"129","date":"Wed, 15 May 2024 02:16:37 GMT"},"transferStats":[]}}';


        /** @var \Tests\App\Models\User */
        $user = User::factory()->create();

        /** @var \Stanliwise\CompreParkway\Services\AWS\FaceRecognitionService */
        $service = ParkwayFaceTechService::driver(AwsFacialAdaptor::class)->facialRecognitionService();

        $service->getHttpClient()->getHandlerList()->setHandler(function (CommandInterface $cmd, RequestInterface $request) use ($mock_payload, $mock_payload2) {
            $mock_to_use = str_contains($request->getBody()->__toString(), 'ExternalImageId') ? $mock_payload : $mock_payload2;
            $result = new Result(json_decode($mock_to_use, true));
            return Create::promiseFor($result);
        });

        $response = $service->addImage($user, new ImageFile(base_path('Images/8.jpg')));

        $this->assertArrayHasKey('image_uuid', $response);
    }

    public function test_view_all_users()
    {

        /** @var \Stanliwise\CompreParkway\Services\AWS\FaceRecognitionService */
        $service = ParkwayFaceTechService::driver(AwsFacialAdaptor::class)->facialRecognitionService();

        $service->listUsers();
    }


    public function test_view_all_faces()
    {
        $mock_payload = '{"Faces":[{"FaceId":"9179010f-4a89-4079-8a2b-32b9036cc5f9","BoundingBox":{"Width":0.2350350022315979,"Height":0.3896709978580475,"Left":0.5666329860687256,"Top":0.17438499629497528},"ImageId":"6c14659d-bd20-3418-8f11-73209fb863c5","ExternalImageId":"3.jpeg1","Confidence":99.99739837646484,"IndexFacesModelVersion":"7.0"},{"FaceId":"9b56cb4d-c482-4351-98b2-e9354ed2fe8e","BoundingBox":{"Width":0.5919989943504333,"Height":0.571619987487793,"Left":0.20948100090026855,"Top":0.2924500107765198},"ImageId":"e1ddf04b-c704-3428-9bfb-684fb7801714","ExternalImageId":"1.png1","Confidence":99.99960327148438,"IndexFacesModelVersion":"7.0","UserId":"1"}],"FaceModelVersion":"7.0","@metadata":{"statusCode":200,"effectiveUri":"https:\/\/rekognition.us-east-1.amazonaws.com","headers":{"x-amzn-requestid":"a5ebc28c-488d-4888-b9a0-92a7a1d32d9a","content-type":"application\/x-amz-json-1.1","content-length":"671","date":"Wed, 15 May 2024 01:01:07 GMT"},"transferStats":[]}}';


        /** @var \Tests\App\Models\User */
        $user = User::factory()->create();

        /** @var \Stanliwise\CompreParkway\Services\AWS\FaceRecognitionService */
        $service = ParkwayFaceTechService::driver(AwsFacialAdaptor::class)->facialRecognitionService();

        $service->listFaces();
    }

    public function test_remove_face()
    {

        $mock_payload = '{"DeletedFaces":["9179010f-4a89-4079-8a2b-32b9036cc5f9"],"UnsuccessfulFaceDeletions":[],"@metadata":{"statusCode":200,"effectiveUri":"https:\/\/rekognition.us-east-1.amazonaws.com","headers":{"x-amzn-requestid":"374134c8-ab2a-4605-98ef-1fe68a77bbc1","content-type":"application\/x-amz-json-1.1","content-length":"88","date":"Wed, 15 May 2024 01:12:01 GMT"},"transferStats":[]}}';


        /** @var \Stanliwise\CompreParkway\Services\AWS\FaceRecognitionService */
        $service = ParkwayFaceTechService::driver(AwsFacialAdaptor::class)->facialRecognitionService();

        $service->removeFace('9179010f-4a89-4079-8a2b-32b9036cc5f9');
    }
}
