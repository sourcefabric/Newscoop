<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\BlockPlugin;
use NoiseLabs\Bundle\SmartyBundle\Extension\AsseticExtension as SmartyAsseticExtension;

/**
 * {@inheritdoc}
 */
abstract class AsseticExtension extends SmartyAsseticExtension
{
    /**
     * {@inheritdoc}
     */
    public function getPlugins()
    {
        return array(
            new BlockPlugin('smarty_javascripts', $this, 'javascriptsBlock'),
            new BlockPlugin('smarty_stylesheets', $this, 'stylesheetsBlock'),
            new BlockPlugin('smarty_image', $this, 'imageBlock'),
        );
    }
}
