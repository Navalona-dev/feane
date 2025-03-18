<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FrenchToDateTimeTransformer implements DataTransformerInterface
{

    public function transform($datetime)
{
    if (!is_object($datetime) || !($datetime instanceof \DateTimeInterface)) {
        return '';
    }

    return $datetime->format('d/m/Y');
}


    public function reverseTransform($frenchDatetime) 
    {
        if($frenchDatetime === null) {
            throw new TransformationFailedException("Vous devez fournir une date!");
        }

        $datetime = \DateTime::createFromFormat('d/m/Y', $frenchDatetime);

        if($datetime === false) {
            throw new TransformationFailedException("Le fomat de la date n'est pas le bon!");
        }

        return $datetime;
    }

}