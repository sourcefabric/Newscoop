<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CommentsFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pending', 'checkbox', array(
            'label' => 'comments.label.new',
            'required'  => false,
        ))
        ->add('approved', 'checkbox', array(
            'label' => 'comments.label.approved',
            'required'  => false,
        ))
        ->add('hidden', 'checkbox', array(
            'label' => 'comments.label.hidden',
            'required'  => false,
        ))
        ->add('recommended', 'checkbox', array(
            'label' => 'comments.label.recommended',
            'required'  => false,
        ))
        ->add('unrecommended', 'checkbox', array(
            'label' => 'comments.label.unrecommended',
            'required'  => false,
        ))
        ->add('filterButton', 'submit', array(
            'label' => 'comments.btn.filter'
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'new_comments',
            'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return 'filterForm';
    }
}