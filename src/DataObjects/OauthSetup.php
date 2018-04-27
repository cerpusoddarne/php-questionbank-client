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
     * @var string $coreUrl
     * @var string $authUrl
     * @var string $tokenSecret
     * @var string $token
     */
    public $key, $secret, $coreUrl, $authUrl, $tokenSecret, $token;
}