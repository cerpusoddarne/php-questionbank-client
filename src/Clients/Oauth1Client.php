<?php

namespace Cerpus\QuestionBankClient\Clients;


use Cerpus\QuestionBankClient\Contracts\QuestionBankClientContract;
use Cerpus\QuestionBankClient\DataObjects\OauthSetup;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use GuzzleHttp\Client;

/**
 * Class Oauth1Client
 * @package Cerpus\QuestionBankClient\Clients
 */
class Oauth1Client implements QuestionBankClientContract
{

    /**
     * @param OauthSetup $config
     * @return ClientInterface
     */
    public static function getClient(OauthSetup $config): ClientInterface
    {
        $stack = HandlerStack::create();

        $middleware = new Oauth1([
            'consumer_key' => $config->key,
            'consumer_secret' => $config->secret,
            'token' => $config->token,
            'token_secret' => $config->tokenSecret,
        ]);

        $stack->push($middleware);

        return new Client([
            'base_uri' => $config->coreUrl,
            'handler' => $stack,
            RequestOptions::AUTH => 'oauth',
        ]);
    }
}