<?php

namespace Cerpus\QuestionBankClient\DataObjects;


use Cerpus\QuestionBankClient\Traits\CreateTrait;

/**
 * Class OauthSetup
 * @package Cerpus\QuestionBankClient\DataObjects
 */
class OauthSetup
{
    use CreateTrait;

    /**
     * @var string $key
     * @var string $secret
     * @var string $url
     * @var string $authUrl
     * @var string $tokenSecret
     * @var string $token
     * @var string $key
     * @var string $secret
     */
    public $baseUrl, $authKey, $authSecret, $authUrl, $authTokenSecret, $authToken, $key, $secret;
}
