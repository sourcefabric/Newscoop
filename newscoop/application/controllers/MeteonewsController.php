<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Proxy controller
 */

class MeteonewsController extends Zend_Controller_Action
{
    /** Zend_Config */
    private $config;

    public function init()
    {
        $this->config = new Zend_Config_Ini(
            APPLICATION_PATH . '/configs/meteonews.ini',
            APPLICATION_ENV
        );
        $this->_helper->layout->disableLayout();
    }

    public function indexAction()
    {
       $this->_forward('proxy'); 
    }

    public function proxyAction()
    {
        $url = $this->_getParam('url');
        $user = $this->config->api_user;
        $pass = $this->config->api_pass;

        $fOpts = array(
            'lifetime' => 14400,
        );

        $cache = Zend_Cache::factory('Core', 'Apc', $fOpts, array());
        $cache_key = md5("__meteonews_cache_$url");
        if (!$json = $cache->load($cache_key)) {
 
            $client = new \Zend_Http_Client($url);
            $client->setMethod(Zend_Http_Client::GET);
            $client->setAuth($user,$pass, Zend_Http_Client::AUTH_BASIC);
            $json = Zend_Json::fromXml($client->request()->getBody(), false);
            
            $cache->save($json, $cache_key);
        }
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $response->setBody($json);
        $response->sendResponse();
        exit;
    }
    
    public function configAction()
    {
        $format = $this->_getParam('format');

        switch ($format) {
            case 'ini':
                $writer = new Zend_Config_Writer_Ini();
                break;
            case 'xml':
                $writer = new Zend_Config_Writer_Xml();
                break;
            case 'json':
            default:
                $writer = new Zend_Config_Writer_Json();
                break;
        }            
                        
        $writer->setConfig($this->config);
 
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $response->setBody($writer->render());
        $response->sendResponse();
        exit;
    }
}
