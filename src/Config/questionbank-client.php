<?php

return [
    //default adapter for questionsets
    "default" => "questionbankservice",

    "adapters" => [

        "questionbankservice" => [
            "handler" => \Cerpus\QuestionBankClient\Adapters\QuestionBankAdapter::class,
            "base-url" => "",
            "auth-client" => "none",
            "auth-url" => "",
            "auth-user" => "",
            "auth-secret" => "",
            "auth-token" => "",
            "auth-token_secret" => "",
        ],

    ],
];