<?php

namespace Cerpus\QuestionBankClientTests\Adapters;

use Cerpus\QuestionBankClient\DataObjects\AnswerDataObject;
use Cerpus\QuestionBankClient\DataObjects\QuestionDataObject;
use Cerpus\QuestionBankClient\DataObjects\QuestionsetDataObject;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
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
            return (new Response(\Illuminate\Http\Response::HTTP_OK, [], "[]"));
        });

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
            return (new Response(\Illuminate\Http\Response::HTTP_OK, [], '[{"metadata": {"keywords": ["progress"]},"id": "c2197f7c-668d-4464-b645-cb4068f7eade","title": "QS progress"},{"metadata": {"keywords": ["test"]},"id": "dd4c2d2f-6490-4611-9be8-df5d1c7c6eb2","title": "QS inmaking"}]'));
        });

        $adapter = $this->getMockBuilder(QuestionBankAdapter::class)
            ->setConstructorArgs(['client' => $client])
            ->setMethods(['getQuestions'])
            ->getMock();
        $adapter->method('getQuestions')->willReturn(collect([QuestionDataObject::create(['text' => "Mock question"])]));

        $questionsets = $adapter->getQuestionsets(true);
        $this->assertEquals(get_class($questionsets), Collection::class);
        $questionsetsArray = $questionsets->toArray();
        $this->assertCount(2, $questionsetsArray);
        $this->assertEquals(get_class($questionsetsArray[0]), QuestionsetDataObject::class);
    }

    /**
     * @test
     */
    public function getQuestions_empty_thenSuccess()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->method("request")->willReturnCallback(function () {
            return (new Response(\Illuminate\Http\Response::HTTP_OK, [], "[]"));
        });

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
            return (new Response(\Illuminate\Http\Response::HTTP_OK, [], '[{"metadata":{"keywords":["progress"]},"id":"37a3ebce-002c-4e74-b611-0cb6e2e91515","questionSetId":"dd4c2d2f-6490-4611-9be8-df5d1c7c6eb2","title":"QS progress 3"},{"metadata":{"keywords":["progress2"]},"id":"6bdeda3c-1169-47c5-b173-782e7f36f9fc","questionSetId":"dd4c2d2f-6490-4611-9be8-df5d1c7c6eb2","title":"QS progress 2"},{"metadata":{"keywords":["testquestion"]},"id":"a184acf1-4c78-4f43-9a44-aad294dcc146","questionSetId":"dd4c2d2f-6490-4611-9be8-df5d1c7c6eb2","title":"QS question"}]'));
        });

        $adapter = $this->getMockBuilder(QuestionBankAdapter::class)
            ->setConstructorArgs(['client' => $client])
            ->setMethods(['getAnswersByQuestion'])
            ->getMock();
        $adapter->method('getAnswersByQuestion')->willReturn(collect([AnswerDataObject::create(['text' => "Mock answer", 'isCorrect' => 100])]));

        $questions = $adapter->getQuestions("questionsetWithNoQuestions");
        $this->assertEquals(get_class($questions), Collection::class);
        $questionsArray = $questions->toArray();
        $this->assertCount(3, $questionsArray);
        $this->assertEquals(get_class($questionsArray[0]), QuestionDataObject::class);
    }

    /**
     * @test
     */
    public function getAnswers_empty_thenSuccess()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->method("request")->willReturnCallback(function () {
            return (new Response(\Illuminate\Http\Response::HTTP_OK, [], "[]"));
        });

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
            return (new Response(\Illuminate\Http\Response::HTTP_OK, [], '[{"metadata":{"keywords":["testanswer"]},"id":"7b937904-adfc-417a-bb7c-b9a7ad576709","questionId":"a184acf1-4c78-4f43-9a44-aad294dcc146","description":"QS answer 2","correctness":0},{"metadata":{"keywords":["testanswer"]},"id":"cebcf2f0-b233-4615-ac65-70d1af015f9a","questionId":"a184acf1-4c78-4f43-9a44-aad294dcc146","description":"QS answer","correctness":100}]'));
        });

        $adapter = new QuestionBankAdapter($client);
        $questions = $adapter->getAnswersByQuestion("QuestionWithNoAnswers");
        $this->assertEquals(get_class($questions), Collection::class);
        $questionsArray = $questions->toArray();
        $this->assertCount(2, $questionsArray);
    }
}
