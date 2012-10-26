<?php

namespace Newscoop\GimmeBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use JMS\SerializerBundle\DependencyInjection\JMSSerializerExtension;
use Newscoop\GimmeBundle\DependencyInjection\Factory\ArticleAuthorHandlerFactory;

class NewscoopGimmeBundle extends Bundle
{
    public function configureSerializerExtension(JMSSerializerExtension $ext)  
    {  
        $ext->addHandlerFactory(new ArticleAuthorHandlerFactory());  
    }  
}
