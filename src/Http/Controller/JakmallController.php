<?php

namespace Jakmall\Recruitment\Calculator\Http\Controller;

use Symfony\Component\HttpFoundation\Response;

class JakmallController
{
    public function __construct() {}

    public function JsonOutput($data=[])
    {
        $response = new Response();
        $response->setContent(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}