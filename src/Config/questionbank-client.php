<?php

return [
    "core" => [
        "url" => env("CERPUS_CORE_SERVER", env("CORECLIENT_CORE_SERVER")),
        "key" => env("CERPUS_CORE_KEY", env("CORECLIENT_CORE_KEY")),
        "secret" => env("CERPUS_CORE_SECRET", env("CORECLIENT_CORE_SECRET")),
        "token" => env("CERPUS_CORE_TOKEN", env("CORECLIENT_CORE_TOKEN")),
        "token_secret" => env("CERPUS_CORE_TOKEN_SECRET", env("CORECLIENT_CORE_TOKEN_SECRET")),
    ],

    "auth" => [
        "url" => env("CERPUS_AUTH_SERVER", env("CORECLIENT_AUTH_SERVER")),
        "user" => env("CERPUS_AUTH_USER", env("CORECLIENT_AUTH_USER")),
        "secret" => env("CERPUS_AUTH_SECRET", env("CORECLIENT_AUTH_SECRET")),
    ],

    "adapter" => [
        'current' => env("QUESTIONSETCLIENT_ADAPTER", \Cerpus\QuestionBankClient\Adapters\QuestionBankAdapter::class),
        'client' => env("QUESTIONSETCLIENT_SERVICE", \Cerpus\QuestionBankClient\Clients\Client::class),
    ]
];