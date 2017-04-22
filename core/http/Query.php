<?php

namespace Core\Http;

final class Query{

    private $url;

    public function __construct(Url $url)
    {
        $this->url = $url;
    }

}