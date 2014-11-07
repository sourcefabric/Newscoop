<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PublicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array(
            'label' => 'publications.form_type.label.name',
            'trim' => true,
            'error_bubbling' => true,
            'constraints' => array(
               new NotBlank(),
               new Length(array('min' => 3, 'minMessage' => 'type_publications.name.min')),
           ),
        ));

        if ($options['publication_id']) {
            $builder->add('alias', 'entity', array(
                'class' => 'Newscoop\Entity\Aliases',
                'property' => 'name',
                'query_builder' => function(EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('a')
                        ->where('a.publication = :publicationId')
                        ->setParameter('publicationId', $options['publication_id'])
                        ->orderBy('a.name', 'ASC');
                },
                'error_bubbling' => true,
            ));
        }

        $builder->add('language', 'entity', array(
            'class' => 'Newscoop\Entity\Language',
            'property' => 'name',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('a')
                    ->orderBy('a.name', 'ASC');
            },
            'error_bubbling' => true,
        ));

        $builder->add('url_type', 'choice', array(
            'choices'   => array('1' => 'publications.form_type.label.template_path', '2' => 'publications.form_type.label.short_names'),
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'pub',
            'publication_id' => null
        ));
    }

    public function getName()
    {
        return 'publication';
    }
}
