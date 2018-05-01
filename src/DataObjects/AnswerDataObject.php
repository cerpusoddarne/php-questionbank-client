<?php

namespace Cerpus\QuestionBankClient\DataObjects;


use Cerpus\QuestionBankClient\Traits\CreateTrait;
use Cerpus\QuestionBankClient\Traits\MetadataTrait;

class AnswerDataObject
{
    use CreateTrait, MetadataTrait;

    public $text, $id, $isCorrect, $questionId;
}