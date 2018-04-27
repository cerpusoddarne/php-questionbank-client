<?php

namespace Cerpus\QuestionBankClient\Contracts;

use Cerpus\QuestionBankClient\DataObjects\OauthSetup;
use GuzzleHttp\ClientInterface;

/**
 * Interface QuestionBankClientContract
 * @package Cerpus\CoreClient\Contracts
 */
interface QuestionBankClientContract
{
    /**
     * @param OauthSetup $config
     * @return ClientInterface
     */
    public static function getClient(OauthSetup $config): ClientInterface;
}