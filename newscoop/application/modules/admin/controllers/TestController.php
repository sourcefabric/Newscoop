<?php

use Newscoop\Entity\OutputSettings;
use Newscoop\Service\IThemeManagementService;
use Newscoop\Entity\Resource;
use Newscoop\Service\IOutputService;
use Newscoop\Service\Model\SearchPublication;
use Newscoop\Service\Model\SearchLanguage;
use Newscoop\Service\ILanguageService;
use Newscoop\Entity\Publication;
use Newscoop\Service\IPublicationService;
use Newscoop\Entity\Theme;
use Newscoop\Service\Model\SearchTheme;
use Newscoop\Service\Resource\ResourceId;
use Newscoop\Service\IThemeService;


class Admin_TestController extends Zend_Controller_Action
{

	/** @var Newscoop\Services\Resource\ResourceId */
	private $resourceId = NULL;
	/** @var Newscoop\Service\IThemeService */
	private $themeService = NULL;
	/** @var Newscoop\Service\IThemeManagementService */
	private $themeManagementService = NULL;
	/** @var Newscoop\Service\IPublicationService */
	private $publicationService = NULL;
	/** @var Newscoop\Service\ILanguageService */
	private $languageService = NULL;
	/** @var Newscoop\Service\IOutputService */
	private $outputService = NULL;

	public function init(){

	}

	/* --------------------------------------------------------------- */

	/**
	 * Provides the controller resource id.
	 *
	 * @return Newscoop\Services\Resource\ResourceId
	 *		The controller resource id.
	 */
	public function getResourceId()
	{
		if ($this->resourceId === NULL) {
			$this->resourceId = new ResourceId(__CLASS__);
		}
		return $this->resourceId;
	}

	/**
	 * Provides the ouput service.
	 *
	 * @return Newscoop\Service\IOutputService
	 *		The service service to be used by this controller.
	 */
	public function getOutputService()
	{
		if ($this->outputService === NULL) {
			$this->outputService = $this->getResourceId()->getService(IOutputService::NAME);
		}
		return $this->outputService;
	}

	/**
	 * Provides the language service.
	 *
	 * @return Newscoop\Service\ILanguageService
	 *		The language service to be used by this controller.
	 */
	public function getLanguageService()
	{
		if ($this->languageService === NULL) {
			$this->languageService = $this->getResourceId()->getService(ILanguageService::NAME);
		}
		return $this->languageService;
	}

	/**
	 * Provides the publications service.
	 *
	 * @return Newscoop\Service\IPublicationService
	 *		The publication service to be used by this controller.
	 */
	public function getPublicationService()
	{
		if ($this->publicationService === NULL) {
			$this->publicationService = $this->getResourceId()->getService(IPublicationService::NAME);
		}
		return $this->publicationService;
	}

	/**
	 * Provides the theme service.
	 *
	 * @return Newscoop\Service\IThemeService
	 *		The theme service to be used by this controller.
	 */
	public function getThemeService()
	{
		if ($this->themeService === NULL) {
			$this->themeService = $this->getResourceId()->getService(IThemeService::NAME);
		}
		return $this->themeService;
	}

	/**
	 * Provides the theme management service.
	 *
	 * @return Newscoop\Service\IThemeManagementService
	 *		The theme management service to be used by this controller.
	 */
	public function getThemeManagementService()
	{
		if ($this->themeManagementService === NULL) {
			$this->themeManagementService = $this->getResourceId()->getService(IThemeManagementService::NAME_1);
		}
		return $this->themeManagementService;
	}

	/* --------------------------------------------------------------- */

	public function indexAction()
	{
		$this->test6();
	}

	protected function test1()
	{
		$search = new SearchPublication();
		$search->NAME->orderAscending();


		try{

			$outputs = $this->getOutputService()->getEntities();
			$text = '---><br/>';//.$this->getLanguageService()->getCount();

			foreach($outputs as $out){
				/* @var $lang Language */
				$text = $text.$out->getName().'<br/>';
			}

			$this->view->text = $text;

		}catch (\Exception $e){
			$this->view->text = $e->getMessage();
		}
	}

