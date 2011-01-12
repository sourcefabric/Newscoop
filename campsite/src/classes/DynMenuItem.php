<?php
/**
 * Class to dynamically create a javascript menu.
 *
 * Copyright (C) 2005 MDLF/Campware
 * Copyright (C) 2010 Sourcefabric
 *
 * Authors: Paul Baranowski (paul@paulbaranowski.org)
 *          Holman Romero (holman.romero@sourcefabric.org)
 *
 */
class DynMenuItem {
    var $m_title = '';
    var $m_url = '';
    var $m_attrs = array();
    var $m_subItems = array();
    var $m_parent = null;

    /**
     * Set the type of menu you want to create.
     *
     * @param string $p_type -
     *      The name of the class.
     */
    public static function SetMenuType($p_type = null)
    {
        static $type = null;
        if (!is_null($p_type)) {
            $type = $p_type;
        }
        return $type;
    } // fn SetMenuType


    /**
     * Dont use this directly, use the Create method instead.
     * You will be able to swap out the type of menu then.
     *
     * @param string $p_title
     * @param string $p_url
     * @param array $p_attrs
     */
    public function DynMenuItem($p_title, $p_url, $p_attrs = null)
    {
        $this->m_title = $p_title;
        $this->m_url = $p_url;
        if (!is_null($p_attrs)) {
            $this->m_attrs = $p_attrs;
        }
    } // fn DynMenuItem


    /**
     * Create a menu item node of the type set with SetMenuType().
     *
     * @param string $p_title
     * 		Display string for the menu item.
     * @param string $p_url
     * 		Destination URL.
     * @param array $p_attrs
     * 		Options:
     * 		'icon' => HTML IMG tag
     * 		'id' => unique id for the item
     * 		'target' => new window ID for the destination URL
     * @return DynMenuItem
     */
    public static function &Create($p_title, $p_url, $p_attrs = null)
    {
        $className = DynMenuItem::SetMenuType();
        if (class_exists($className)) {
            $obj = new $className($p_title, $p_url, $p_attrs);
            return $obj;
        }
        return null;
    } // fn Create


    /**
     * Add a menu item as a child to this item.
     * @param DynMenuItem $p_item
     * @return none
     */
    public function addItem(&$p_item)
    {
        if (isset($p_item->m_attrs['id'])) {
            $this->m_subItems[$p_item->m_attrs['id']] = $p_item;
        } else {
            $this->m_subItems[] = $p_item;
        }
    } // fn addItem


    /**
     * Get the child menu item matching the given ID.
     * @param string $p_id
     * @return DynMenuItem
     */
    public function &getChildById($p_id)
    {
        if (isset($this->m_subItems[$p_id])) {
            return $this->m_subItems[$p_id];
        }
        return null;
    } // fn getItem


    /**
     * Do a breadth-first recursive search and get the first menu item matching
     * the given ID.
     *
     * @param string $p_id
     * @return DynMenuItem
     */
    public function &getMatchingItem($p_id)
    {
        if (count($this->m_subItems) <= 0) {
            return null;
        }
        $match = $this->getChildById($p_id);
        if (!is_null($match)) {
            return $match;
        }
        foreach ($this->m_subItems as $subItem) {
            $match = $subItem->getMatchingItem($p_id);
            if (!is_null($match)) {
                return $match;
            }
        }
        return null;
    } // fn getMatchingItem


    /**
     * Add the item after the child with the given ID.
     * @param string $p_id
     */
    public function addItemAfter($p_item, $p_id)
    {
        $newSubItems = array();
        reset($this->m_subItems);
        while (list($key, $value) = each($this->m_subItems)) {
            $newSubItems[$key] = $value;
            if ($key == $p_id) {
                $newSubItems[$p_item->m_attrs['id']] = $p_item;
            }
        }
        $this->m_subItems =& $newSubItems;
    } // fn addItemAfter


    /**
     * Add a separator in the menu.
     *
     */
    public function &addSplit($p_attrs = null)
    {
        $className = DynMenuItem::SetMenuType();
        if (!class_exists($className)) {
            return;
        }
        $newItem = new $className('[[split]]', '', $p_attrs);
        if (isset($newItem->m_attrs['id'])) {
            $this->m_subItems[$newItem->m_attrs['id']] = $newItem;
        } else {
            $this->m_subItems[] = $newItem;
        }
        return $newItem;
    } // fn addSplit


    /**
     * Create the menu, return it as a string.
     * @return string
     */
    public function createMenu($p_name = null, $p_extraArgs = null) {  }

} // class DynMenuItem


class DynMenuItem_JQueryFG extends DynMenuItem
{
    /**
     * Create the javascript for the menu.
     * @param string $p_name
     * @return string
     */
    public function createMenu($p_name, $p_extraArgs = null)
    {
        return $this->__recurseBuild(1);
    } // fn createMenu


    public function __recurseBuild($p_level)
    {
        $str = "<ul>\n";
        foreach ($this->m_subItems as $subItem) {
            if (count($subItem->m_subItems) > 0) {
                $str .= str_repeat("\t", $p_level);
                $str .= '<li><a href="' . $subItem->m_url . '">' . $subItem->m_title . '</a>';
                $str .= "\n". $subItem->__recurseBuild($p_level + 1, $p_newLevel);
                $str .= "</li>\n";
            } else {
                $str .= str_repeat("\t", $p_level);
                $str .= '<li><a href="' . $subItem->m_url . '">' . $subItem->m_title . '</a>';
                $str .= "</li>\n";
            }
        }
        $str .= "</ul>\n";
        return $str;
    } // fn __recurseBuild
}

?>