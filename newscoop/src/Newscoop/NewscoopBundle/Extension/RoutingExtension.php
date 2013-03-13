<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\RoutingExtension as SmartyRoutingExtension;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\BlockPlugin;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\ModifierPlugin;

class RoutingExtension extends SmartyRoutingExtension
{
    /**
     * {@inheritdoc}
     */
    public function getPlugins()
    {
        return array(
            new BlockPlugin('smarty_path', $this, 'getPath_block'),
            new ModifierPlugin('smarty_path', $this, 'getPath_modifier'),
            new BlockPlugin('smarty_url', $this, 'getUrl_block'),
            new ModifierPlugin('smarty_url', $this, 'getUrl_modifier')
        );
    }
}
