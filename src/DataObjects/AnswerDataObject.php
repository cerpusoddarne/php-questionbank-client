<?php

namespace Cerpus\QuestionBankClient\DataObjects;


use Cerpus\QuestionBankClient\Traits\CreateTrait;
use Cerpus\QuestionBankClient\Traits\MetadataTrait;

/**
 * Class AnswerDataObject
 * @package Cerpus\QuestionBankClient\DataObjects
 *
 * @method static AnswerDataObject create($attributes = null)
 */
class AnswerDataObject
{
    use CreateTrait, MetadataTrait;

    public $text, $id, $isCorrect, $questionId;
}