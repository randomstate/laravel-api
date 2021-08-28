<?php

namespace RandomState\LaravelApi\Http\Response;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use RandomState\Api\Versioning\Version;

class TransformedResponse implements Responsable
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function toResponse($request): Response
    {
        return new Response(app(Version::class)->transform($this->data));
    }
}