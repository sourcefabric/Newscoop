<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\User;

/**
 */
class Admin_Form_UserCriteria extends Zend_Form
{
    public function init()
    {
        $this->addElement('text', 'q');

        $this->addElement('select', 'status', array(
            'multioptions' => array(
                User::STATUS_ACTIVE => getGS('active'),
                User::STATUS_INACTIVE => getGS('pending'),
                User::STATUS_DELETED => getGS('deleted'),
            ),
        ));

        $this->addElement('select', 'groups', array(
            'multioptions' => array(
                null => getGS('Any group'),
            ),
        ));
    }
}
