<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Action_Helper_Config extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Get config
     *
     * @param string $key
     * @return Zend_Config
     */
    public function direct($key)
    {
        $options = $this->getActionController()->getInvokeArg('bootstrap')
            ->getOption($key);
        return new Zend_Config($options);
    }
}
