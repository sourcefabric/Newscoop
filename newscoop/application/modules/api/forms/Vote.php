<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Api_Form_Vote extends Zend_Form
{
    public function init()
    {
        $this->addElement('text', 'debateId', array(
            'required' => true,
        ));

        $this->addElement('text', 'vote', array(
            'required' => true,
        ));

        $this->addElement('text', 'deviceId', array(
            'required' => true,
        ));

        $this->addElement('text', 'userId', array(
        ));
    }
}
