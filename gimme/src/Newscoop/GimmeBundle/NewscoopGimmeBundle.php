<?php

namespace Newscoop\GimmeBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use JMS\SerializerBundle\DependencyInjection\JMSSerializerExtension;
use Newscoop\GimmeBundle\DependencyInjection\Factory\ArticleAuthorHandlerFactory;
use Newscoop\GimmeBundle\DependencyInjection\Factory\ArticleCommentsLinkHandlerFactory;
use Newscoop\GimmeBundle\DependencyInjection\Factory\AuthorImageUriHandlerFactory;

class NewscoopGimmeBundle extends Bundle
{
    public function configureSerializerExtension(JMSSerializerExtension $ext)  
    {  
        $ext->addHandlerFactory(new ArticleAuthorHandlerFactory());  
        $ext->addHandlerFactory(new ArticleCommentsLinkHandlerFactory());
        $ext->addHandlerFactory(new AuthorImageUriHandlerFactory());
    }  
}
