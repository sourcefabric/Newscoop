<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
namespace Newscoop\Datatable;
/**
 * Data table Interface
 */
interface ISource
{
    /**
     * Get the data needed by data table listing
     *
     * @param array $params
     * @param array $cols
     * @return
     */
    public function getData(array $p_params, array $p_cols);

    /**
     * Get the count of the data table listing used with params
     *
     * @param $params
     * @param $cols
     * @return integer
     */
    public function getCount(array $p_params, array $p_cols);

}