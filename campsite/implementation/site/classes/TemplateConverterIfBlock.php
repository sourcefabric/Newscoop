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
 * Class TemplateConverterIfBlock
 */
class TemplateConverterIfBlock
{
    /**
     * @var array
     */
    private $m_ifBlocks = array('nextitems',
                                'previousitems',
                                'nextsubtitles',
                                'prevsubtitles');


    /**
     * @var string
     */
    private $m_ifBlock = '';

    /**
     * @var string
     */
    private $m_ifBlockStr = '';

    /**
     * @var string
     */
    private $m_endIfBlock = '';

    /**
     * @var array
     */
    private static $m_ifBlockStack = array();


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
        if (isset($p_optArray[1]) && strtolower($p_optArray[1]) == 'not') {
            $condType = true;
            $idx = 2;
        } else {
            $condType = false;
            $idx = 1;
        }

        if (!isset($p_optArray[$idx])
                || !in_array(strtolower($p_optArray[$idx]), $this->m_ifBlocks)) {
            return;
        }

        $this->m_ifBlockStr = 'if ';
        switch(strtolower($p_optArray[$idx])) {
        case 'nextitems':
            $this->m_ifBlock = strtolower($p_optArray[$idx]);
            $this->m_ifBlockStr .= CS_OBJECT.'->current_list->has_next_elements';
            break;
        case 'previousitems':
            $this->m_ifBlock = strtolower($p_optArray[$idx]);
            $this->m_ifBlockStr .= CS_OBJECT.'->current_list->has_previous_elements';
            break;
        }

        if (!empty($this->m_ifBlockStr)) {
            $this->m_endIfBlock = '/if';
        }
    } // fn parse


    /**
     *
     */
    public function getEndIfBlock()
    {
        return $this->m_endIfBlock;
    } // fn getEndIfBlock


    /**
     *
     */
    public function getIfBlock()
    {
        return $this->m_ifBlock;
    } // fn getIfBlockString


    /**
     *
     */
    public function getIfBlockString()
    {
        return $this->m_ifBlockStr;
    } // fn getIfBlockString


    /**
     *
     */
    public function GetNewTagContent($p_optArray)
    {
        $newTag = '';
        $maxIndex = sizeof(self::$m_ifBlockStack) ? sizeof(self::$m_ifBlockStack) - 1 : 0;
        if ($p_optArray[0] == 'if') {
            self::$m_ifBlockStack[] = new TemplateConverterIfBlock($p_optArray);
            $maxIndex = sizeof(self::$m_ifBlockStack) ? sizeof(self::$m_ifBlockStack) - 1 : 0;
            $newTag = self::$m_ifBlockStack[$maxIndex]->getIfBlockString();
        } elseif (strpos($p_optArray[0], 'endif') !== false) {
            if (isset(self::$m_ifBlockStack[$maxIndex])
                    && is_object(self::$m_ifBlockStack[$maxIndex])) {
                $newTag = self::$m_ifBlockStack[$maxIndex]->getEndIfBlock();
                array_pop(self::$m_ifBlockStack);
            }
        }

        return $newTag;
    } // fn GetNewTagContent


    public static function GetIfBlockStack()
    {
        return self::$m_ifBlockStack;
    } // fn GetIfBlockStack

} // class TemplateConverterIfBlock

?>