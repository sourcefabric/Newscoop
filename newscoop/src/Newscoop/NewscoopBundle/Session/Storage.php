<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Session;

use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

/**
 * Newscoop Session Storage adapter.
 * Newscoop still uses the Zend_Session and we now have a custom wrapper for it (PhpBridgeSessionStorage). 
 * This class provides support for a custom session lifetime value.
 */
class Storage extends PhpBridgeSessionStorage
{
    /**
     * @param array  														   $options
     * @param AbstractProxy|NativeSessionHandler|\SessionHandlerInterface|null $handler
     * @param MetadataBag                                                      $metaBag MetadataBag
    */
    public function __construct(array $options = array(), $handler = null, MetadataBag $metaBag = null)
    {   
        $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');

        $seconds = $preferencesService->SiteSessionLifeTime;
        
        $options['cookie_lifetime'] = $seconds;
        $options['gc_maxlifetime'] = $seconds;

        $this->setMetadataBag($metaBag);
        $this->setOptions($options);
        $this->setSaveHandler($handler);
    }
}