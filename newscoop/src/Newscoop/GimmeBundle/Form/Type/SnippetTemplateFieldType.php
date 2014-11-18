<?php
/**
 * @package Newscoop\GimmeBundle
 * @author Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class SnippetTemplateFieldType extends AbstractType
{
    private $patch;

    public function __construct(array $options = array())
    {
        $this->patch = false;
        if (array_key_exists('patch', $options)) {
            $this->patch = $options['patch'];
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $defaultRequired = true;
        $constraints = array(
            new NotBlank
        );

        if ($this->patch) {
            $defaultRequired = false;
            $constraints = array();
        }

        $builder->add('name', null, array(
            'required' => $defaultRequired,
            'constraints'  => $constraints,
        ));

        $builder->add('type', 'choice', array(
            'choices' => array('integer' => 'integer', 'text' => 'text', 'textarea' => 'textarea', 'url' => 'url', 'boolean' => 'boolean'),
            'required' => $defaultRequired,
            'constraints'  => $constraints,
        ));

        $builder->add('scope', 'choice', array(
            'choices' => array('frontend' => 'frontend', 'backend' => 'backend'),
            'required' => $defaultRequired,
            'constraints'  => $constraints,
        ));

        $builder->add('required', 'checkbox', array(
            'required' => $defaultRequired,
            'constraints'  => $constraints,
        ));

        $callback = function(FormEvent $event) {
            if (null === $event->getData()) { // check if the Form's field is empty
                if (is_bool($event->getForm()->getData())) { // check if it's a boolean
                    if ($event->getForm()->getData()) {
                        $event->setData('1');
                    } else {
                         $event->setData('0');
                    }
                } else { // set the data back
                    $event->setData($event->getForm()->getData());
                }
            }
        };

        $builder->get('name')->addEventListener(FormEvents::PRE_SUBMIT, $callback);
        $builder->get('type')->addEventListener(FormEvents::PRE_SUBMIT, $callback);
        $builder->get('scope')->addEventListener(FormEvents::PRE_SUBMIT, $callback);
        $builder->get('required')->addEventListener(FormEvents::PRE_SUBMIT, $callback);
    }

    public function getName()
    {
        return 'snippetField';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'data_class' => 'Newscoop\Entity\Snippet\SnippetTemplate\SnippetTemplateField',
        ));
    }
}