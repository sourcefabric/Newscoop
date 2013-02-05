<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop;

use SmartyBC;

/**
 * Newscoop preconfigured Smarty class
 */
class Smarty extends SmartyBC
{
    /**
     */
    public function __construct()
    {
        parent::__construct();

        $this->left_delimiter = '{{';
        $this->right_delimiter = '}}';
        $this->auto_literal = false;

        $this->addPluginsDir(__DIR__ . '/../../include/smarty/campsite_plugins');
    }
}
