<?php

namespace Cerpus\QuestionBankClient\Contracts;

use Cerpus\QuestionBankClient\DataObjects\AnswerDataObject;
use Cerpus\QuestionBankClient\DataObjects\QuestionDataObject;
use Cerpus\QuestionBankClient\DataObjects\QuestionsetDataObject;
use Illuminate\Support\Collection;

/**
 * Interface QuestionBankContract
 * @package Cerpus\QuestionBankClient\Contracts
 */
interface QuestionBankContract
{

    public function getQuestionsets(): Collection;

    public function getQuestionset($questionsetId) : QuestionsetDataObject;

    public function createQuestionset(QuestionsetDataObject $questionset);

    public function updateQuestionset(QuestionsetDataObject $questionset);

    public function deleteQuestionset($id);

    public function getQuestions($questionsetId): Collection;

    public function getQuestion($questionId);

    public function createQuestion(QuestionDataObject $question);

    public function updateQuestion(QuestionDataObject $question);

    public function deleteQuestion($questionId);

    public function getAnswer($answerId);

    public function getAnswersByQuestion($questionId);

    public function createAnswer(AnswerDataObject $answer);

    public function updateAnswer(AnswerDataObject $answer);

    public function deleteAnswer($answerId);
}