	protected function test2()
	{
		$search = new SearchTheme();
		$search->NAME->orderAscending();


		try{
			$themes = $this->getThemeService()->getEntities();
			$text = '---><br/>';

			foreach($themes as $theme){
				/* @var $theme Theme */
				$text = $text.$theme->getName().'  -  '.$theme->getId().'  -  '.$theme->getPath().'<br/>';
				$imgs = $this->getThemeService()->getPresentationImages($theme);
				foreach($imgs as $img){
					/* @var $img Resource */
					$text = $text.'              '.$img->getName().'  -  '.$img->getId().'  -  '.$img->getPath().'<br/>';
				}
			}

			$this->view->text = $text;

		}catch (\Exception $e){
			$this->view->text = $e->getMessage();
		}
	}

	protected function test3()
	{
		$search = new SearchTheme();
		$search->NAME->orderAscending();


		try{
			$themes = $this->getThemeManagementService()->getUnassignedThemes();
			$text = '---><br/>';

			foreach($themes as $theme){
				/* @var $theme Theme */
				$text = $text.$theme->getName().'  -  '.$theme->getId().'  -  '.$theme->getPath().'<br/>';
				$imgs = $this->getThemeService()->getPresentationImages($theme);
				foreach($imgs as $img){
					/* @var $img Resource */
					$text = $text.'              '.$img->getName().'  -  '.$img->getId().'  -  '.$img->getPath().'<br/>';
				}
			}

			$this->view->text = $text;

		}catch (\Exception $e){
			$this->view->text = $e->getMessage();
		}
	}

	protected function test4()
	{
		$search = new SearchTheme();
		$search->NAME->orderAscending();


		try{
			$themes = $this->getThemeManagementService()->getThemes($this->getPublicationService()->findById(2));
			$text = '---><br/>';

			foreach($themes as $theme){
				/* @var $theme Theme */
				$text = $text.$theme->getName().'  -  '.$theme->getId().'  -  '.$theme->getPath().'<br/>';
				$tpls = $this->getThemeManagementService()->getTemplates($theme);
				foreach($tpls as $tpl){
					/* @var $img Resource */
					$text = $text.'              '.$tpl->getName().'  -  '.$tpl->getId().'  -  '.$tpl->getPath().'<br/>';
				}
				$this->getThemeManagementService()->getPresentationImages($theme);
			}

			$this->view->text = $text;

		}catch (\Exception $e){
			$this->view->text = $e->getMessage();
		}
	}

	protected function test5()
	{
		$search = new SearchTheme();
		$search->NAME->orderAscending();


		try{
			$themes = $this->getThemeManagementService()->getThemes($this->getPublicationService()->findById(2));
			$text = '---><br/>';

			foreach($themes as $theme){
				/* @var $theme Theme */
				$text = $text.$theme->getName().'  -  '.$theme->getId().'  -  '.$theme->getPath().'<br/>';
				$outputs = $this->getThemeManagementService()->getOutputSettings($theme);
				foreach($outputs as $out){
					/* @var $out OutputSettings */
					$text = $text.$out->getOutput()->getName().'------------------<br/>';
					$text = $text.$out->getFrontPage()->getPath().'<br/>';
					$text = $text.$out->getSectionPage()->getPath().'<br/>';
					$text = $text.$out->getArticlePage()->getPath().'<br/>';
					$text = $text.$out->getErrorPage()->getPath().'<br/>';
				}
			}

			$this->view->text = $text;

		}catch (\Exception $e){
			$this->view->text = $e->getMessage();
		}
	}

	protected function test6()
	{
		try{
			$theme1 = $this->getThemeManagementService()->getById(3);
			$pub = $this->getPublicationService()->findById(2);
			
			$this->getThemeManagementService()->assignTheme($theme1, $pub);
			$text = '---><br/>';

			$this->view->text = $text;

		}catch (\Exception $e){
			$this->view->text = 'errror<br/>'.$e.'</br>'.$e->getMessage();
		}
	}

}

?>