<?php

namespace Cerpus\QuestionBankClientTests\Adapters;

use Cerpus\QuestionBankClient\DataObjects\AnswerDataObject;
use Cerpus\QuestionBankClient\DataObjects\QuestionDataObject;
use Cerpus\QuestionBankClient\DataObjects\QuestionsetDataObject;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use Teapot\StatusCode;
use Tests\TestCase;

use Cerpus\QuestionBankClient\Adapters\QuestionBankAdapter;

class QuestionBankAdapterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function getQuestionsets_empty_thenSuccess()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->method("request")->willReturnCallback(function () {
            return (new Response(StatusCode::OK, [], "[]"));
        });

        /** @var ClientInterface $client */
        $adapter = new QuestionBankAdapter($client);
        $questionsets = $adapter->getQuestionsets(false);
        $this->assertEquals(get_class($questionsets), Collection::class);
        $questionsetsArray = $questionsets->toArray();
        $this->assertCount(0, $questionsetsArray);
    }

    /**
     * @test
     */
    public function getQuestionsets_twoSets_thenSuccess()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->method("request")->willReturnCallback(function () {
            return (new Response(StatusCode::OK, [], '[{"metadata": {"keywords": ["progress"]},"id": "c2197f7c-668d-4464-b645-cb4068f7eade","title": "QS progress"},{"metadata": {"keywords": ["test"]},"id": "dd4c2d2f-6490-4611-9be8-df5d1c7c6eb2","title": "QS inmaking"}]'));
        });

        $adapter = $this->getMockBuilder(QuestionBankAdapter::class)
            ->setConstructorArgs(['client' => $client])
            ->setMethods(['getQuestions'])
            ->getMock();
        $adapter->method('getQuestions')->willReturn(collect([QuestionDataObject::create()]));

        /** @var QuestionBankAdapter $adapter */
        $questionsets = $adapter->getQuestionsets(true);
        $this->assertEquals(get_class($questionsets), Collection::class);
        $questionsetsArray = $questionsets->toArray();
        $this->assertCount(2, $questionsetsArray);
        $this->assertEquals(get_class($questionsetsArray[0]), QuestionsetDataObject::class);
    }

    /**
     * @test
     */
    public function getQuestionsetWithoutQuestions()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->method("request")->willReturnCallback(function () {
            return (new Response(StatusCode::OK, [], '{"metadata": {"keywords": ["progress"]},"id": "c2197f7c-668d-4464-b645-cb4068f7eade","title": "QS progress"}'));
        });

        /** @var ClientInterface $client */
        $adapter = new QuestionBankAdapter($client);
        $questionset = $adapter->getQuestionset('c2197f7c-668d-4464-b645-cb4068f7eade', false);
        $this->assertEquals('c2197f7c-668d-4464-b645-cb4068f7eade', $questionset->id);
        $this->assertEmpty($questionset->getQuestions());
    }

    /**
     * @test
     */
    public function getQuestionsetWithQuestions()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->method("request")->willReturnCallback(function () {
            return (new Response(StatusCode::OK, [], '{"metadata": {"keywords": ["progress"]},"id": "c2197f7c-668d-4464-b645-cb4068f7eade","title": "QS progress"}'));
        });

        $adapter = $this->getMockBuilder(QuestionBankAdapter::class)
            ->setConstructorArgs(['client' => $client])
            ->setMethods(['getQuestions'])
            ->getMock();
        $adapter->method('getQuestions')->willReturn(collect([QuestionDataObject::create()]));

        /** @var QuestionBankAdapter $adapter */
        $questionset = $adapter->getQuestionset('c2197f7c-668d-4464-b645-cb4068f7eade');
        $this->assertEquals('c2197f7c-668d-4464-b645-cb4068f7eade', $questionset->id);
        $this->assertNotEmpty($questionset->getQuestions());
    }

    /**
     * @test
     */
    public function getQuestions_empty_thenSuccess()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->method("request")->willReturnCallback(function () {
            return (new Response(StatusCode::OK, [], "[]"));
        });

        /** @var ClientInterface $client */
        $adapter = new QuestionBankAdapter($client);
        $questions = $adapter->getQuestions("questionsetWithNoQuestions");
        $this->assertEquals(get_class($questions), Collection::class);
        $questionsArray = $questions->toArray();
        $this->assertCount(0, $questionsArray);
    }

    /**
     * @test
     */
    public function getQuestions_threeQuestions_thenSuccess()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->method("request")->willReturnCallback(function () {
            return (new Response(StatusCode::OK, [], '[{"metadata":{"keywords":[]},"id":"37a3ebce-002c-4e74-b611-0cb6e2e91515","questionSetId":"dd4c2d2f-6490-4611-9be8-df5d1c7c6eb2","title":"QS progress 3"},{"metadata":{"keywords":["progress2"]},"id":"6bdeda3c-1169-47c5-b173-782e7f36f9fc","questionSetId":"dd4c2d2f-6490-4611-9be8-df5d1c7c6eb2","title":"QS progress 2"},{"metadata":{"keywords":["testquestion"]},"id":"a184acf1-4c78-4f43-9a44-aad294dcc146","questionSetId":"dd4c2d2f-6490-4611-9be8-df5d1c7c6eb2","title":"QS question"}]'));
        });

        $adapter = $this->getMockBuilder(QuestionBankAdapter::class)
            ->setConstructorArgs(['client' => $client])
            ->setMethods(['getAnswersByQuestion'])
            ->getMock();
        $adapter->method('getAnswersByQuestion')->willReturn(collect([AnswerDataObject::create()]));

        /** @var QuestionBankAdapter $adapter */
        $questions = $adapter->getQuestions("questionsetWithNoQuestions");
        $this->assertEquals(get_class($questions), Collection::class);
        $questionsArray = $questions->toArray();
        $this->assertCount(3, $questionsArray);
        $this->assertEquals(get_class($questionsArray[0]), QuestionDataObject::class);
    }

    /**
     * @test
     */
    public function getQuestionWithAnswers()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->method("request")->willReturnCallback(function () {
            return (new Response(StatusCode::OK, [], '{"metadata":{"keywords":[]},"id":"37a3ebce-002c-4e74-b611-0cb6e2e91515","questionSetId":"dd4c2d2f-6490-4611-9be8-df5d1c7c6eb2","title":"Existing question"}'));
        });

        $adapter = $this->getMockBuilder(QuestionBankAdapter::class)
            ->setConstructorArgs(['client' => $client])
            ->setMethods(['getAnswersByQuestion'])
            ->getMock();
        $adapter->method('getAnswersByQuestion')->willReturn(collect([AnswerDataObject::create()]));

        /** @var QuestionBankAdapter $adapter */
        $question = $adapter->getQuestion('37a3ebce-002c-4e74-b611-0cb6e2e91515');
        $this->assertEquals('37a3ebce-002c-4e74-b611-0cb6e2e91515', $question->id);
        $this->assertEquals('Existing question', $question->text);
        $this->assertEquals('dd4c2d2f-6490-4611-9be8-df5d1c7c6eb2', $question->questionSetId);
        $this->assertNotEmpty($question->getAnswers());
    }

    /**
     * @test
     */
    public function getQuestionWithoutAnswers()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->method("request")->willReturnCallback(function () {
            return (new Response(StatusCode::OK, [], '{"metadata":{"keywords":[]},"id":"37a3ebce-002c-4e74-b611-0cb6e2e91515","questionSetId":"dd4c2d2f-6490-4611-9be8-df5d1c7c6eb2","title":"Existing question"}'));
        });

        $adapter = $this->getMockBuilder(QuestionBankAdapter::class)
            ->setConstructorArgs(['client' => $client])
            ->setMethods(['getAnswersByQuestion'])
            ->getMock();
        $adapter->method('getAnswersByQuestion')->willReturn(collect([AnswerDataObject::create()]));

        /** @var QuestionBankAdapter $adapter */
        $question = $adapter->getQuestion('37a3ebce-002c-4e74-b611-0cb6e2e91515');
        $this->assertEquals('37a3ebce-002c-4e74-b611-0cb6e2e91515', $question->id);
        $this->assertEquals('Existing question', $question->text);
        $this->assertEquals('dd4c2d2f-6490-4611-9be8-df5d1c7c6eb2', $question->questionSetId);
    }

    /**
     * @test
     */
    public function getAnswers_empty_thenSuccess()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->method("request")->willReturnCallback(function () {
            return (new Response(StatusCode::OK, [], "[]"));
        });

        /** @var ClientInterface $client */
        $adapter = new QuestionBankAdapter($client);
        $questions = $adapter->getAnswersByQuestion("QuestionWithNoAnswers");
        $this->assertEquals(get_class($questions), Collection::class);
        $questionsArray = $questions->toArray();
        $this->assertCount(0, $questionsArray);
    }

    /**
     * @test
     */
    public function getAnswers_twoAnswers_thenSuccess()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->method("request")->willReturnCallback(function () {
            return (new Response(StatusCode::OK, [], '[{"metadata":{"keywords":[]},"id":"7b937904-adfc-417a-bb7c-b9a7ad576709","questionId":"a184acf1-4c78-4f43-9a44-aad294dcc146","description":"QS answer 2","correctness":0},{"metadata":{"keywords":["testanswer"]},"id":"cebcf2f0-b233-4615-ac65-70d1af015f9a","questionId":"a184acf1-4c78-4f43-9a44-aad294dcc146","description":"QS answer","correctness":100}]'));
        });

        /** @var ClientInterface $client */
        $adapter = new QuestionBankAdapter($client);
        $questions = $adapter->getAnswersByQuestion("QuestionWithNoAnswers");
        $this->assertEquals(get_class($questions), Collection::class);
        $questionsArray = $questions->toArray();
        $this->assertCount(2, $questionsArray);
    }

    /**
     * @test
     */
    public function getAnswer()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->method("request")->willReturnCallback(function () {
            return (new Response(StatusCode::OK, [], '{"metadata":{"keywords":[]},"id":"cebfd105-d5e5-4158-9568-2f8a1252ccb4","questionId":"a184acf1-4c78-4f43-9a44-aad294dcc146","description":"Existing answer","correctness":0}'));
        });

        /** @var ClientInterface $client */
        $adapter = new QuestionBankAdapter($client);
        $answer = $adapter->getAnswer('cebfd105-d5e5-4158-9568-2f8a1252ccb4');
        $this->assertEquals('cebfd105-d5e5-4158-9568-2f8a1252ccb4', $answer->id);
        $this->assertEquals('Existing answer', $answer->text);
        $this->assertEquals('a184acf1-4c78-4f43-9a44-aad294dcc146', $answer->questionId);
    }

    /**
     * @test
     */
    public function createQuestionset()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->method("request")->willReturnCallback(function () {
            return (new Response(StatusCode::OK, [], '{"metadata":{"keywords":[]},"id":"d8884054-5fb4-4f4e-9fd6-6bceb85ee57d","title":"New Questionset"}'));
        });

        /** @var ClientInterface $client */
        $adapter = new QuestionBankAdapter($client);
        $savedQuestionset = $adapter->createQuestionset(QuestionsetDataObject::create("New Questionset"));
        $this->assertEquals(get_class($savedQuestionset), QuestionsetDataObject::class);
        $this->assertEquals("New Questionset", $savedQuestionset->title);
        $this->assertEquals('d8884054-5fb4-4f4e-9fd6-6bceb85ee57d', $savedQuestionset->id);
    }

    /**
     * @test
     */
    public function createQuestion()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->method("request")->willReturnCallback(function () {
            return (new Response(StatusCode::OK, [], '{"metadata":{"keywords":[]},"id":"d8884054-5fb4-4f4e-9fd6-6bceb85ee57d","title":"New Question","questionSetId":"cebfd105-d5e5-4158-9568-2f8a1252ccb4"}'));
        });

        /** @var ClientInterface $client */
        $adapter = new QuestionBankAdapter($client);
        $savedQuestion = $adapter->createQuestion(QuestionDataObject::create("New Question"));
        $this->assertEquals(get_class($savedQuestion), QuestionDataObject::class);
        $this->assertEquals("New Question", $savedQuestion->text);
        $this->assertEquals('d8884054-5fb4-4f4e-9fd6-6bceb85ee57d', $savedQuestion->id);
        $this->assertEquals('cebfd105-d5e5-4158-9568-2f8a1252ccb4', $savedQuestion->questionSetId);
    }

    /**
     * @test
     */
    public function createAnswer()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->method("request")->willReturnCallback(function () {
            return (new Response(StatusCode::OK, [], '{"metadata":{"keywords":[]},"id":"cebfd105-d5e5-4158-9568-2f8a1252ccb4","questionId":"a184acf1-4c78-4f43-9a44-aad294dcc146","description":"New Answer","correctness":100}'));
        });

        /** @var ClientInterface $client */
        $adapter = new QuestionBankAdapter($client);
        $savedAnswer = $adapter->createAnswer(AnswerDataObject::create("New Answer"));
        $this->assertEquals(get_class($savedAnswer), AnswerDataObject::class);
        $this->assertEquals("New Answer", $savedAnswer->text);
        $this->assertEquals('cebfd105-d5e5-4158-9568-2f8a1252ccb4', $savedAnswer->id);
    }
}
