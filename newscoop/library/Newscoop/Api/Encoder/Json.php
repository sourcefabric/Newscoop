<?php

namespace Newscoop\Api\Encoder;

class Json
{
    public static function encode($data)
    {
        return json_encode($data);
    }
}