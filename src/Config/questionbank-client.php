<?php

return [
    //default adapter for questionsets
    "default" => "questionbankservice",

    "adapters" => [
        "questionbankservice" => [
            "handler" => \Cerpus\QuestionBankClient\Adapters\QuestionBankAdapter::class,
            "base-url" => env('QUESTIONBANK_SERVICE_URL'),
            "auth-client" => "none",
            "auth-url" => "",
            "auth-user" => "",
            "auth-secret" => "",
            "auth-token" => "",
            "auth-token_secret" => "",
            "key" => env('QUESTIONBANK_KEY'),
            "secret" => env('QUESTIONBANK_SECRET'),
        ],
    ],
];
