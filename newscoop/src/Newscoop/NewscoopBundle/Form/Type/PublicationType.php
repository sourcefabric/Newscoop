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
            'required' => true,
        ));

        if ($options['publication_id']) {
            $builder->add('defaultAlias', 'entity', array(
                'class' => 'Newscoop\Entity\Aliases',
                'property' => 'name',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('a')
                        ->where('a.publication = :publicationId')
                        ->setParameter('publicationId', $options['publication_id'])
                        ->orderBy('a.name', 'ASC');
                },
                'error_bubbling' => true,
                'label' => 'publications.form_type.label.alias',
                'required' => true,
            ));
        } else {
            $builder->add('defaultAlias', 'text', array(
                'error_bubbling' => true,
                'label' => 'publications.form_type.label.alias',
                'required' => true,
            ));
        }

        $builder->add('language', 'entity', array(
            'class' => 'Newscoop\Entity\Language',
            'property' => 'name',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('a')
                    ->orderBy('a.name', 'ASC');
            },
            'error_bubbling' => true,
            'required' => true,
        ))
        ->add('urlTypeId', 'choice', array(
            'choices'   => array('2' => 'publications.form_type.label.short_names', '1' => 'publications.form_type.label.template_path'),
            'label' => 'publications.form_type.label.url_type',
            'error_bubbling' => true,
            'required' => true,
        ))
        ->add('seoChoices', 'choice', array(
            'choices'   => array(
                'name' => 'publications.form_type.label.seo_article_title',
                'keywords' => 'publications.form_type.label.seo_article_keywords',
                'topics' => 'publications.form_type.label.seo_article_topics'
            ),
            'label' => 'publications.form_type.label.seo',
            'expanded' => true,
            'multiple'  => true,
            'error_bubbling' => true,
            'required' => false,
        ))
        ->add('metaTitle', null, array(
            'label' => 'publications.form_type.label.meta_title',
            'error_bubbling' => true,
            'required' => false,
        ))
        ->add('metaKeywords', null, array(
            'label' => 'publications.form_type.label.meta_keywords',
            'error_bubbling' => true,
            'required' => false,
        ))
        ->add('metaDescription', 'textarea', array(
            'label' => 'publications.form_type.label.meta_description',
            'required' => false,
            'error_bubbling' => true,
        ))
        ->add('moderator_to', 'email', array(
            'label' => 'publications.form_type.label.moderator_to',
            'error_bubbling' => true,
            'required' => false,
        ))
        ->add('moderator_from', 'email', array(
            'label' => 'publications.form_type.label.moderator_from',
            'error_bubbling' => true,
            'required' => false,
        ))
        ->add('commentsEnabled', null, array(
            'label' => 'publications.form_type.label.comments_enabled',
            'error_bubbling' => true,
            'required' => false,
        ))
        ->add('public_enabled', 'checkbox', array(
            'label' => 'publications.form_type.label.public_comments_enabled',
            'error_bubbling' => true,
            'required' => false,
        ))
        ->add('commentsArticleDefaultEnabled', null, array(
            'label' => 'publications.form_type.label.comments_article_default_enabled',
            'error_bubbling' => true,
            'required' => false,
        ))
        ->add('commentsSubscribersModerated', null, array(
            'label' => 'publications.form_type.label.comments_subscribers_moderated',
            'error_bubbling' => true,
            'required' => false,
        ))
        ->add('commentsPublicModerated', null, array(
            'label' => 'publications.form_type.label.comments_public_moderated',
            'error_bubbling' => true,
            'required' => false,
        ))
        ->add('commentsCaptchaEnabled', null, array(
            'label' => 'publications.form_type.label.comments_captcha_enabled',
            'error_bubbling' => true,
            'required' => false,
        ))
        ->add('commentsSpamBlockingEnabled', null, array(
            'label' => 'publications.form_type.label.comments_spam_blocking_enabled',
            'error_bubbling' => true,
            'required' => false,
        ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'pub',
            'publication_id' => null,
        ));
    }

    public function getName()
    {
        return 'publication';
    }
}
