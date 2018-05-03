<?php

namespace Cerpus\QuestionBankClientTests;


use Cerpus\QuestionBankClient\DataObjects\QuestionsetDataObject;
use Cerpus\QuestionBankClient\QuestionBankClient;
use Faker\Provider\Uuid;
use PHPUnit\Framework\TestCase;

class QuestionBankClientTest extends TestCase
{

    /**
     * @test
     */
    public function getBasedir()
    {
        $this->assertEquals(dirname(__DIR__), QuestionBankClient::getBasePath());
    }

    /**
     * @test
     */
    public function getConfigPath()
    {
        $this->assertEquals(dirname(__DIR__) . '/src/Config/questionbank-client.php', QuestionBankClient::getConfigPath());
    }


    /**
     * @test
     */
    public function createQuestionset_validData_thenSuccess()
    {
        $title = "My first questionset";

        QuestionBankClient::shouldReceive('createQuestionset')
            ->once()
            ->andReturn(QuestionsetDataObject::create([
                'id' => Uuid::uuid(),
                'title' => $title,
            ]));

        $questionset = QuestionsetDataObject::create($title);

        $responseQuestionset = QuestionBankClient::createQuestionset($questionset);
        $this->assertInstanceOf(QuestionsetDataObject::class, $responseQuestionset);
        $this->assertEquals($title, $responseQuestionset->title);
        $this->assertObjectHasAttribute('id', $responseQuestionset);
    }
}