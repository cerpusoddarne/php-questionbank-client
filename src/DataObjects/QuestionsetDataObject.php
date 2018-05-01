<?php

namespace Cerpus\QuestionBankClient\DataObjects;


use Cerpus\QuestionBankClient\Traits\CreateTrait;
use Illuminate\Support\Collection;

class QuestionsetDataObject
{
    use CreateTrait;

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

    public function addQuestions(Collection $questions)
    {
        $questions->each(function ($question) {
            $this->addQuestion($question);
        });
    }

}