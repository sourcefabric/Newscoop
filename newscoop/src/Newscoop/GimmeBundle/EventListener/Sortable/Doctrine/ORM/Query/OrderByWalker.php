<?php

namespace Newscoop\GimmeBundle\EventListener\Sortable\Doctrine\ORM\Query;

use Doctrine\ORM\Query\TreeWalkerAdapter;
use Doctrine\ORM\Query\AST\SelectStatement;
use Doctrine\ORM\Query\AST\PathExpression;
use Doctrine\ORM\Query\AST\OrderByItem;
use Doctrine\ORM\Query\AST\OrderByClause;

/**
 * OrderBy Query TreeWalker for Sortable functionality
 * in doctrine paginator
 */
class OrderByWalker extends TreeWalkerAdapter
{
    /**
     * Walks down a SelectStatement AST node, modifying it to
     * sort the query like requested by url
     *
     * @param SelectStatement $AST
     * @return void
     */
    public function walkSelectStatement(SelectStatement $AST)
    {
        $query = $this->_getQuery();
        $sort = $query->getHint('newscoop.api.sort');
        $orderByItems = array();

        $components = $this->_getQueryComponents();
        foreach ($sort as $field => $direction) {
            $entityData = $this->findAliasForField($field);
            $realField = \Newscoop\Gimme\PropertyMatcher::match($entityData[1], $field);

            if ($field != $realField) {
                $entityData = $this->findAliasForField($realField);
                $field = $realField;
            }

            $alias = $entityData[0];

            if ($alias !== false) {
                if (!array_key_exists($alias, $components)) {
                    throw new \UnexpectedValueException("There is no component aliased by [{$alias}] in the given Query");
                }
                $meta = $components[$alias];
                if (!$meta['metadata']->hasField($field)) {
                    throw new \UnexpectedValueException("There is no such field [{$field}] in the given Query component, aliased by [$alias]");
                }
            } else {
                if (!array_key_exists($field, $components)) {
                    throw new \UnexpectedValueException("There is no component field [{$field}] in the given Query");
                }
            }

            if ($alias !== false) {
                $pathExpression = new PathExpression(PathExpression::TYPE_STATE_FIELD, $alias, $field);
                $pathExpression->type = PathExpression::TYPE_STATE_FIELD;
            } else {
                $pathExpression = $field;
            }

            $orderByItem = new OrderByItem($pathExpression);
            $orderByItem->type = $direction;
            $orderByItems[] = $orderByItem;
        }

        $AST->orderByClause = new OrderByClause($orderByItems);
    }

    /**
     * Find alias for choosen field
     * @param  string $field Field name
     * @return string         array with alias or field name and class namespace
     */
    private function findAliasForField($field)
    {
        $components = $this->_getQueryComponents();

        foreach (array_keys($components) as $alias) {
            $meta = $components[$alias];
            if ($meta['metadata']->hasField($field)) {
                return array($alias, $meta['metadata']->name);
            } else {
                 return array($field, $meta['metadata']->name);
            }
        }

        return array($field, $components['metadata']->name);
    }
}