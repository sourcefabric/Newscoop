<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Extension;

use Newscoop\NewscoopBundle\Extension\AsseticExtension;
use Assetic\Factory\AssetFactory;
use Assetic\Asset\AssetInterface;
use Assetic\ValueSupplierInterface;
use NoiseLabs\Bundle\SmartyBundle\Extension\RoutingExtension;
use NoiseLabs\Bundle\SmartyBundle\Exception\RuntimeException;

/**
 * The "dynamic" reincarnation of AsseticExtension.
 *
 * @author Vítor Brandão <vitor@noiselabs.com>
 */
class DynamicAsseticExtension extends AsseticExtension
{
    protected $routingExtension;

    /**
     * Constructor.
     *
     * @param RoutingExtension $routingExtension The routing extension
     * @param AssetFactory     $factory          The asset factory
     * @param boolean          $useController    Handle assets dynamically
     *
     * @see Symfony\Bundle\AsseticBundle\Templating\DynamicAsseticHelper
     */
    public function __construct(RoutingExtension $routingExtension, AssetFactory $factory, $useController = false, $enabledBundles = array(), ValueSupplierInterface $valueSupplier = null)
    {
        $this->routingExtension = $routingExtension;

        parent::__construct($factory, $useController, $valueSupplier);
    }

    /**
     * Returns an URL for the supplied asset.
     *
     * @param AssetInterface $asset   An asset
     * @param array          $options An array of options
     *
     * @return string An echo-ready URL
     */
    protected function getAssetUrl(AssetInterface $asset, array $options = array())
    {
        try {
            return $this->routingExtension->getPath('_assetic_'.$options['name']);
        } catch (\Exception $e) {
            throw RuntimeException::createFromPrevious($e);
        }
    }
}
