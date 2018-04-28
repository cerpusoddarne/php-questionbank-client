<?php

namespace Cerpus\QuestionBankClient\DataObjects;


use Cerpus\QuestionBankClient\Traits\CreateTrait;

class AnswerDataObject
{
    use CreateTrait;

    public $text, $id, $isCorrect, $questionId, $metadata;
}