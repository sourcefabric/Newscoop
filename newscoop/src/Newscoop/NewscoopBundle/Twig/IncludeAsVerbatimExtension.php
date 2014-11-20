<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Twig;

use Twig_Environment;
use Twig_Extension;

class IncludeAsVerbatimExtension extends \Twig_Extension
{
    /**
     * Returns a list of global functions to add to the existing list.
     *
     * @return \Twig_SimpleFunction[] An array of global functions
     */
    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction( 'include_as_verbatim', array( $this, 'includeAsVerbatim' ), array( 'needs_environment' => true, 'is_safe' => array( 'html' ) ) )
        );
    }

    function includeAsVerbatim( Twig_Environment $env, $location) {
        $contents = $env->getLoader()->getSource( $location );
        return "{$contents}";
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName() {
        return 'include_as_verbatim_extension';
    }
}