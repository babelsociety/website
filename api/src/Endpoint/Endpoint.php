<?php
namespace BabelSociety\Endpoint;

interface Endpoint {
    public function invoke(string $httpMethod, string $rawRequest);
}
