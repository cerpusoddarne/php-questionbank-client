<?php

namespace Cerpus\QuestionBankClientTests\Utils;

use Cerpus\QuestionBankClientTests\Utils\Traits\WithFaker;
use PHPUnit\Framework\TestCase;

class QuestionBankTestCase extends TestCase
{

    protected function setUp()
    {
        parent::setUp();
        $this->setUpTraits();
    }

    public function setUpTraits()
    {
        $uses = array_flip(class_uses_recursive(static::class));

        if (isset($uses[WithFaker::class])) {
            $this->setUpFaker();
        }
    }
}