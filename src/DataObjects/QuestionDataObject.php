<?php

namespace Cerpus\QuestionBankClient\DataObjects;


use Cerpus\QuestionBankClient\Traits\CreateTrait;
use Illuminate\Support\Collection;

class QuestionDataObject
{
    use CreateTrait;

    public $text, $questionSetId, $metadata, $id;

    private $answers;

    public function __construct()
    {
        $this->answers = collect();
    }

    /**
     * @return array
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    public function addAnswer(AnswerDataObject $answer)
    {
        $this->answers->push($answer);
    }

    public function addAnswers(Collection $answers)
    {
        $answers->each(function ($answer) {
            $this->addAnswer($answer);
        });
    }
}