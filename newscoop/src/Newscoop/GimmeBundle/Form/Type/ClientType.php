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
use Doctrine\ORM\EntityRepository;
use Newscoop\GimmeBundle\Form\DataTransformer\StringToArrayTransformer;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new StringToArrayTransformer();
        $builder->add('name', null, array(
            'label' => 'api.form.client.label.name'
        ));

        $builder->add('publication', 'entity', array(
            'class' => 'Newscoop\Entity\Publication',
            'property' => 'name',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('p')
                    ->orderBy('p.name', 'ASC');
            },
            'label' => 'api.form.client.label.publication'
        ));

        $builder->add(
            $builder->create('redirectUris', 'text', array(
                'label' => 'api.form.client.label.redirectUris'
            ))
            ->addModelTransformer($transformer)
        );

        $builder->add('trusted', 'checkbox', array(
            'label' => 'api.form.client.label.trusted',
            'required' => false
        ));

        $builder->add('save', 'submit', array(
            'attr' => array('class' => 'btn btn-success pull-right'),
            'label' => 'api.form.client.label.save'
        ));
    }

    public function getName()
    {
        return 'client';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'api'
        ));
    }
}
