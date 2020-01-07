<?php

namespace Cerpus\QuestionBankClient\DataObjects;


use Cerpus\Helper\Traits\CreateTrait;
use Cerpus\QuestionBankClient\Traits\MetadataTrait;
use Illuminate\Support\Collection;

/**
 * Class QuestionDataObject
 * @package Cerpus\QuestionBankClient\DataObjects
 *
 * @method static QuestionDataObject create($attributes = null)
 */
class QuestionDataObject extends BaseDataObject
{
    use CreateTrait, MetadataTrait;

    public $text, $questionSetId, $id;
    public $stripMathContainerElements = true;

    private $answers;

    public $guarded = ['answers', 'metadata'];

    public function __construct()
    {
        $this->answers = collect();
    }

    /**
     * @return Collection
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
