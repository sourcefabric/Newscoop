<?php

namespace Newscoop;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class ServiceContainer extends ContainerBuilder
{

	public function __construct($options)
    {
    	parent::__construct(new ParameterBag($options));
    }

	public function setService($id, $service)
	{
		parent::set($id, $service);
	}

	public function hasService($id)
	{
		return parent::has($id);
	}


	public function getService($id)
	{
		return parent::get($id);
	}

	public function getServiceIds()
	{
		return parent::getServiceIds();
	}

	public function setAlias($alias, $id)
	{
		parent::setAlias($alias, $id);
	}

	public function getAliases()
	{
		return parrent::getAliases();
	}

	public function register($id, $class = null)
	{
		return parent::register($id, $class);
	}
}
