<?php
return [
    'base_url' => env('COMPREFACE_BASE_URL'),
    'verification_api_key' => env('COMPREFACE_VERIFY_API_KEY'),
    'detection_api_key' => env('COMPREFACE_DETECT_API_KEY'),
    'recognition_api_key' => env('COMPREFACE_RECOGNITION_API_KEY'),
    'image_directory' => env('COMPREFACE_SAVE_DIRECTORY'),
    'storage_drive' => env('COMPREFACE_STORAGE_DRIVE'),
    'trust_threshold' => env('COMPREFACE_TRUST_THRESHOLD'),
    'driver' => env('FACE_DRIVER', 'compreFace'),
    'aws_collection_id' => '',
    'aws_credentials' => [
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'key' => env('AWS_ACCESS_KEY_ID')
    ]
];
