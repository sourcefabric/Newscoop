<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class StringToArrayTransformer implements DataTransformerInterface
{
    /**
     * Transforms array to string with implode.
     *
     * @param  array $array
     * 
     * @return string
     */
    public function transform($array)
    {
        return implode(',', $array);
    }

    /**
     * 
     * Transforms string to array with explode and coma as delimiter.
     *
     * @param  array $string
     *
     * @return array
     */
    public function reverseTransform($string)
    {
        return explode(',', $string);
    }
}