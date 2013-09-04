<?php

namespace Newscoop\NewscoopBundle\ORM;

use Doctrine\DBAL\Types\Type as BaseType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Mapping type for spatial POINT objects
 */
class PointType extends BaseType {

    const POINT = 'point';

    /**
     * Gets the name of this type.
     *
     * @return string
     */
    public function getName() 
    {
        return self::POINT;
    }

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array $fieldDeclaration The field declaration.
     * @param AbstractPlatform $platform The currently used database platform.
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform) 
    {
        return 'POINT';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform) 
    {

        if($value == '') {
            return null;
        }

        $data = unpack('x/x/x/x/corder/Ltype/dlat/dlon', $value);
        return new \Newscoop\NewscoopBundle\ORM\Point($data['lat'], $data['lon']);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform) 
    {
        if (!$value) return;
        
        return pack('xxxxcLdd', '0', 1, $value->getLatitude(), $value->getLongitude());
    }
}