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

    private function mapQuestionsetResponseToDataObject($questionValues)
    {
        $questionset = QuestionsetDataObject::create([
            'id' => $questionValues->id,
            'title' => $questionValues->title,
        ]);
        $questionset->addMetadata($this->transformMetadata($questionValues->metadata));
        return $questionset;
    }

    private function mapQuestionResponseToDataObject($questionValues)
    {
        $question = QuestionDataObject::create([
            'id' => $questionValues->id,
            'text' => $questionValues->title,
            'questionSetId' => $questionValues->questionSetId,
        ]);
        $question->addMetadata($this->transformMetadata($questionValues->metadata));
        return $question;
    }

    private function mapAnswerResponseToDataObject($answerValues)
    {
        $answer = AnswerDataObject::create([
            'id' => $answerValues->id,
            'text' => $answerValues->description,
            'questionId' => $answerValues->questionId,
            'isCorrect' => intval($answerValues->correctness) === 100,
        ]);
        $answer->addMetadata($this->transformMetadata($answerValues->metadata));
        return $answer;
    }

    /**
     * @return Collection[QuestionsetDataObject]
     */
    public function getQuestionsets($includeQuestions = true): Collection
    {
        $response = $this->client->request("GET", self::QUESTIONSETS);
        $data = collect(\GuzzleHttp\json_decode($response->getBody()));
        $questionsets = $data->map(function ($questionset) {
            return $this->mapQuestionsetResponseToDataObject($questionset);
        });
        if ($includeQuestions === true) {
            $questionsets->each(function ($questionset) {
                /** @var QuestionsetDataObject $questionset */
                $questionset->addQuestions($this->getQuestions($questionset->id));
            });
        }
        return $questionsets;
    }

    public function getQuestionset($questionsetId, $includeQuestions = true): QuestionsetDataObject
    {
        $response = $this->client->request("GET", sprintf(self::QUESTIONSET, $questionsetId));
        $data = \GuzzleHttp\json_decode($response->getBody());
        $questionset = $this->mapQuestionsetResponseToDataObject($data);
        if ($includeQuestions === true) {
            /** @var QuestionsetDataObject $questionset */
            $questionset->addQuestions($this->getQuestions($questionset->id));
        }
        return $questionset;
    }

    public function createQuestionset(QuestionsetDataObject $questionset): QuestionsetDataObject
    {
        if (is_null($questionset->getMetadata())) {
            $questionset->addMetadata(MetadataDataObject::create());
        }
        $questionsetStructure = (object)[
            'title' => $questionset->title,
            'metadata' => $questionset->getMetadata(),
        ];

        $response = $this->client->request("POST", self::QUESTIONSETS, ['json' => $questionsetStructure]);
        $questionsetResponse = \GuzzleHttp\json_decode($response->getBody());
        return $this->mapQuestionsetResponseToDataObject($questionsetResponse);
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
            $question = $this->mapQuestionResponseToDataObject($question);
            $question->addAnswers($this->getAnswersByQuestion($question->id));
            return $question;
        });
    }

    public function getQuestion($questionId, $includeAnswers = true): QuestionDataObject
    {
        $response = $this->client->request("GET", sprintf(self::QUESTION, $questionId));
        $data = \GuzzleHttp\json_decode($response->getBody());
        $question = $this->mapQuestionResponseToDataObject($data);
        if ($includeAnswers === true) {
            $question->addAnswers($this->getAnswersByQuestion($question->id));
        }
        return $question;
    }

    public function createQuestion(QuestionDataObject $question): QuestionDataObject
    {
        if (is_null($question->getMetadata())) {
            $question->addMetadata(MetadataDataObject::create());
        }

        $questionStructure = (object)[
            'title' => $question->text,
            'metadata' => $question->getMetadata(),
        ];

        $response = $this->client->request("POST", sprintf(self::QUESTIONS, $question->questionSetId), ['json' => $questionStructure]);
        $questionResponse = \GuzzleHttp\json_decode($response->getBody());
        return $this->mapQuestionResponseToDataObject($questionResponse);
    }

    public function updateQuestion(QuestionDataObject $question)
    {
        // TODO: Implement updateQuestion() method.
    }

    public function deleteQuestion($questionId)
    {
        // TODO: Implement deleteQuestion() method.
    }

    public function getAnswer($answerId): AnswerDataObject
    {
        $response = $this->client->request("GET", sprintf(self::ANSWER, $answerId));
        $data = \GuzzleHttp\json_decode($response->getBody());
        $answer = $this->mapAnswerResponseToDataObject($data);
        return $answer;

    }

    public function getAnswersByQuestion($questionId): Collection
    {
        $response = $this->client->request("GET", sprintf(self::ANSWERS, $questionId));
        $data = collect(\GuzzleHttp\json_decode($response->getBody()));
        return $data->map(function ($answer) {
            return $this->mapAnswerResponseToDataObject($answer);
        });
    }

    public function createAnswer(AnswerDataObject $answer): AnswerDataObject
    {
        if (is_null($answer->getMetadata())) {
            $answer->addMetadata(MetadataDataObject::create());
        }

        $answerStructure = (object)[
            'description' => $answer->text,
            'correctness' => !empty($answer->isCorrect) ? 100 : 0,
            'metadata' => $answer->getMetadata(),
        ];

        $response = $this->client->request("POST", sprintf(self::ANSWERS, $answer->questionId), ['json' => $answerStructure]);
        $answerResponse = \GuzzleHttp\json_decode($response->getBody());
        return $this->mapAnswerResponseToDataObject($answerResponse);
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