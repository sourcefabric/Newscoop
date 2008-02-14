<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2008 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */


/**
 * Class TemplateConvertorListObject
 */
class TemplateConvertorListObject
{
    /**
     * @var array
     */
    private $m_listTypes = array('article' => 'articles',
                                 'articleattachment' => 'article_attachments',
                                 'articlecomment' => 'article_comments',
                                 'articleimage' => 'article_images',
                                 'articletopic' => 'article_topics',
                                 'issue' => 'issues',
                                 'searchresult' => 'search_results',
                                 'section' => 'sections',
                                 'subtopic' => 'subtopics',
                                 'subtitle' => 'subtitles');

    /**
     * @var string
     */
    private $m_list = '';

    /**
     * @var string
     */
    private $m_length = '';

    /**
     * @var string
     */
    private $m_columns = '';

    /**
     * @var string
     */
    private $m_constraints = '';

    /**
     * @var string
     */
    private $m_order = '';

    /**
     * @var string
     */
    private $m_endList = '';


    /**
     * @param array $p_optArray
     */
    public function __construct($p_optArray)
    {
        if (!is_array($p_optArray) || sizeof($p_optArray) < 1) {
            return false;
        }

        $this->parse($p_optArray);
    } // fn __construct


    /**
     * @param array $p_optArray
     */
    private function parse($p_optArray)
    {
        $listTypeFound = false;
        $orderFound = false;
        $constraints = '';
        $order = '';
        for ($i = 1; $i < sizeof($p_optArray); $i++) {
            if (strtolower($p_optArray[$i]) == 'length') {
                $this->m_length = 'length="'.$p_optArray[++$i].'"';
                continue;
            }
            if (strtolower($p_optArray[$i]) == 'columns') {
                $this->m_columns = 'columns="'.$p_optArray[++$i].'"';
                continue;
            }
            if (!$listTypeFound
                    && array_key_exists(strtolower($p_optArray[$i]), $this->m_listTypes)) {
                $this->m_list = 'list_'.$this->m_listTypes[strtolower($p_optArray[$i])];
                $this->m_endList = '/list_'.$this->m_listTypes[strtolower($p_optArray[$i])];
                $listTypeFound = true;
                continue;
            }
            if (!$orderFound && strtolower($p_optArray[$i]) != 'order') {
                $constraints.= $p_optArray[$i].' ';
            } else {
                if (strtolower($p_optArray[$i]) != 'order') {
                    $order.= $p_optArray[$i].' ';
                }
                $orderFound = true;
            }
        }

        if (strlen($constraints) > 0) {
            $this->m_constraints = 'constraints="'.trim($constraints).'"';
        }
        if (strlen($order) > 0) {
            $this->m_order = 'order="'.trim($order).'"';
        }
    } // fn parse


    /**
     *
     */
    public function getEndList()
    {
        return $this->m_endList;
    } // fn getEndList


    /**
     *
     */
    public function getListString()
    {
        $list = $this->m_list;
        $list.= (strlen($this->m_length)) ? ' '.$this->m_length : '';
        $list.= (strlen($this->m_columns)) ? ' '.$this->m_columns : '';
        $list.= (strlen($this->m_constraints)) ? ' '.$this->m_constraints : '';
        $list.= (strlen($this->m_order)) ? ' '.$this->m_order : '';

        return $list;
    } // fn getListString

} // fn TemplateConvertorListObject

?>