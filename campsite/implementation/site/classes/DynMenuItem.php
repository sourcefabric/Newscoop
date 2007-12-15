<?PHP
/**
 * PHP class to dynamically create a javascript menu.
 * Funded by MDLF/Campware (http://www.campware.org)
 *
 * Copyright (C) 2005  Paul Baranowski (paul@paulbaranowski.org)
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * To see the full license, go here:
 * http://www.gnu.org/copyleft/gpl.html
 *
 *
 * Dynamic programmable web menu class.
 *
 * The design goals are:
 * 1) Make it easy to swap to a new menu type without having to
 *    reprogram anything.
 * 2) Dynamically insert items in the list.  This is to support
 *    application plugins that want to put themselves in the menu
 *    after it has been created.
 *
 * The first design goal is accomplished with a function that sets
 * which menu implementation you want to use:
 * DynMenuItem::SetMenuType('class_name');
 *
 * The second design goal is accomplished by setting an identifier
 * for each menu item.  Note that we cannot use the title or any other
 * existing peice of data for the identifier, because 1) any of
 * the data can be repeated, and even if that wasnt the case,
 * 2) multi-ligual interfaces make the data dynamic.
 *
 * However, use of IDs is optional.
 *
 * Example:
 * // This allows you to swap out different menu systems.
 * DynMenuItem::SetMenuType('DynMenuItem_JsCook');
 *
 * // You always must create a root node to contain all the others.
 * $root = DynMenuItem::Create('', '');
 *
 * // Create a "home" menu item.
 * $home = DynMenuItem::Create('Home', 'http://mysite.com/index.php',
 *                           array('id' => 'home',
 * 								   'icon' => "<img src='http://mysite.com/img/home.png' align='middle' style='padding-bottom: 3px;' width='16' height='16' />"));
 * $root->addItem($home);
 *
 * $root->addSplit();
 *
 * $content = DynMenuItem::Create('Content', 'http://mysite.com/content.php',
 *                              array('id' => 'content'));
 * $root->addItem($content);
 * $articles = DynMenuItem::Create('Articles', 'http://mysite.com/articles.php',
 *                               array('id' => 'articles'));
 *
 * $content->addItem($articles);
 *
 * // Generate the menu
 * echo $root->createMenu('myMenu');
 *
 * Note: the JSCook menu requires camp_javascriptspecialchars() which
 * escapes javascript strings.
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
        $p_item->m_parent =& $this;
        if (isset($p_item->m_attrs['id'])) {
            $this->m_subItems[$p_item->m_attrs['id']] =& $p_item;
        } else {
            $this->m_subItems[] =& $p_item;
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
            $this->m_subItems[$newItem->m_attrs['id']] =& $newItem;
        } else {
            $this->m_subItems[] =& $newItem;
        }
        return $newItem;
    } // fn addSplit


    /**
     * Create the menu, return it as a string.
     * @return string
     */
    public function createMenu($p_name = null, $p_extraArgs = null) {  }

} // class DynMenuItem


class DynMenuItem_JsCook extends DynMenuItem {

    /**
     * Create the javascript for the menu.
     * @param string $p_name
     * @return string
     */
    public function createMenu($p_name, $p_extraArgs = null)
    {
    	$str = "<SCRIPT LANGUAGE=\"JavaScript\"><!--\n";
        $str .= "var $p_name =\n";
        $str .= "[\n";
        $str .= $this->__recurseBuild(1);
        $str .= "];\n";
        $str .= "--></SCRIPT>";
        return $str;
    } // fn createMenu


    public function __recurseBuild($p_level)
    {
        $str = '';
        foreach ($this->m_subItems as $subItem) {
            $attrs =& $subItem->m_attrs;
            if (!isset($attrs['target'])) {
                $attrs['target'] = '';
            }
            if (!isset($attrs['description'])) {
                $attrs['description'] = '';
            }
            if (!isset($attrs['icon'])) {
                $attrs['icon'] = '';
            }
            if ($subItem->m_title != "[[split]]") {
                $str .= str_repeat("\t", $p_level);
                $str .= "['" . $attrs['icon'] . "', '" . camp_javascriptspecialchars($subItem->m_title) . "', '"
                             . $subItem->m_url . "', '" . $attrs['target'] . "', '"
                             . $attrs['description']. "'";
                if (count($subItem->m_subItems) > 0) {
                    $str .= ",\n". $subItem->__recurseBuild($p_level+1);
                    $str .= str_repeat("\t", $p_level)."],\n";
                } else {
                    $str .= "],\n";
                }
            } else {
                $str .= str_repeat("\t", $p_level)."_cmSplit,\n";
            }
        }
        return $str;
    } // fn __recurseBuild

} // class DynMenuItem_JsCook

?>