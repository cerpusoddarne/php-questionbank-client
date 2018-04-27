<?php

namespace Cerpus\QuestionBankClient\Traits;


use Illuminate\Support\Collection;

/**
 * Trait CreateTrait
 * @package Cerpus\QuestionBankClient\Traits
 */
trait CreateTrait
{
    /**
     * @param array|null $attributes
     * @return CreateTrait
     */
    public static function create(array $attributes = null)
    {
        $self = new self();
        if (is_array($attributes)) {
            $properties = get_object_vars($self);
            foreach ($attributes as $attribute => $value) {
                if (array_key_exists($attribute, $properties)) {
                    $self->$attribute = $value;
                }
            }
        }
        return $self;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $returnArray = [];
        $properties = get_object_vars($this);
        foreach ($properties as $property => $value) {
            if ($this->$property instanceof Collection) {
                $returnArray[$property] = $this->$property->map(function ($element) {
                    if (method_exists($element, "toArray")) {
                        return $element->toArray();
                    } else {
                        return $element;
                    }
                })->toArray();
            } else {
                $returnArray[$property] = $value;
            }
        }
        return $returnArray;
    }
}