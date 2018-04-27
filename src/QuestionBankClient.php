<?php

namespace Cerpus\QuestionBankClient;

use Cerpus\QuestionBankClient\Contracts\QuestionBankContract;
use Illuminate\Support\Facades\Facade;

/**
 * Class QuestionBankClient
 * @package Cerpus\QuestionBankClient
 */
class QuestionBankClient extends Facade
{

    protected $defer = true;

    /**
     * @var string
     */
    static $alias = "questionbank-client";

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return QuestionBankContract::class;
    }

    /**
     * @return string
     */
    public static function getBasePath()
    {
        return dirname(__DIR__);
    }

    /**
     * @return string
     */
    public static function getConfigPath()
    {
        return self::getBasePath() . '/src/Config/questionbank-client.php';
    }
}