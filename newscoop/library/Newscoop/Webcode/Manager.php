<?php
namespace Newscoop\Webcode;

class Manager
{
	/**
	 * @var \Newscoop\Webcode
	 */
    private static $webcoder;

    public static function getWebcoder($for)
    {
    	switch($for)
        {
            case 'mapper' :
            default :
            self::$webcoder = new Mapper(); break;
        }
        return self::$webcoder;
    }
}
