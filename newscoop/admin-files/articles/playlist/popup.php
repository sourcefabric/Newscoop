<?php
    $front = Zend_Controller_Front::getInstance();
    $front->getRequest()
        ->clearParams()
        ->setDispatched(false)
        ->clearParams()
        ->setActionName('article')
        ->setControllerName('playlist')
        ->setModuleName('admin')
        ->setParam('id', Input::Get('f_article_number', 'int', 1) );
        ;
    $front->getDispatcher()->dispatch($front->getRequest(), $front->getResponse());
    $front->getResponse()->getBody();
?>