<?php

namespace Cerpus\QuestionBankClient\Adapters;

use Cerpus\QuestionBankClient\Contracts\QuestionBankContract;
use Cerpus\QuestionBankClient\DataObjects\AnswerDataObject;
use Cerpus\QuestionBankClient\DataObjects\QuestionDataObject;
use Cerpus\QuestionBankClient\DataObjects\QuestionsetDataObject;
use Cerpus\QuestionBankClient\QuestionBankClient;
use GuzzleHttp\ClientInterface;
use Ramsey\Uuid\Uuid;

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
        $this->data = $this->createData($this->getData());
    }

    private function getData()
    {
        $data = file_get_contents(QuestionBankClient::getBasePath() . '/src/Adapters/tempData.json');
        return json_decode($data, true);
    }

    private function dataBackup()
    {
        $questionsetsTemplate = [
            [
                'title' => 'Geografi',
                'questions' => [
                    [
                        'text' => "Hvilken verdensdel tilhører Norge?",
                        'answers' => [
                            [
                                'text' => "Europa",
                                'isCorrect' => true,
                            ],
                            [
                                'text' => "Asia",
                                'isCorrect' => false,
                            ],
                            [
                                'text' => "Afrika",
                                'isCorrect' => false,
                            ],
                        ],
                    ],
                    [
                        'text' => "Hva heter verdens høyeste fjell?",
                        'answers' => [
                            [
                                'text' => "Mount Everest",
                                'isCorrect' => true,
                            ],
                            [
                                'text' => "K2",
                                'isCorrect' => false,
                            ],
                            [
                                'text' => "Galdhøpiggen",
                                'isCorrect' => false,
                            ],
                        ],
                    ],
                    [
                        'text' => "Hvor mange land består Storbritannia av?",
                        'answers' => [
                            [
                                'text' => "4",
                                'isCorrect' => true,
                            ],
                            [
                                'text' => "1",
                                'isCorrect' => false,
                            ],
                            [
                                'text' => "2",
                                'isCorrect' => false,
                            ],
                            [
                                'text' => "5",
                                'isCorrect' => false,
                            ],
                        ],
                    ],
                    [
                        'text' => "Hvor mange land grenser Norge til?",
                        'answers' => [
                            [
                                'text' => "3",
                                'isCorrect' => true,
                            ],
                            [
                                'text' => "2",
                                'isCorrect' => false,
                            ],
                            [
                                'text' => "3",
                                'isCorrect' => false,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Sport',
                'questions' => [
                    [
                        'text' => "Hvor mange ganger har Norge vunnet VM i fotball for kvinner?",
                        'answers' => [
                            [
                                'text' => "1",
                                'isCorrect' => true,
                            ],
                            [
                                'text' => "0",
                                'isCorrect' => false,
                            ],
                            [
                                'text' => "2",
                                'isCorrect' => false,
                            ],
                        ],
                    ],
                    [
                        'text' => "Hvor arrangerte man OL i 2002?",
                        'answers' => [
                            [
                                'text' => "Bejing",
                                'isCorrect' => false,
                            ],
                            [
                                'text' => "Oslo",
                                'isCorrect' => false,
                            ],
                            [
                                'text' => "Salt Lake City",
                                'isCorrect' => true,
                            ],
                        ],
                    ],
                    [
                        'text' => "Hvor kommer sporten curling fra?",
                        'answers' => [
                            [
                                'text' => "England",
                                'isCorrect' => false,
                            ],
                            [
                                'text' => "Skottland",
                                'isCorrect' => true,
                            ],
                            [
                                'text' => "Nederland",
                                'isCorrect' => false,
                            ],
                            [
                                'text' => "Norge",
                                'isCorrect' => false,
                            ],
                            [
                                'text' => "Island",
                                'isCorrect' => false,
                            ],
                            [
                                'text' => "USA",
                                'isCorrect' => false,
                            ],
                        ],
                    ],
                    [
                        'text' => "Hvor mange stener har hvert lag i curling?",
                        'answers' => [
                            [
                                'text' => "6",
                                'isCorrect' => false,
                            ],
                            [
                                'text' => "8",
                                'isCorrect' => true,
                            ],
                            [
                                'text' => "10",
                                'isCorrect' => false,
                            ],
                        ],
                    ],
                    [
                        'text' => "Hvilken spiller har vunnet flest Grand Slam titler i tennis?",
                        'answers' => [
                            [
                                'text' => "Roger Federer",
                                'isCorrect' => true,
                            ],
                            [
                                'text' => "Rafael Nadal",
                                'isCorrect' => false,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Math trivia',
                'questions' => [
                    [
                        'text' => "How many digits of pi are there?",
                        'answers' => [
                            [
                                'text' => "2",
                                'isCorrect' => false,
                            ],
                            [
                                'text' => "124",
                                'isCorrect' => false,
                            ],
                            [
                                'text' => "Infinity",
                                'isCorrect' => true,
                            ],
                            [
                                'text' => "256",
                                'isCorrect' => false,
                            ],
                            [
                                'text' => "10000",
                                'isCorrect' => false,
                            ],
                        ],
                    ],
                    [
                        'text' => "What is the 21st prime number?",
                        'answers' => [
                            [
                                'text' => "73",
                                'isCorrect' => true,
                            ],
                            [
                                'text' => "51",
                                'isCorrect' => false,
                            ],
                            [
                                'text' => "103",
                                'isCorrect' => false,
                            ],
                        ],
                    ],
                    [
                        'text' => "Is 4 an irrational number?",
                        'answers' => [
                            [
                                'text' => "Yes",
                                'isCorrect' => false,
                            ],
                            [
                                'text' => "No",
                                'isCorrect' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function createData($data, $original = true)
    {
        $questionsets = [];
        foreach ($data as $questionsetElement) {
            $questionsetId = array_key_exists('id', $questionsetElement) ? $questionsetElement['id'] : Uuid::uuid4()->toString();
            $questionset = QuestionsetDataObject::create([
                "id" => $questionsetId,
                "title" => $questionsetElement['title'],
            ]);
            foreach ($questionsetElement['questions'] as $questionElement) {
                $questionId = array_key_exists('id', $questionElement) ? $questionElement['id'] : Uuid::uuid4()->toString();
                $question = QuestionDataObject::create([
                    "id" => $questionId,
                    'text' => $questionElement['text'],
                ]);
                foreach ($questionElement['answers'] as $answerElement) {
                    $answerId = array_key_exists('id', $answerElement) ? $answerElement['id'] : Uuid::uuid4()->toString();
                    $question->addAnswer(AnswerDataObject::create([
                        "id" => $answerId,
                        'text' => $answerElement['text'],
                        'isCorrect' => $answerElement['isCorrect'],
                    ]));
                }
                $questionset->addQuestion($question);
            }
            if( $original === true){
                $questionsets[] = $questionset;
            } else {
                $questionsets[] = $questionset->toArray();
            }
        }

        if( $original !== true){
            $questionsets = json_encode($questionsets);
        }
        return $questionsets;
    }

    /**
     * @return array[QuestionsetDataObject]
     */
    public function getQuestionsets($includeQuestions = true): array
    {
        return $this->data;
    }

    public function getQuestionset($questionsetId) : QuestionsetDataObject
    {
        $questionset = collect($this->data)->filter(function ($questionset) use ($questionsetId){
            return $questionset->id === $questionsetId;
        });
        if( $questionset->isNotEmpty()){
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

    public function getQuestions($questionsetId)
    {
        $questionset = collect($this->data)->filter(function ($questionset) use ($questionsetId){
            return $questionset->id === $questionsetId;
        });
        if( $questionset->isNotEmpty()){
            return $questionset->first()->getQuestions();
        }
        throw new \Exception("Could not find your questionset");
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