<?php

namespace Cerpus\QuestionBankClient\Adapters;

use Cerpus\QuestionBankClient\Contracts\QuestionBankContract;
use Cerpus\QuestionBankClient\DataObjects\AnswerDataObject;
use Cerpus\QuestionBankClient\DataObjects\MetadataDataObject;
use Cerpus\QuestionBankClient\DataObjects\QuestionDataObject;
use Cerpus\QuestionBankClient\DataObjects\QuestionsetDataObject;
use Cerpus\QuestionBankClient\DataObjects\SearchDataObject;
use Cerpus\QuestionBankClient\Exceptions\InvalidSearchParametersException;
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

    /**
     * QuestionBankAdapter constructor.
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param object $metadata
     * @return MetadataDataObject
     */
    private function transformMetadata($metadata)
    {
        return MetadataDataObject::create([
            'keywords' => $metadata->keywords,
            'images' => $metadata->images,
        ]);
    }

    /**
     * @param object $questionValues
     * @return QuestionsetDataObject
     */
    private function mapQuestionsetResponseToDataObject($questionValues)
    {
        $questionset = QuestionsetDataObject::create([
            'id' => $questionValues->id,
            'title' => $questionValues->title,
        ]);
        $questionset->addMetadata($this->transformMetadata($questionValues->metadata));
        return $questionset;
    }

    /**
     * @param object $questionValues
     * @return QuestionDataObject
     */
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

    /**
     * @param object $answerValues
     * @return AnswerDataObject
     */
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
     * @param Collection|SearchDataObject $search
     * @return array
     * @throws InvalidSearchParametersException
     */
    private function traverseSearch($search): array
    {
        if (!is_object($search) || !in_array(get_class($search), [
                Collection::class,
                SearchDataObject::class,
            ])) {
            throw new InvalidSearchParametersException();
        }

        if (is_a($search, SearchDataObject::class)) {
            $params = collect([$search]);
        } else {
            $params = $search;
        }

        $queryParams = $params
            ->map(function (SearchDataObject $param) {
                return $param->make();
            })
            ->reduce(function ($old, $new) {
                return array_merge($old, $new);
            }, []);
        return ['query' => $queryParams];
    }

    /**
     * @return Collection[QuestionsetDataObject]
     * @param Collection|SearchDataObject $search = null
     * @param boolean $includeQuestions = true
     */
    public function getQuestionsets($search = null, $includeQuestions = true): Collection
    {
        $additionalParameters = !is_null($search) ? $this->traverseSearch($search) : [];
        $response = $this->client->request("GET", self::QUESTIONSETS, $additionalParameters);
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

    /**
     * @param string $questionsetId
     * @param bool $includeQuestions
     * @return QuestionsetDataObject
     */
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

    /**
     * @param QuestionsetDataObject $questionset
     * @return QuestionsetDataObject
     */
    public function storeQuestionset(QuestionsetDataObject $questionset): QuestionsetDataObject
    {
        if (empty($questionset->id)) {
            return $this->createQuestionset($questionset);
        } else {
            return $this->updateQuestionset($questionset);
        }
    }

    /**
     * @param QuestionsetDataObject $questionset
     * @return QuestionsetDataObject
     */
    private function createQuestionset(QuestionsetDataObject $questionset): QuestionsetDataObject
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
        $createdQuestionset = $this->mapQuestionsetResponseToDataObject($questionsetResponse);
        $createdQuestionset->wasRecentlyCreated = true;
        return $createdQuestionset;
    }

    /**
     * @param QuestionsetDataObject $questionset
     * @return QuestionsetDataObject
     */
    private function updateQuestionset(QuestionsetDataObject $questionset): QuestionsetDataObject
    {
        if (is_null($questionset->getMetadata())) {
            $questionset->addMetadata(MetadataDataObject::create());
        }
        $questionsetStructure = (object)[
            'title' => $questionset->title,
            'metadata' => $questionset->getMetadata(),
        ];

        $response = $this->client->request("PUT", sprintf(self::QUESTIONSET, $questionset->id), ['json' => $questionsetStructure]);
        $questionsetResponse = \GuzzleHttp\json_decode($response->getBody());
        return $this->mapQuestionsetResponseToDataObject($questionsetResponse);
    }

    /**
     * @param $id
     */
    public function deleteQuestionset($id)
    {
        // TODO: Implement deleteQuestionset() method.
    }

    /**
     * @param $questionsetId
     * @return Collection[QuestionDataObject]
     */
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

    /**
     * @param $questionId
     * @param bool $includeAnswers
     * @return QuestionDataObject
     */
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

    /**
     * @param QuestionDataObject $question
     * @return QuestionDataObject
     */
    public function storeQuestion(QuestionDataObject $question): QuestionDataObject
    {
        if (empty($question->id)) {
            return $this->createQuestion($question);
        } else {
            return $this->updateQuestion($question);
        }
    }

    /**
     * @param QuestionDataObject $question
     * @return QuestionDataObject
     */
    private function createQuestion(QuestionDataObject $question): QuestionDataObject
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
        $createdQuestion = $this->mapQuestionResponseToDataObject($questionResponse);
        $createdQuestion->wasRecentlyCreated = true;
        return $createdQuestion;
    }

    /**
     * @param QuestionDataObject $question
     * @return QuestionDataObject
     */
    private function updateQuestion(QuestionDataObject $question): QuestionDataObject
    {
        if (is_null($question->getMetadata())) {
            $question->addMetadata(MetadataDataObject::create());
        }

        $questionStructure = (object)[
            'title' => $question->text,
            'metadata' => $question->getMetadata(),
        ];

        $response = $this->client->request("PUT", sprintf(self::QUESTION, $question->id), ['json' => $questionStructure]);
        $questionResponse = \GuzzleHttp\json_decode($response->getBody());
        return $this->mapQuestionResponseToDataObject($questionResponse);
    }

    /**
     * @param $questionId
     */
    public function deleteQuestion($questionId)
    {
        // TODO: Implement deleteQuestion() method.
    }

    /**
     * @param $answerId
     * @return AnswerDataObject
     */
    public function getAnswer($answerId): AnswerDataObject
    {
        $response = $this->client->request("GET", sprintf(self::ANSWER, $answerId));
        $data = \GuzzleHttp\json_decode($response->getBody());
        return $this->mapAnswerResponseToDataObject($data);
    }

    /**
     * @param $questionId
     * @return Collection[AnswerDataObject]
     */
    public function getAnswersByQuestion($questionId): Collection
    {
        $response = $this->client->request("GET", sprintf(self::ANSWERS, $questionId));
        $data = collect(\GuzzleHttp\json_decode($response->getBody()));
        return $data->map(function ($answer) {
            return $this->mapAnswerResponseToDataObject($answer);
        });
    }

    /**
     * @param AnswerDataObject $answer
     * @return AnswerDataObject
     */
    public function storeAnswer(AnswerDataObject $answer): AnswerDataObject
    {
        if (empty($answer->id)) {
            return $this->createAnswer($answer);
        } else {
            return $this->updateAnswer($answer);
        }
    }

    /**
     * @param AnswerDataObject $answer
     * @return AnswerDataObject
     */
    private function createAnswer(AnswerDataObject $answer): AnswerDataObject
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
        $createdAnswer = $this->mapAnswerResponseToDataObject($answerResponse);
        $createdAnswer->wasRecentlyCreated = true;
        return $createdAnswer;
    }

    /**
     * @param AnswerDataObject $answer
     * @return AnswerDataObject
     */
    private function updateAnswer(AnswerDataObject $answer): AnswerDataObject
    {
        if (is_null($answer->getMetadata())) {
            $answer->addMetadata(MetadataDataObject::create());
        }

        $answerStructure = (object)[
            'description' => $answer->text,
            'correctness' => !empty($answer->isCorrect) ? 100 : 0,
            'metadata' => $answer->getMetadata(),
        ];

        $response = $this->client->request("PUT", sprintf(self::ANSWER, $answer->id), ['json' => $answerStructure]);
        $answerResponse = \GuzzleHttp\json_decode($response->getBody());
        return $this->mapAnswerResponseToDataObject($answerResponse);
    }

    /**
     * @param $answerId
     */
    public function deleteAnswer($answerId)
    {
        // TODO: Implement deleteAnswer() method.
    }
}