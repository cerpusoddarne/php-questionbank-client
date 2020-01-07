<?php

namespace Cerpus\QuestionBankClient\DataObjects;


use Cerpus\Helper\Traits\CreateTrait;
use Cerpus\QuestionBankClient\Traits\MetadataTrait;
use Illuminate\Support\Collection;

/**
 * Class QuestionsetDataObject
 * @package Cerpus\QuestionBankClient\DataObjects
 *
 * @method static QuestionsetDataObject create($attributes = null)
 */
class QuestionsetDataObject extends BaseDataObject
{
    use CreateTrait, MetadataTrait;

    public $title, $id, $questionCount;

    private $questions;

    public $guarded = ['metadata', 'questions'];

    public function __construct()
    {
        $this->questionCount = 0;
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

    public function addMetadata(MetadataDataObject $metadata)
    {
        $this->metadata = $metadata;
    }

    public function getMetadata()
    {
        return $this->metadata;
    }

}
