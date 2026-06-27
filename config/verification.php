<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Verification Scoring
    |--------------------------------------------------------------------------
    |
    | The document verifier runs a set of weighted checks against the OCR text.
    | Each check contributes up to its weight to a 0-100 confidence score. The
    | resulting score is mapped to a status using the thresholds below.
    |
    | Weights should sum to 100.
    */

    'weights' => [
        'ocr_quality' => 15,
        'property_number_match' => 30,
        'owner_name_match' => 30,
        'owner_cnic_match' => 25,
    ],

    'thresholds' => [
        'verified' => 80,   // score >= 80  => Verified
        'suspicious' => 50, // score >= 50  => Suspicious, otherwise Rejected
    ],

    // Minimum characters of extracted text for OCR to be considered usable.
    'min_ocr_length' => 40,

    /*
    |--------------------------------------------------------------------------
    | Asynchronous Verification
    |--------------------------------------------------------------------------
    |
    | When true, verification runs are pushed onto the queue instead of running
    | inside the request. Synchronous runs give instant feedback in the UI and
    | are the sensible default for single-document OCR.
    */

    'async' => env('VERIFICATION_ASYNC', false),

];
