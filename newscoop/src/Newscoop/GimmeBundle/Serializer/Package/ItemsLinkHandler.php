<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Serializer\Package;  

use JMS\Serializer\JsonSerializationVisitor;

/**
 * Create uri for items resource.
 */
class ItemsLinkHandler
{
    protected $router;

    public function __construct($router)
    {
        $this->router = $router;
    }

    public function serializeToJson(JsonSerializationVisitor $visitor, $data, $type)
    {
        return $uri = $this->router->generate('newscoop_gimme_slideshows_getslideshowitems', array(
            'id' => $data->id
        ), true);
    }
}
