<?php
return [
    'driver' => env('COMPREFACE_DRIVER', 'compreFace'),
    'driver_name' => 'aws',
    'base_url' => env('COMPREFACE_BASE_URL'),
    'verification_api_key' => env('COMPREFACE_VERIFY_API_KEY'),
    'detection_api_key' => env('COMPREFACE_DETECT_API_KEY'),
    'recognition_api_key' => env('COMPREFACE_RECOGNITION_API_KEY'),
    'image_directory' => env('COMPREFACE_SAVE_DIRECTORY'),
    'storage_drive' => env('COMPREFACE_STORAGE_DRIVE'),
    'trust_threshold' => env('COMPREFACE_TRUST_THRESHOLD'),
    'aws_collection_id' => env('AWS_COLLECTION_ID'),
    'aws_credentials' => [
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'key' => env('AWS_ACCESS_KEY_ID')
    ]
];
