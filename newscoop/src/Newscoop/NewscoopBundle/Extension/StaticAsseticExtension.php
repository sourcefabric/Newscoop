<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Extension;

use Assetic\Factory\AssetFactory;
use Assetic\Asset\AssetInterface;
use Assetic\ValueSupplierInterface;
use NoiseLabs\Bundle\SmartyBundle\Extension\AssetsExtension;
use Newscoop\NewscoopBundle\Extension\AsseticExtension;

/**
 * The "static" reincarnation of AsseticExtension.
 *
 * @author Vítor Brandão <vitor@noiselabs.com>
 */
class StaticAsseticExtension extends AsseticExtension
{
    protected $assetsExtension;

    /**
     * Constructor.
     *
     * @param AssetsExtension $assetsExtension The assets extension
     * @param AssetFactory    $factory         The asset factory
     * @param boolean         $useController   Handle assets dynamically
     *
     * @see Symfony\Bundle\AsseticBundle\Templating\StaticAsseticHelper
     */
    public function __construct(AssetsExtension $assetsExtension, AssetFactory $factory, $useController = false, $enabledBundles = array(), ValueSupplierInterface $valueSupplier = null)
    {
        $this->assetsExtension = $assetsExtension;

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
        return $this->assetsExtension->getAssetUrl($asset->getTargetPath(), isset($options['package']) ? $options['package'] : null);
    }
}
