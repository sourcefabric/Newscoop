<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Gimme;

use Symfony\Component\Yaml\Parser;

/**
 * Match Newscoop API property name from response with corresponding database field.
 */
class PropertyMatcher
{

    /**
     * Match properties with fields names
     * @param  string $class    class namespace
     * @param  string $property property name
     * @return string           matched field name or property
     */
    public static function match($class, $property)
    {
        $namespace = explode('\\', $class);
        $class = $namespace[count($namespace)-1];
        $yaml = new Parser();

        // TODO: cache for this.
        // http://php-and-symfony.matthiasnoback.nl/2012/05/symfony2-config-component-using-filelocator-loaders-and-loaderresolver/
        $entityDescription = $yaml->parse(file_get_contents(__DIR__.'/../../../src/Newscoop/GimmeBundle/Resources/config/serializer/newscoop/'.$class.'.yml'));

        foreach ($entityDescription as $class => $classDescription) {
            foreach ($classDescription['properties'] as $field => $description) {
                if (array_key_exists('serialized_name', $description)) {
                    if ($description['serialized_name'] == $property) {
                        return $field;
                    }
                }
            }
        }

        return $property;
    }

}