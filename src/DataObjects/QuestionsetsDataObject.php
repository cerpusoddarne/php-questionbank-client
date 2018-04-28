<?php

namespace Cerpus\QuestionBankClient\DataObjects;


use Cerpus\QuestionBankClient\Traits\CreateTrait;

class QuestionsetsDataObject
{
    use CreateTrait;

    public $id, $metadata;

    private $questionsets;

    public function __construct()
    {
        $this->questionsets = collect();
    }

    public function addQuestionset(QuestionsetDataObject $questionset)
    {
        $this->questionsets->push($questionset);
    }

    public function getQuestionsets()
    {
        return $this->questionsets;
    }

}