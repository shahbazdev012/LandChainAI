<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default OCR Driver
    |--------------------------------------------------------------------------
    |
    | Which OCR engine to use for AI document verification. "tesseract" runs
    | the real Tesseract binary; "fake" returns seeded text and is intended
    | for tests / environments where the binary is not installed.
    |
    | Supported: "tesseract", "fake"
    */

    'driver' => env('OCR_DRIVER', 'tesseract'),

    'tesseract' => [
        'binary' => env('OCR_TESSERACT_BINARY', 'tesseract'),
    ],

    // Text returned by the "fake" driver when no response has been queued.
    'fake_text' => env('OCR_FAKE_TEXT', ''),

];
