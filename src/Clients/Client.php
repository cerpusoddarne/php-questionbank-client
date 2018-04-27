<?php

namespace Cerpus\QuestionBankClient\Clients;

use Cerpus\QuestionBankClient\DataObjects\OauthSetup;
use Cerpus\QuestionBankClient\Contracts\QuestionBankClientContract;
use GuzzleHttp\ClientInterface;

/**
 * Class Client
 * @package Cerpus\QuestionBankClient\Clients
 */
class Client implements QuestionBankClientContract
{

    /**
     * @param OauthSetup $config
     * @return ClientInterface
     */
    public static function getClient(OauthSetup $config): ClientInterface
    {
        return new \GuzzleHttp\Client([
            'base_uri' => $config->coreUrl,
        ]);
    }
}