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
    	$this->_helper->layout->disableLayout(true);
        $params = $this->_request->getParams();
        
        if( !array_key_exists('accepturl', $params) || !array_key_exists('amount', $params) 
	        || !array_key_exists('currency', $params) || !array_key_exists('language', $params)
	        || !array_key_exists('orderID', $params) || !array_key_exists('PSPID', $params)
        ) {
			die('Forbidden');        	
        }
        
        
        $shaPass = 'nzzonline123456#$';
        
        $accepturl = $params['accepturl'];
        $amount = $params['amount'];
        $currency = $params['currency'];
        $language = $params['language'];
        $orderId = $params['orderID'];
        $PSPID = $params['PSPID'];
        
        $shaString = "ACCEPTURL=".$accepturl.$shaPass."AMOUNT=".$amount.$shaPass."CURRENCY=".$currency.$shaPass."LANGUAGE=".$language.$shaPass."ORDERID=".$orderId.$shaPass."PSPID=".$PSPID.$shaPass;
        
    	$SHASign = sha1($shaString);
    	
    	$this->view->amount = $amount;
    	$this->view->accepturl = $accepturl;
    	$this->view->realAmount = round($amount/100, 2);
    	$this->view->language = $language;
    	$this->view->orderID = $orderId;
    	$this->view->PSPID = $PSPID;
    	$this->view->SHASign = $SHASign;
    	
    }
}