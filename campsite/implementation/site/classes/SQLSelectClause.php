<?php
/**
 * @package Campsite
 */

define ('SQL', 'SELECT %s FROM %s');
define ('SQL_WHERE', ' WHERE %s');
define ('SQL_ORDER_BY', ' ORDER BY %s');
define ('SQL_LIMIT', ' LIMIT %d, %d');

/**
 * @package Campsite
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
     *
     */
    public function __construct()
    {
        $this->m_columns = array();
        $this->m_joins = array();
        $this->m_where = array();
        $this->m_orderBy = array();
        $this->m_limitStart = 0;
        $this->m_limitOffset = 0;
    } // fn __construct


    /**
     *
     */
    public function addColumn($p_column)
    {
        $this->m_columns[] = $p_column;
    } // fn addColumn


    /**
     *
     */
    public function addJoin($p_join)
    {
        $this->m_joins[] = $p_join;
    } // fn addJoin


    /**
     *
     */
    public function addWhere($p_condition)
    {
        $this->m_where[] = $p_condition;
    } // fn addWhere


    /**
     *
     */
    public function addOrderBy($p_order)
    {
        $this->m_orderBy[] = $p_order;
    } // fn addOrderBy


    /**
     *
     */
    public function setTable($p_table)
    {
        $this->m_table = $p_table;
    } // fn setTable


    /**
     *
     */
    public function setLimit($p_start = 0, $p_offset = 0)
    {
        $this->m_limitStart = $p_start;
        $this->m_limitOffset = $p_offset;
    } // fn setLimit


    /**
     * @return string
     */
    public function buildQuery()
    {
        $columns = $this->buildColumns();
        $from = $this->buildFrom();
        $sql = sprintf(SQL, $columns, $from);

        $where = $this->buildWhere();
        $sql .= sprintf(SQL_WHERE, $where);

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
     * @return string
     */
    private function buildColumns()
    {
        $columns = '';

        if ($this->hasJoins()) {
            if (sizeof($this->m_columns) == 0) {
                $columns = $this->m_table.'*';
            }
            //foreach ($this->m_joins as $join) {
            //$columns .= ', '.$join->getColumns();
            //}
        } else {
            if (sizeof($this->m_columns) == 0) {
                $columns = '*';
            }
        }

        if (empty($columns)) {
            $columns = implode(', ', $this->m_columns);
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

        if ($this->hasJoins()) {
            foreach ($this->m_joins as $join) {
                $from .= ' '.$join;

                // TODO: SQLJoinClause class
                //
                //$from .= ' LEFT JOIN '.$join->getTable();
                //$from .= ' ON '.$this->m_table.'.'.$join->getLeft()
                //.' = '.$join->getTable().'.'.$join->getRight();
            }
        }

        return $from;
    } // fn buildFrom


    /**
     * @return string
     */
    private function buildWhere()
    {
        return implode(' AND ', $this->m_where);
    } // fn buildWhere


    /**
     * @return string
     */
    private function buildOrderBy()
    {
        return implode (', ', $this->m_orderBy);
    } // fn buildOrderBy

} // class SQLSelectClause

?>