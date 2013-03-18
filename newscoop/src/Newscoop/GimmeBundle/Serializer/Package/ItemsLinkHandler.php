<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Serializer\Package;  

use JMS\SerializerBundle\Serializer\VisitorInterface;
use JMS\SerializerBundle\Serializer\Handler\SerializationHandlerInterface;

/**
 * Create uri for items resource.
 */
class ItemsLinkHandler implements SerializationHandlerInterface
{
    private $router;

    public function __construct($router)
    {
        $this->router = $router;
    }

    public function serialize(VisitorInterface $visitor, $data, $type, &$visited)
    {   
        if ($type != 'Newscoop\\Package\\Package') {
            return;
        }

        $uri = $this->router->generate('newscoop_gimme_slideshows_getslideshowitems', array('id' => $data->getId()), true);

        $data->setItemsLink($uri);
    }
}