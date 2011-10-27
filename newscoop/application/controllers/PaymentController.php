<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Handles payment specific actions
 */
class PaymentController extends Zend_Controller_Action
{
    public function init()
    {
        //$this->_helper->ajaxContext->addActionContext('postfinance', 'json')->initContext();
    }

    public function postfinanceAction()
    {
        //$params = $this->_request->getParams();

        /*
        $params = $this->_reqeust->getParams();

        $client = new Zend_Http_Client();
        $client->setUri('http://example.org');
        $client->setMethod(Zend_Http_Client::POST);
        $client->setConfig( array
        (
    		'maxredirects' => 0,
    		'timeout'      => 30
        ));
        $client->setParameterPost(array
        (
    		'abc'  => '123',
    		'xyz'  => 123,
    		'smth' => array('a', 'b', 3)
        ));
        */
    }
}