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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use ArticleTypeField;

class ArticleFieldsType extends AbstractType
{
    private $articleData;

    public function __construct($articleData = null)
    {
        $this->articleData = $articleData;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (is_null($this->articleData)) {
            throw new \LogicException(
                'The ArticleFieldsType cannot be used without article data!'
            );
        }

        $articleData = $this->articleData;
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($articleData) {
                $form = $event->getForm();

                foreach ($articleData->getUserDefinedColumns(true) as $articleTypeField) {
                    switch ($articleTypeField->getType()) {
                        case ArticleTypeField::TYPE_TEXT:
                        case ArticleTypeField::TYPE_COMPLEX_DATE:
                        case ArticleTypeField::TYPE_DATE:
                            $form->add(substr($articleTypeField->getName(), 1), 'text', array(
                                'required' => false,
                            ));
                            break;

                        case ArticleTypeField::TYPE_LONGTEXT:
                        case ArticleTypeField::TYPE_BODY:
                            $form->add(substr($articleTypeField->getName(), 1), 'textarea', array(
                                'required' => false,
                            ));
                            break;

                        case ArticleTypeField::TYPE_TOPIC:
                        case ArticleTypeField::TYPE_NUMERIC:
                            $form->add(substr($articleTypeField->getName(), 1), 'integer', array(
                                'required' => false,
                            ));
                            break;

                        case ArticleTypeField::TYPE_SWITCH:
                            $form->add(substr($articleTypeField->getName(), 1), 'integer', array(
                                'required' => false,
                            ));
                            break;
                    }
                }
            }
        );
    }

    public function getName()
    {
        return 'fields';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
        ));
    }
}
