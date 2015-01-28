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

class ArticleType extends AbstractType
{
    private $articleData;

    public function __construct($articleData = null)
    {
        $this->articleData = $articleData;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name', 'text', array(
            'required' => true,
        ))
        ->add('language', 'text', array(
            'required' => true,
        ))
        ->add('publication', 'number', array(
            'required' => true,
        ))
        ->add('issue', 'number', array(
            'required' => false,
        ))
        ->add('section', 'number', array(
            'required' => false,
        ))
        ->add('comments_enabled', 'integer', array(
            'required' => false,
        ))
        ->add('comments_locked', 'integer', array(
            'required' => false,
        ))
        ->add('type', 'text', array(
            'required' => true,
        ))
        ->add('onFrontPage', 'integer', array(
            'required' => false,
        ))
        ->add('onSection', 'integer', array(
            'required' => false,
        ))
        ->add('keywords', 'text', array(
            'required' => false,
        ));

        if (!is_null($this->articleData)) {
            $builder->add('fields', new ArticleFieldsType($this->articleData), array(
                'required' => false,
            ));
        }
    }

    public function getName()
    {
        return 'article';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
        ));
    }
}
