<?php

namespace Cerpus\QuestionBankClient\DataObjects;


use Cerpus\QuestionBankClient\Traits\CreateTrait;

/**
 * Class MetadataDataObject
 * @package Cerpus\QuestionBankClient\DataObjects
 *
 * @method static MetadataDataObject create($attributes = null)
 */
class MetadataDataObject
{
    use CreateTrait;

    public $keywords = [];
    public $image;
}