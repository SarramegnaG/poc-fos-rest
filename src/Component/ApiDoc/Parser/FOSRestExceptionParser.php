<?php

namespace Component\ApiDoc\Parser;

use Nelmio\ApiDocBundle\DataTypes;
use Nelmio\ApiDocBundle\Parser\ParserInterface;
use Nelmio\ApiDocBundle\Parser\PostParserInterface;

class FOSRestExceptionParser implements ParserInterface, PostParserInterface
{
    public function supports(array $item)
    {
        return isset($item['fos_rest_exception']) && $item['fos_rest_exception'] === true;
    }

    public function parse(array $item)
    {
        return array();
    }

    public function postParse(array $item, array $parameters)
    {
        $params = [];

        // Il faut d'abord désactiver tous les anciens paramètres créé par d'autres parseurs avant de reformater
        foreach ($parameters as $key => $parameter) {
            $params[$key] = null;
        }

        $params['code'] = [
            'dataType' => 'integer',
            'actualType' => DataTypes::INTEGER,
            'subType' => null,
            'required' => false,
            'description' => 'The status code',
            'readonly' => true,
        ];

        $params['message'] = [
            'dataType' => 'string',
            'actualType' => DataTypes::STRING,
            'subType' => null,
            'required' => true,
            'description' => 'The error message',
            'default' => 'Validation failed.',
        ];

        return $params;
    }
}