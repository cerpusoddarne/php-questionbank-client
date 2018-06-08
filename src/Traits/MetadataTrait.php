<?php

namespace Cerpus\QuestionBankClient\Traits;


use Cerpus\QuestionBankClient\DataObjects\MetadataDataObject;

trait MetadataTrait
{
    private $metadata;

    /**
     * @param MetadataDataObject $metadata
     */
    public function addMetadata(MetadataDataObject $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * @return MetadataDataObject|null
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @return array
     */
    public function getImages()
    {
        if( !empty($this->metadata->images)){
            return $this->metadata->images;
        }
        return [];
    }

    /**
     * @param $index
     * @return string|null
     */
    public function getImageAt($index)
    {
        if( !empty($this->metadata->images[$index])){
            return $this->metadata->images[$index];
        }
        return null;

    }
}