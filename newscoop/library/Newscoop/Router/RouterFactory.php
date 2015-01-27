<?php
/**
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Router;

/**
 * Zend Router wrapper for setting in container.
 */
class RouterFactory
{
    public static function initRouter($container) {
        $front = \Zend_Controller_Front::getInstance();
        $router = $front->getRouter();

        $router->addRoute(
            'admin',
            new \Zend_Controller_Router_Route(
                'admin/:controller/:action/*',
                array(
                    'module' => 'admin',
                    'controller' => 'legacy',
                    'action' => 'index',
                )
            )
        );

        $router->addDefaultRoutes();

        $router->addRoute(
            'content',
            new \Zend_Controller_Router_Route(':language/:issue/:section/:articleNo/:articleUrl', array(
                'module' => 'default',
                'controller' => 'legacy',
                'action' => 'index',
                'articleUrl' => null,
                'articleNo' => null,
                'section' => null,
                'issue' => null,
                'language' => null,
            ), array(
                'language' => '[a-z]{2}',
            ))
        );

        $router->addRoute(
            'confirm-email',
            new \Zend_Controller_Router_Route('confirm-email/:user/:token', array(
                'module' => 'default',
                'controller' => 'register',
                'action' => 'confirm',
            ))
        );

        $router->addRoute(
            'topic',
            new \Zend_Controller_Router_Route(':language/topic/:id/:topicName', array(
                'module' => 'default',
                'controller' => 'topic',
                'action' => 'articles',
                'topicName' => null,
            ), array(
                'language' => '[a-z]{2}',
            ))
        );

        $router->addRoute(
            'author',
            new \Zend_Controller_Router_Route('author/:author', array(
                'module' => 'default',
                'controller' => 'author',
                'action' => 'profile',
            ))
        );

        $router->addRoute(
            'user',
            new \Zend_Controller_Router_Route('user/profile/:username/:action', array(
                'module' => 'default',
                'controller' => 'user',
                'action' => 'profile',
            ))
        );

        $image = $container->getParameter('image');
        $router->addRoute('image',
            new \Zend_Controller_Router_Route_Regex($image['cache_url'] . '/(.*)', array(
                'module' => 'default',
                'controller' => 'image',
                'action' => 'cache',
            ), array(
                1 => 'src',
            ), $image['cache_url'] . '/%s')
        );

        $router->addRoute('rest',
            new \Zend_Rest_Route($front, array(), array(
                'admin' => array(
                    'slideshow-rest',
                    'subscription-rest',
                    'subscription-section-rest',
                    'subscription-ip-rest',
                ),
            ))
        );

        $router->addRoute(
            'search',
            new \Zend_Controller_Router_Route(
                ':language/search',
                array(
                    'module' => 'default',
                    'controller' => 'search',
                    'action' => 'index',
                    'language' => null,
                ), array(
                    'language' => '[a-z]{2}',
                )
            )
        );

        return $router;
    }
}
