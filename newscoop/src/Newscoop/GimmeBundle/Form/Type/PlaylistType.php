<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\GimmeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Newscoop\GimmeBundle\Form\DataTransformer\LanguageToIdTransformer;

class PlaylistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entityManager = $options['em'];
        $transformer = new LanguageToIdTransformer($entityManager);

        $builder
            ->add('name', 'text', array(
                'required' => true,
            ))
            ->add('notes', 'text', array(
                'required' => false,
            ))
            ->add('maxItems', 'integer', array(
                'required' => false,
            ));

        $builder->add(
            $builder->create('language', 'text')
                ->addModelTransformer($transformer)
        );
    }

    public function getName()
    {
        return 'playlist';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
        ))
        ->setRequired(array(
            'em',
        ))
        ->setAllowedTypes(array(
            'em' => 'Doctrine\Common\Persistence\ObjectManager',
        ));
    }
}
