<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @author Sebastian Goebel <devel@yellowsunshine.de>
 * @copyright 2008 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */

define ('SQL', "SELECT %s\nFROM %s");
define ('SQL_WHERE', "\nWHERE %s");
define ('SQL_GROUP_BY', "\nGROUP BY %s");
define ('SQL_ORDER_BY', "\nORDER BY %s");
define ('SQL_LIMIT', "\nLIMIT %d, %d");
define ('SQL_DISTINCT', 'DISTINCT(%s)');

/**
 * Class SQLSelectClause
 */
class SQLSelectClause {
    /**
     * The name of the base table.
     *
     * @var string
     */
    private $m_table = null;

    /**
     * The columns to be retrieved.
     *
     * @var array
     */
    private $m_columns = null;

    /**
     *
     * @var array
     */
    private $m_from = null;

    /**
     * The tables the query will request from.
     *
     * @var array
     */
    private $m_joins = null;

    /**
     * The query conditions.
     *
     * @var array
     */
    private $m_where = null;
    
    /**
     * Conditional where clauses (separated by 'OR' operator)
     *
     * @var array
     */
    private $m_conditionalWhere = null;
    
    /**
     * Fields we want to group the result by.
     *
     * @var array
     */
    private $m_group = null;

    /**
     * The columns list and directions to order by.
     *
     * @var array
     */
    private $m_orderBy = null;

    /**
     * The record number to start selecting.
     *
     * @var integer
     */
    private $m_limitStart = null;

    /**
     * The offset.
     *
     * @var integer
     */
    private $m_limitOffset = null;

    /**
     * The DISTINCT mode.
     *
     * @var string
     */
    private $m_distinctMode = false;
    
