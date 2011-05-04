<?php

use Newscoop\Service\Resource\ResourceId;
use Newscoop\Service\IThemeService;


class Admin_TestController extends Zend_Controller_Action
{

	/** @var Newscoop\Services\Resource\ResourceId */
	private $resourceId = NULL;
	/** @var Newscoop\Service\IThemeService */
	private $themeService = NULL;

	public function init(){
		
	}

	/* --------------------------------------------------------------- */

	/**
	 * Provides the controller resource id.
	 *
	 * @return Newscoop\Services\Resource\ResourceId
	 *		The controller resource id.
	 */
	public function getResourceId(){
		if ($this->resourceId === NULL) {
			$this->resourceId = new ResourceId(__CLASS__);
		}
		return $this->resourceId;
	}

	/**
	 * Provides the theme service.
	 *
	 * @return Newscoop\Service\IThemeService
	 *		The theme service to be used by this controller.
	 */
	public function getThemeService(){
		if ($this->themeService === NULL) {
			$this->themeService = $this->getResourceId()->service(IThemeService::NAME);
		}
		return $this->themeService;
	}


	/* --------------------------------------------------------------- */

	public function indexAction()
	{
		try{
			$this->view->name = $this->getThemeService()->getMsg();
			//service(IThemeService::NAME);
		}catch (\Exception $e){
			$this->view->name=$e->getMessage();
		}
	}
}

?>