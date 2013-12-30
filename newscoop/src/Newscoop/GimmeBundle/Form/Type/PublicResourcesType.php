<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PublicResourcesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('routes', 'choice', array(
            'choices'   => $options['choices'],
            'data'      => $options['data'],
            'multiple'  => true,
            'expanded'  => true,
            'label' => 'api.form.PublicResources.label.routes'
        ));


        $builder->add('save', 'submit', array(
            'attr' => array('class' => 'btn btn-success pull-right'),
            'label' => 'api.form.PublicResources.label.save'
        ));
    }

    public function getName()
    {
        return 'public_api_routes';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => false,
            'translation_domain' => 'api'
        ));
    }
}
