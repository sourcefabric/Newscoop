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
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Doctrine\ORM\EntityRepository;

class SnippetTemplateType extends AbstractType
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

        $builder->add('fields', 'collection', array(
         'type' => new SnippetTemplateFieldType(array('patch'=>$this->patch)),
         'required' => $defaultRequired,
         'constraints' => $constraints,
         'allow_add' => true
        ));

        $builder->add('name', 'text', array(
            'required' => $defaultRequired,
            'constraints'  => $constraints,
        ));

      $builder->add('templateCode', 'textarea', array(
            'required' => $defaultRequired,
            'constraints'  => $constraints,
        ));

        $builder->add('enabled', 'checkbox', array(
            'required' => false,
        ));
    }

    public function getName()
    {
        return 'snippetTemplate';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'data_class' => 'Newscoop\Entity\Snippet\SnippetTemplate',
        ));
    }
}