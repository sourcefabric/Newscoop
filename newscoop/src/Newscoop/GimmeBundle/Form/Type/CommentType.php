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
use Symfony\Component\Validator\Constraints as Assert;

class CommentType extends AbstractType
{
    private $patch;

    public function __construct(array $options = array())
    {
        if (array_key_exists('patch', $options)) {
            $this->patch = $options['patch'];
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $defaultRequired = ($this->patch) ? false : true;

        $builder->add('subject', null, array(
            'required' => false,
        ));
        $builder->add('message', null, array(
            'required' => $defaultRequired,
        ));

        $builder->add('name', null, array(
            'required' => false,
        ));

        $builder->add('email', null, array(
            'required' => false,
        ));

        $builder->add('parent', null, array(
            'required' => false,
        ));

        $builder->add('recommended', 'integer', array(
            'required' => false,
        ));

        $builder->add('status', null, array(
            'required' => false,
        ));
    }

    public function getName()
    {
        return 'comment';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
        ));
    }
}
