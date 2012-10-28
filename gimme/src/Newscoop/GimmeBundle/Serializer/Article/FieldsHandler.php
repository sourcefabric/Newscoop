<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Serializer\Article;  

use JMS\SerializerBundle\Serializer\VisitorInterface;
use JMS\SerializerBundle\Serializer\Handler\SerializationHandlerInterface;

/**
 * Create array of Article type fields.
 */
class FieldsHandler implements SerializationHandlerInterface
{

    public function serialize(VisitorInterface $visitor, $data, $type, &$visited)
    {   
        if ($type != 'Newscoop\\Entity\\Article') {
            return;
        }

        $GLOBALS['g_campsiteDir'] = realpath(__DIR__ . '/../../../../../../newscoop/');

        $articleData = new \ArticleData($data->getType(), $data->getNumber(), $data->getLanguageId());
        if (count($articleData->getUserDefinedColumns()) == 0) {
            $data->setFields(null);
            return;
        }

        $fields = array();
        foreach ($articleData->getUserDefinedColumns() as $column) {
            $fields[$column->getPrintName()] = $articleData->getFieldValue($column->getPrintName());
        }

        $data->setFields($fields);
    }
}