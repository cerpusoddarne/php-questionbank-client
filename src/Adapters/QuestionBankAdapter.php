<?php

namespace Cerpus\QuestionBankClient\Adapters;

use Cerpus\QuestionBankClient\Contracts\QuestionBankContract;
use Cerpus\QuestionBankClient\DataObjects\AnswerDataObject;
use Cerpus\QuestionBankClient\DataObjects\MetadataDataObject;
use Cerpus\QuestionBankClient\DataObjects\QuestionDataObject;
use Cerpus\QuestionBankClient\DataObjects\QuestionsetDataObject;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\Collection;

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

    const QUESTIONS = '/v1/question_sets/%s/questions';
    const QUESTION = '/v1/questions/%s';

    const ANSWERS = '/v1/questions/%s/answers';
    const ANSWER = '/v1/answers/%s';

    private $data;

    /**
     * QuestionBankAdapter constructor.
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    private function transformMetadata($metadata)
    {
        return MetadataDataObject::create([
            'keywords' => $metadata->keywords
        ]);
    }

    /**
     * @return array[QuestionsetDataObject]
     */
    public function getQuestionsets($includeQuestions = true): Collection
    {
        $response = $this->client->request("GET", self::QUESTIONSETS);
        $data = collect(\GuzzleHttp\json_decode($response->getBody()));
        $questionsets = $data->map(function ($questionset) {
            return QuestionsetDataObject::create([
                'id' => $questionset->id,
                'title' => $questionset->title,
                'metadata' => $this->transformMetadata($questionset->metadata),
            ]);
        });
        if ($includeQuestions === true) {
            $questionsets->each(function ($questionset) {
                /** @var QuestionsetDataObject add */
                $questionset->addQuestions($this->getQuestions($questionset->id));
            });
        }
        return $questionsets;
    }

    public function getQuestionset($questionsetId): QuestionsetDataObject
    {
        $questionset = collect($this->data)->filter(function ($questionset) use ($questionsetId) {
            return $questionset->id === $questionsetId;
        });
        if ($questionset->isNotEmpty()) {
            return $questionset->first();
        }
        throw new \Exception("Could not find your questionset");
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

    public function getQuestions($questionsetId): Collection
    {
        $response = $this->client->request("GET", sprintf(self::QUESTIONS, $questionsetId));
        $data = collect(\GuzzleHttp\json_decode($response->getBody()));
        return $data->map(function ($question) {
            $question = QuestionDataObject::create([
                'id' => $question->id,
                'text' => $question->title,
                'metadata' => $this->transformMetadata($question->metadata),
                'questionSetId' => $question->questionSetId,
            ]);
            $question->addAnswers($this->getAnswersByQuestion($question->id));
            return $question;
        });
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
        $response = $this->client->request("GET", sprintf(self::ANSWERS, $questionId));
        $data = collect(\GuzzleHttp\json_decode($response->getBody()));
        return $data->map(function ($answer) {
            $answer = AnswerDataObject::create([
                'id' => $answer->id,
                'text' => $answer->description,
                'metadata' => $this->transformMetadata($answer->metadata),
                'questionId' => $answer->questionId,
                'isCorrect' => intval($answer->correctness) === 100,
            ]);
            return $answer;
        });
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