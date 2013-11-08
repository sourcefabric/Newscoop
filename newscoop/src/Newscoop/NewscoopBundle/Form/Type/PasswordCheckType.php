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

class PasswordCheckType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('password', 'password', array(
            'required' => true,
        ));
    }

    public function getName()
    {
        return 'password_check_recovery_form';
    }
}