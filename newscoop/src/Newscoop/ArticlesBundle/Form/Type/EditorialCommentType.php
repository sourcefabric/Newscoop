<?php
/**
 * @package Newscoop\ArticlesBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric ź.u.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\ArticlesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EditorialCommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('comment', null, array(
            'required' => true,
        ));
        $builder->add('resolved', 'checkbox', array(
            'required' => false,
        ));
        $builder->add('parent', 'number', array(
            'required' => false,
        ));
    }

    public function getName()
    {
        return 'editorial_comment';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
        ));
    }
}
