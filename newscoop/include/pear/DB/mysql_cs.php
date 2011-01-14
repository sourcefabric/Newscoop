<?php
require_once 'DB/mysql.php';

class DB_mysql_cs extends DB_mysql
{
    function connect($dsn, $persistent = false)
    {
        global $g_ado_db;

        $this->dsn = $dsn;
        if ($dsn['dbsyntax']) {
            $this->dbsyntax = $dsn['dbsyntax'];
        }

        $this->connection = $g_ado_db->_connectionID;
        $this->_db = $dsn['database'];

        return;
    }

    function disconnect()
    {
        $this->connection = null;
        return true;
    }
}