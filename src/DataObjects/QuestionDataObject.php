<?php

namespace Cerpus\QuestionBankClient\DataObjects;


class QuestionDataObject
{
    public $text, $questionSetId, $metadata, $id;

    private $answers;

    public function __construct()
    {
        $this->answers = collect();
    }

    /**
     * @return array
     */
    public function getAnswers(): array
    {
        return $this->answers;
    }

    public function addAnswer(AnswerDataObject $answer)
    {
        $this->answers->push($answer);
    }
}