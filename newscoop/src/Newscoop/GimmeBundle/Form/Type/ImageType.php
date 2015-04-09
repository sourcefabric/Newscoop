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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('description', null, array(
            'required' => false,
        ));
        $builder->add('photographer', null, array(
            'required' => false,
        ));
        $builder->add('photographer_url', null, array(
            'required' => false,
        ));
        $builder->add('place', null, array(
            'required' => false,
        ));
        $builder->add('image', 'file', array(
            'constraints' => array(
                new Assert\File(),
                new Assert\NotNull()
            )
        ));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $image = $options['image'];
            $form = $event->getForm();

            $form->remove('image');

            if ($image && null !== $image->getId()) {
                $form->add('image', 'file', array(
                    'required' => false
                ));
            } else {
                $form->add('image', 'file', array(
                    'constraints' => array(
                        new Assert\File(),
                        new Assert\NotNull()
                    )
                ));
            }
        });
    }

    public function getName()
    {
        return 'image';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'image' => null
        ));
    }
}
