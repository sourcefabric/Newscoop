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

class SnippetFieldType extends AbstractType
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
        $defaultRequired = false;
        $constraints = array();

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();
                $defaultRequired = false;
                $constraints = array();
                $fieldType = null;
                if (!is_null($data)) {
                    $fieldType = $data->getFieldType();
                    $patch = (!is_null($this)) ? !$this->patch : false;
                    if ($data->isRequired() && $patch) {
                        $defaultRequired = true;
                        $constraints = array(
                            new NotBlank
                        );
                    }
                }
                $form->add('data', $fieldType, array(
                    'required' => $defaultRequired,
                    'constraints'  => $constraints,

                ));
            }
        );
    }

    public function getName()
    {
        return 'snippetField';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'data_class' => 'Newscoop\Entity\Snippet\SnippetField',
        ));
    }
}