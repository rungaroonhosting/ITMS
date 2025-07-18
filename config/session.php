<?php
// config/session.php - ตรวจสอบการตั้งค่าเหล่านี้

return [
    // ใช้ 'file' หรือ 'database' สำหรับ local development
    'driver' => env('SESSION_DRIVER', 'file'),

    // Session lifetime ในนาที (2 ชั่วโมง = 120 นาที)
    'lifetime' => env('SESSION_LIFETIME', 120),

    'expire_on_close' => false,

    'encrypt' => false,

    'files' => storage_path('framework/sessions'),

    'connection' => env('SESSION_CONNECTION'),

    'table' => 'sessions',

    'store' => env('SESSION_STORE'),

    'lottery' => [2, 100],

    // สำคัญ: ต้องตั้งค่าให้ถูกต้องตาม domain
    'cookie' => env('SESSION_COOKIE', 'itms_session'),

    'path' => '/',

    // สำหรับ local development ใช้ null
    'domain' => env('SESSION_DOMAIN', null),

    // สำหรับ local development ใช้ false
    'secure' => env('SESSION_SECURE_COOKIE', false),

    'http_only' => true,

    'same_site' => 'lax',

    'partitioned' => false,
];

/*
สิ่งที่ต้องตรวจสอบใน .env:

SESSION_DRIVER=file
SESSION_LIFETIME=120
APP_URL=http://10.0.0.10
SESSION_COOKIE=itms_session
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=false
*/
