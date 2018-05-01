<?php

namespace Cerpus\QuestionBankClient\DataObjects;


use Cerpus\QuestionBankClient\Traits\CreateTrait;
use Cerpus\QuestionBankClient\Traits\MetadataTrait;
use Illuminate\Support\Collection;

class QuestionsetsDataObject extends BaseDataObject
{
    use CreateTrait, MetadataTrait;

    public $id;

    private $questionsets;

    public $guarded = ['questionsets', 'metadata'];

    public function __construct()
    {
        $this->questionsets = collect();
    }

    public function addQuestionset(QuestionsetDataObject $questionset)
    {
        $this->questionsets->push($questionset);
    }

    public function addQuestionsets(Collection $questionsets)
    {
        $questionsets->each(function ($questionset) {
            $this->addQuestionset($questionset);
        });
    }

    public function getQuestionsets()
    {
        return $this->questionsets;
    }

}