<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImageFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, array(
            'label' => 'Set filter name'
        ));
        $builder->add('type', 'choice', array(
            'choices'   => array(
                'thumbnail'   => 'Thumbnail',
                'resize' => 'Resize',
                'relative_resize'   => 'RelativeResize',
                'paste'   => 'Paste',
                'chain'   => 'Chain',
                'crop'   => 'Crop',
            ),
            'label' => 'Choose filter type'
        ));

        $builder->add('save', 'submit', array(
            'attr' => array('class' => 'save'),
        ));
    }

    public function getName()
    {
        return 'image_filter';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {    
        $resolver->setDefaults(array(
            'data_class'        => 'Newscoop\Entity\ImageFilters',
        ));
    }
}