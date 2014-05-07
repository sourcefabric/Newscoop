<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Serializer\Article;

use JMS\Serializer\JsonSerializationVisitor;

/**
 * Create array of Article type fields.
 */
class FieldsHandler
{

    public function serializeToJson(JsonSerializationVisitor $visitor, $data, $type)
    {
        $GLOBALS['g_campsiteDir'] = realpath(__DIR__ . '/../../../../../../newscoop/');

        $articleData = new \ArticleData($data->type, $data->number, $data->languageId);
        if (count($articleData->getUserDefinedColumns()) == 0) {
            return null;
        }

        $fields = array();
        foreach ($articleData->getUserDefinedColumns() as $column) {
            $fields[$column->getPrintName()] = $articleData->getFieldValue($column->getPrintName());
        }

        return $fields;
    }
}