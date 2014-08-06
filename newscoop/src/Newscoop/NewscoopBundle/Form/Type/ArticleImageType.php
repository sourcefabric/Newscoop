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

class ArticleImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('number', 'number', array(
            'label' => 'articles.images.edit.form.number',
            'trim' => true,
            'error_bubbling' => true,
        ));

        $builder->add('caption', 'text', array(
            'label' => 'articles.images.edit.form.caption',
            'error_bubbling' => true,
        ));

        $builder->add('language', 'hidden', array(
            'error_bubbling' => true
        ));

        $builder->add('status', 'choice', array(
            'choices'   => array(
                'Y' => 'articles.images.edit.form.status_approved',
                'N' => 'articles.images.edit.form.status_unapproved'
            ),
            'error_bubbling' => true,
            'multiple' => false,
            'expanded' => false,
            'required' => true,
            'label' => 'articles.images.edit.form.status',
        ));

        $builder->add('description', 'text', array(
            'label' => 'articles.images.edit.form.description',
            'error_bubbling' => true,
        ));

        $builder->add('photographer', 'text', array(
            'label' => 'articles.images.edit.form.photographer',
            'error_bubbling' => true,
        ));

        $builder->add('photographer_url', 'text', array(
            'label' => 'articles.images.edit.form.photographer_url',
            'error_bubbling' => true,
        ));

        $builder->add('place', 'text', array(
            'label' => 'articles.images.edit.form.place',
            'error_bubbling' => true,
        ));

        $builder->add('date', 'text', array(
            'label' => 'articles.images.edit.form.date',
            'error_bubbling' => true,
            'attr' => array(
                'placeholder' => date('Y-m-d')
            )
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'articles',
        ));
    }

    public function getName()
    {
        return 'article_image';
    }
}
