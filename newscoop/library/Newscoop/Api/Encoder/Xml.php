<?php

namespace Newscoop\Api\Encoder;

use SimpleXMLElement;

class Xml
{
    public static function encode($data)
    {
        $xml = new SimpleXMLElement( '<xml></xml>' );
        foreach( $data as $k => $v )
            $xml->addChild( $k, $v );
        return $xml->asXML();
    }
}