<?php

namespace Newscoop\GimmeBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use JMS\SerializerBundle\DependencyInjection\JMSSerializerExtension;
use Newscoop\GimmeBundle\DependencyInjection\Factory\ArticleAuthorHandlerFactory;
use Newscoop\GimmeBundle\DependencyInjection\Factory\ArticleCommentsLinkHandlerFactory;
use Newscoop\GimmeBundle\DependencyInjection\Factory\AuthorImageUriHandlerFactory;
use Newscoop\GimmeBundle\DependencyInjection\Factory\PackageItemsLinkHandlerFactory;
use Newscoop\GimmeBundle\DependencyInjection\Factory\ItemLinkHandlerFactory;
use Newscoop\GimmeBundle\DependencyInjection\Factory\TopicArticlesLinkHandlerFactory;
use Newscoop\GimmeBundle\DependencyInjection\Factory\ArticleFieldsHandlerFactory;
use Newscoop\GimmeBundle\DependencyInjection\Factory\ArticleTranslationsHandlerFactory;
use Newscoop\GimmeBundle\DependencyInjection\Factory\ArticleRenditionsHandlerFactory;

class NewscoopGimmeBundle extends Bundle
{
    public function configureSerializerExtension(JMSSerializerExtension $ext)
    {
        $ext->addHandlerFactory(new ArticleAuthorHandlerFactory());
        $ext->addHandlerFactory(new ArticleCommentsLinkHandlerFactory());
        $ext->addHandlerFactory(new AuthorImageUriHandlerFactory());
        $ext->addHandlerFactory(new PackageItemsLinkHandlerFactory());
        $ext->addHandlerFactory(new ItemLinkHandlerFactory());
        $ext->addHandlerFactory(new TopicArticlesLinkHandlerFactory());
        $ext->addHandlerFactory(new ArticleFieldsHandlerFactory());
        $ext->addHandlerFactory(new ArticleTranslationsHandlerFactory());
        $ext->addHandlerFactory(new ArticleRenditionsHandlerFactory());
    }

    public function getParent()
    {
        return 'FOSOAuthServerBundle';
    }
}
