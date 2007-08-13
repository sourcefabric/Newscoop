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
class SQLSelectClause { // extends SQLQuery {
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
    private $m_columns = array();

    /**
     * The tables the query will request from.
     *
     * @var array
     */
    private $m_joins = array();

    /**
     * The query conditions.
     *
     * @var array
     */
    private $m_where = array();

    /**
     * The columns list and directions to order by.
     *
     * @var array
     */
    private $m_orderBy = array();

    /**
     * The record number to start selecting.
     *
     * @var integer
     */
    private $m_limitStart = 0;

    /**
     * The offset.
     *
     * @var integer
     */
    private $m_limitOffset = 0;


    /**
     *
     */
    public function __construct()
    {

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
        return (implode(', ', $this->m_columns));
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
            foreach ($this->m_joins as $table) {
                // TODO
            }
        }

        return $from;
    } // fn buildFrom


    /**
     * @return string
     */
    private function buildWhere()
    {
        // TODO support for multiple tables
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