<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PasswordRecoveryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email', array(
            'required' => true
        ));
    }

    public function getName()
    {
        return 'password_recovery_form';
    }
}