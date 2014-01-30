<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Form\Type\ImageFilterOptions;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImageFilterThumbnailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('width', null, array(
            'label' => 'Image Width',
            'attr' => array(
                'class' => 'form-control',
                'style' => 'width: 150px;'
            )
        ));

        $builder->add('height', null, array(
            'label' => 'Image height',
            'attr' => array(
                'class' => 'form-control',
                'style' => 'width: 150px;'
            )
        ));

        $builder->add('mode', 'choice', array(
            'choices'   => array(
                'outbound'   => 'Outbound',
                'inset' => 'Inset',
            ),
            'label' => 'Choose thumbnail mode',
            'attr' => array(
                'class' => 'form-control',
                'style' => 'width: 200px;'
            )
        ));

        $builder->add('save', 'submit', array(
            'attr' => array('class' => 'btn btn-success pull-right'),
        ));
    }

    public function getName()
    {
        return 'ImageFilterThumbnail';
    }
}