<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\MailChimp\ListView;
use Newscoop\MailChimp\MemberView;

/**
 * Newsletter Action Helper
 */
class Action_Helper_Newsletter extends Zend_Controller_Action_Helper_Abstract
{
    /**
     */
    public function initForm(Zend_Form $form, ListView $list, MemberView $member = null)
    {
        $newsletter = new Zend_Form_SubForm();
        $newsletter->addElement('checkbox', 'subscriber', array(
            'label' => 'I want to receive newsletter',
        ));

        foreach ($list->groups as $group) {
            $type = $group['form_field'] == 'radio' ? 'radio' : 'multiCheckbox';
            $newsletter->addElement($type, $group['name'], array(
                'label' => $group['name'],
                'multioptions' => $group['groups'],
            ));
        }

        if ($member !== null) {
            $newsletter->setDefaults(array_merge((array) $member, $member->groups));
        }

        $form->addSubForm($newsletter, 'newsletter');
    }
}
