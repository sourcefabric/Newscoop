<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class ConfigureMenuEvent extends Event
{
    private $factory;
    private $menu;
    private $router;

    /**
     * @param \Knp\Menu\FactoryInterface $factory
     * @param \Knp\Menu\ItemInterface $menu
     * @param \Symfony\Bundle\FrameworkBundle\Routing\Router $router
     */
    public function __construct(FactoryInterface $factory, ItemInterface $menu, Router $router)
    {
        $this->factory = $factory;
        $this->menu = $menu;
        $this->router = $router;
    }

    /**
     * @return \Knp\Menu\FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return \Knp\Menu\ItemInterface
     */
    public function getMenu()
    {
        return $this->menu;
    }

    public function getRouter()
    {
        return $this->router;
    }
}