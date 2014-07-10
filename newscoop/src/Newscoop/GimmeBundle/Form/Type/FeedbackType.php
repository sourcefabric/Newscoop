<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class FeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('subject', null);
        $builder->add('message', null);
        $builder->add('url', null);
        $builder->add('publication', null, array(
            'required' => false,
        ));
        $builder->add('section', null, array(
            'required' => false,
        ));
        $builder->add('article', null, array(
            'required' => false,
        ));
        $builder->add('language', null, array(
            'required' => false,
        ));
        $builder->add('attachment', 'file', array(
            'required' => false,
            'constraints' => array(
                new Assert\File()
            )
        ));
    }

    public function getName()
    {
        return 'feedback';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
        ));
    }
}
