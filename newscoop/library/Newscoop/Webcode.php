<?php
namespace Newscoop;

interface Webcode
{
	static function encode($id);

	static function decode($string);

	static function isWebcode($string);
}