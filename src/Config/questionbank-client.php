<?php

use Cerpus\QuestionBankClient\Adapters\QuestionBankAdapter;

return [
    //default adapter for questionsets
    "default" => "questionbankservice",
    "adapters" => [
        "questionbankservice" => [
            "handler"     => QuestionBankAdapter::class,
            "base-url"    => env('QUESTIONBANK_BASE_URL'),
            "auth-user"   => env('QUESTIONBANK_AUTH_USER'),
            "auth-secret" => env('QUESTIONBANK_AUTH_SECRET'),
        ],
    ],
];
