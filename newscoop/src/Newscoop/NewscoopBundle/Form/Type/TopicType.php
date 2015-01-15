<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class TopicType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array(
            'required' => true,
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Length(array(
                    'max' => 60,
                ))
            ),
            'error_bubbling' => true
        ));
        $builder->add('locale', null, array(
            'required' => false,
            'constraints' => array(
                new Assert\Length(array(
                    'max' => 5,
                ))
            ),
            'error_bubbling' => true
        ));
        $builder->add('description', 'textarea', array('required' => false));
        $builder->add('parent', null, array('required' => false));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return 'topic';
    }
}
