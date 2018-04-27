<?php

namespace Cerpus\QuestionBankClient\DataObjects;


class QuestionsetDataObject
{

    public $title, $id, $metadata;

    private $questions;

    public function __construct()
    {
        $this->questions = collect();
    }

    public function addQuestion(QuestionDataObject $question)
    {
        $this->questions->push($question);
    }

    public function getQuestions()
    {
        return $this->questions;
    }

}