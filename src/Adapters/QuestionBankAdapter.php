<?php

namespace Cerpus\QuestionBankClient\Adapters;

use Cerpus\QuestionBankClient\Contracts\QuestionBankContract;
use Cerpus\QuestionBankClient\DataObjects\AnswerDataObject;
use Cerpus\QuestionBankClient\DataObjects\QuestionDataObject;
use Cerpus\QuestionBankClient\DataObjects\QuestionsetDataObject;
use GuzzleHttp\ClientInterface;
use Illuminate\Http\Response;
use Log;

/**
 * Class QuestionBankAdapter
 * @package Cerpus\QuestionBankClient\Adapters
 */
class QuestionBankAdapter implements QuestionBankContract
{
    /** @var ClientInterface */
    private $client;

    /** @var \Exception */
    private $error;

    const QUESTIONSETS = '/v1/question_sets';
    const QUESTIONSET = '/v1/question_sets/%s';
    const QUESTIONSET_QUESTIONSET = '/v1/question_set/%s/question_set';

    const QUESTION = '/v1/questions/%s';
    const QUESTIONS = '/v1/question_sets/%s/questions';

    const ANSWER = '/v1/answers/%s';
    const ANSWERS = '/v1/questions/%s/answers';

    /**
     * QuestionBankAdapter constructor.
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function getQuestionsets()
    {
        // TODO: Implement getQuestionsets() method.
    }

    public function getQuestionset($questionsetId)
    {
        // TODO: Implement getQuestionset() method.
    }

    public function createQuestionset(QuestionsetDataObject $questionset)
    {
        // TODO: Implement createQuestionset() method.
    }

    public function updateQuestionset(QuestionsetDataObject $questionset)
    {
        // TODO: Implement updateQuestionset() method.
    }

    public function deleteQuestionset($id)
    {
        // TODO: Implement deleteQuestionset() method.
    }

    public function getQuestionsetsByQuestion($questionId)
    {
        // TODO: Implement getQuestionsetsByQuestion() method.
    }

    public function getQuestion($questionId)
    {
        // TODO: Implement getQuestion() method.
    }

    public function createQuestion(QuestionDataObject $question)
    {
        // TODO: Implement createQuestion() method.
    }

    public function updateQuestion(QuestionDataObject $question)
    {
        // TODO: Implement updateQuestion() method.
    }

    public function deleteQuestion($questionId)
    {
        // TODO: Implement deleteQuestion() method.
    }

    public function getAnswer($answerId)
    {
        // TODO: Implement getAnswer() method.
    }

    public function getAnswersByQuestion($questionId)
    {
        // TODO: Implement getAnswersByQuestion() method.
    }

    public function createAnswer(AnswerDataObject $answer)
    {
        // TODO: Implement createAnswer() method.
    }

    public function updateAnswer(AnswerDataObject $answer)
    {
        // TODO: Implement updateAnswer() method.
    }

    public function deleteAnswer($answerId)
    {
        // TODO: Implement deleteAnswer() method.
    }
}