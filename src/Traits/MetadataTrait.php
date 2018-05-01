<?php

namespace Cerpus\QuestionBankClient\Traits;


use Cerpus\QuestionBankClient\DataObjects\MetadataDataObject;

trait MetadataTrait
{
    private $metadata;

    public function addMetadata(MetadataDataObject $metadata)
    {
        $this->metadata = $metadata;
    }

    public function getMetadata()
    {
        return $this->metadata;
    }
}