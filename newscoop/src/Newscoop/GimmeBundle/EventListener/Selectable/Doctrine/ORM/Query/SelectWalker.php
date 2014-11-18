<?php

namespace Newscoop\GimmeBundle\EventListener\Selectable\Doctrine\ORM\Query;

use Doctrine\ORM\Query\TreeWalkerAdapter;
use Doctrine\ORM\Query\AST\SelectStatement;
use Doctrine\ORM\Query\AST\PathExpression;
use Doctrine\ORM\Query\AST\SelectExpression;

/**
 * Select Query TreeWalker for partial response functionality
 */
class SelectWalker extends TreeWalkerAdapter
{
    /**
     * Walks down a SelectStatement AST node, modifying it to
     * select fields like requested by url
     *
     * @param SelectStatement $AST
     * @return void
     */
    public function walkSelectStatement(SelectStatement $AST)
    {
        $query = $this->_getQuery();
        $fields = $query->getHint('newscoop.api.fields');
        $selectItems = array();

        $parent = null;
        $alias = null;
        foreach ($this->_getQueryComponents() AS $dqlAlias => $qComp) {
            if ($qComp['parent'] === null && $qComp['nestingLevel'] == 0) {
                $parent = $qComp;
                $alias = $dqlAlias;
                break;
            }
        }

        $components = $this->_getQueryComponents();
        foreach ($fields as $field) {
            $entityData = $this->findAliasForField($field);
            $orginalField = $field;
            $realField = \Newscoop\Gimme\PropertyMatcher::match($entityData[1], $field);

            if ($field != $realField) {
                $entityData = $this->findAliasForField($realField);
                $field = $realField;
            }

            if ($alias !== false) {
                if (!array_key_exists($alias, $components)) {
                    throw new \UnexpectedValueException("There is no component aliased by [{$alias}] in the given Query");
                }
                $meta = $components[$alias];

                if ($meta['metadata']->hasAssociation($orginalField)) {
                    throw new \UnexpectedValueException("Associations (field {$orginalField}) in partial response are not yet implemented");
                }

                if (!$meta['metadata']->hasField($field)) {
                    throw new \UnexpectedValueException("There is no such field [{$field}] in the given Query component, aliased by [$alias]");
                }
            } else {
                if (!array_key_exists($field, $components)) {
                    throw new \UnexpectedValueException("There is no component field [{$field}] in the given Query");
                }
            }

            if ($alias !== false) {
                $pathExpression = new PathExpression(PathExpression::TYPE_STATE_FIELD | PathExpression::TYPE_SINGLE_VALUED_ASSOCIATION, $alias, $field);
                $pathExpression->type = PathExpression::TYPE_STATE_FIELD;
            } else {
                $pathExpression = $field;
            }

            $simpleSelectExpression = new SelectExpression($pathExpression, $orginalField);
            $selectItems[] = $simpleSelectExpression;
        }

        $AST->selectClause->selectExpressions = $selectItems;
    }

    /**
     * Find alias for choosen field
     * @param  string $field Field name
     * @return string        array with alias or field name and class namespace
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