    /**
     * The column which fetched DISTINCT.
     *
     * @var string
     */
    private $m_distinctColumn = null;
    
    
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->m_columns = array();
        $this->m_from = array();
        $this->m_joins = array();
        $this->m_where = array();
        $this->m_orderBy = array();
        $this->m_limitStart = 0;
        $this->m_limitOffset = 0;
    } // fn __construct


    /**
     * Adds a column to be fetched by the query.
     *
     * @param string $p_column
     *      The name of the column
     *
     * @return void
     */
    public function addColumn($p_column)
    {
        $this->m_columns[] = $p_column;
    } // fn addColumn


    /**
     * Adds a table to the FROM part of the query.
     *
     * @param string $p_table
     *      The name of the table
     *
     * @return void
     */
    public function addTableFrom($p_table)
    {
        $this->m_from[] = $p_table;
    }


    /**
     * Adds a table join to the query.
     *
     * @param string $p_join
     *      The full join string including the ON condition
     *
     * @return void
     */
    public function addJoin($p_join)
    {
        $this->m_joins[] = $p_join;
    } // fn addJoin


    /**
     * Adds a WHERE condition to the query.
     *
     * @param string $p_condition
     *      The comparison operation
     *
     * @return void
     */
    public function addWhere($p_condition)
    {
        $this->m_where[] = $p_condition;
    } // fn addWhere


    /**
     * Adds a conditional WHERE condition to the query (using 'OR' operator)
     *
     * @param string $p_condition
     *      The comparison operation
     *
     * @return void
     */
    public function addConditionalWhere($p_condition)
    {
        $this->m_conditionalWhere[] = $p_condition;
    } // fn addConditionalWhere
    
    
    public function addGroupField($p_field)
    {
        $this->m_group[] = $p_field;
    }


    /**
     * Adds an ORDER BY condition to the query.
     *
     * @param string $p_order
     *      The column and the direction of the order condition
     *
     * @return void
     */
    public function addOrderBy($p_order)
    {
        $this->m_orderBy[] = $p_order;
    } // fn addOrderBy


    /**
     * Sets the name of the main table in the query.
     *
     * @param string $p_table
     *      The name of the table
     *
     * @return void
     */
    public function setTable($p_table)
    {
        $this->m_table = $p_table;
    } // fn setTable


    /**
     * Sets the LIMIT of the query.
     *
     * @param integer $p_start
     *      The number where the query will start to fetch data
     * @param integer $p_offset
     *      The number of rows to be fetched
     *
     * @return void
     */
    public function setLimit($p_start = 0, $p_offset = 0)
    {
        $this->m_limitStart = $p_start;
        $this->m_limitOffset = $p_offset;
    } // fn setLimit


    /**
     * Sets all or specific column(s) to be fetched DISTINCT.
     *
     * @param string $p_column
     *      The column which have to fetched distinct
     *
     * @return void
     */
    public function setDistinct($p_column = null)
    {
        $this->m_distinctMode = true;
        $this->m_distinctColumn = $p_column;
    } // fn setDistinct
    
    
    /**
     * Builds the SQL query from the object attributes.
     *
     * @return string $sql
     *      The full SQL query
     */
    public function buildQuery()
    {
        $sql = '';
        $columns = $this->buildColumns();
        $from = $this->buildFrom();
        $sql = sprintf(SQL, $columns, $from);

        $where = $this->buildWhere();
        if (strlen($where)) {
            $sql .= sprintf(SQL_WHERE, $where);
        }
        
        $groupBy = $this->buildGroupBy();
        if (!empty($groupBy)) {
            $sql .= sprintf(SQL_GROUP_BY, $groupBy);
        }

        if (count($this->m_orderBy) > 0) {
            $orderBy = $this->buildOrderBy();
            $sql .= sprintf(SQL_ORDER_BY, $orderBy);
        }

        if (!empty($this->m_limitOffset)) {
            $sql .= sprintf(SQL_LIMIT, $this->m_limitStart, $this->m_limitOffset);
        }

        return $sql;
    } // fn buildQuery


    /**
     * Returns whether there is FROM tables other than the main query table.
     *
     * @return boolean
     *      true on success, false on failure
     */
    private function hasFrom()
    {
        return (count($this->m_from) > 0);
    } // fn hasFrom


    /**
     * Returns whether there is table joins.
     *
     * @return boolean
     *    true on success, false on failure
     */
    private function hasJoins()
    {
        return (count($this->m_joins) > 0);
    } // fn hasJoins


    /**
     * Builds the list of columns to be retrieved by the query.
     *
     * @return string $columns
     *      The list of columns
     */
    private function buildColumns()
    {
        $columns = '';

        if ($this->hasFrom() || $this->hasJoins()) {
            if (sizeof($this->m_columns) == 0) {
                $columns = $this->m_table.'*';
            }
        } else {
            if (sizeof($this->m_columns) == 0) {
                $columns = '*';
            }
        }
        
        if (!empty($columns) && $this->m_distinctMode) {
            $columns = sprintf(SQL_DISTINCT, $columns);   
        }

        if (empty($columns)) {
            foreach ($this->m_columns as $column) {
                if ($this->m_distinctMode === true && $this->m_distinctColumn === $column) {
                    $columns .= sprintf(SQL_DISTINCT, $column).', ';    
                } else { 
                    $columns .=  $column.', ';   
                } 
            }
            $columns = substr($columns, 0, -2);
        }

        return $columns;
    } // fn buildColumns


    /**
     * Builds the FORM part of the query based on the main table
     * and whether there is some table to join with.
     *
     * @return string
     *    $from The string containing the FORM part of the query
     */
    private function buildFrom()
    {
        $from = $this->m_table;

        if ($this->hasFrom()) {
            $from .= ',';
            $from .= implode (', ', $this->m_from);
        } elseif ($this->hasJoins()) {
            foreach ($this->m_joins as $join) {
                $from .= ' '.$join;
            }
        }

        return $from;
    } // fn buildFrom


    /**
     * Builds the list of WHERE conditions.
     *
     * @return string
     *      The string of conditions
     */
    private function buildWhere()
    {
        $conditionalWhere = null;
        if (is_array($this->m_conditionalWhere)) {
            $conditionalWhere = implode("\n        OR ", $this->m_conditionalWhere);
        }
        $where = null;
        if (is_array($this->m_where)) {
            $where = implode("\n    AND ", $this->m_where);
        }
        if (empty($conditionalWhere) && empty($where)) {
            return null;
        }
        if (empty($where)) {
            return $conditionalWhere;
        }
        if (!empty($conditionalWhere)) {
            $where .= "\n    AND (" . $conditionalWhere . ")";
        }
        return $where;
    } // fn buildWhere
    
    
    /**
     * Builds the GROUP BY clause.
     *
     * @return string
     */
    private function buildGroupBy()
    {
        if (!is_array($this->m_group) || count($this->m_group) == 0) {
            return null;
        }
        return implode(', ', $this->m_group);
    }


    /**
     * Builds the ORDER BY conditions.
     *
     * @return string
     *      The string of ORDER BY conditions
     */
    private function buildOrderBy()
    {
        return implode (",\n    ", $this->m_orderBy);
    } // fn buildOrderBy

} // class SQLSelectClause

?>