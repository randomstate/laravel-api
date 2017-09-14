<?php


namespace RandomState\LaravelApi\Adapters\Fractal;


use RandomState\Api\Transformation\Adapters\Adapter;
use Symfony\Component\HttpFoundation\Response;

class ResponseAdapter implements Adapter
{
    public function transforms($data)
    {
        return $data instanceof Response;
    }

    public function run($data)
    {
        return $data;
    }
}