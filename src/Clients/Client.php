<?php

namespace Cerpus\QuestionBankClient\Clients;

use Cerpus\QuestionBankClient\Contracts\QuestionBankClientContract;
use Cerpus\QuestionBankClient\DataObjects\OauthSetup;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;

/**
 * Class Client
 * @package Cerpus\QuestionBankClient\Clients
 */
class Client implements QuestionBankClientContract
{

    /**
     * @param  OauthSetup  $config
     * @return ClientInterface
     */
    public static function getClient(OauthSetup $config) :ClientInterface
    {
        $clientConfig = [
            'base_uri' => $config->baseUrl,
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json',
            ],
        ];

        if ($config->key ?? null && $config->secret ?? null) {
            $clientConfig[RequestOptions::HEADERS]['Authorization'] = $config->key.':'.$config->secret;
        }

        return new \GuzzleHttp\Client($clientConfig);
    }
}
