<?PHP

/**
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
 * $root =& DynMenuItem::Create('', '');
 *
 * // Create a "home" menu item.
 * $home =& DynMenuItem::Create('Home', 'URL', 
 *                           array('id' => 'home', 'icon' => "ICON"));
 * $root->addItem($home);
 *
 * $root->addSplit();
 *
 * $content =& DynMenuItem::Create('Content', 'URL', 
 *                              array('id' => 'content', 'icon' => 'ICON'));
 * $root->addItem($content);             
 * $articles =& DynMenuItem::Create('Articles', 'URL', 
 *                               array('id' => 'articles', 'icon' => 'ICON'));
 *
 * $content->addItem($articles);
 * $content->addSplit();
 * $images =& DynMenuItem::Create('Images', 'URL',
 *                             array('id' => 'articles', 'icon' => 'ICON'));
 * $content->addItem($images);
 *
 * ... now call plugins to allow them to add their menu items ...
 * $modules =& DynMenuItem::Create('Modules', 'url', array('id' => 'modules'));
 * $root->addItemAfter($modules, 'content');
 *
 * ...
 * // Generate the menu
 * echo $root->createMenu('myMenu');
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
    function SetMenuType($p_type = null)
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
    function DynMenuItem($p_title, $p_url, $p_attrs = null) 
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
     * @param string $p_url
     * @param array $p_attrs
     * @return DynMenuItem
     */
    function &Create($p_title, $p_url, $p_attrs = null) 
    {
        $className = DynMenuItem::SetMenuType();
        if (class_exists($className)) {
            $obj =& new $className($p_title, $p_url, $p_attrs);
            return $obj;
        }
        return null;
    } // fn Create
    
    
    /**
     * Add a menu item as a child to this item.  
     * @param DynMenuItem $p_item
     * @return none
     */
    function addItem(&$p_item) 
    {
        $p_item->m_parent =& $this;
        if (isset($p_item->m_attrs['id'])) {
            $this->m_subItems[$p_item->m_attrs['id']] =& $p_item;
        }
        else {
            $this->m_subItems[] =& $p_item;
        }
    } // fn addItem
    

    /**
     * Get the child menu item matching the given ID.
     * @param string $p_id
     * @return DynMenuItem
     */
    function &getChildById($p_id)
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
    function &getMatchingItem($p_id) 
    {
        if (count($this->m_subItems) <= 0) {
            return null;
        }
        $match =& $this->getChildById($p_id);
        if (!is_null($match)) {
            return $match;
        }
        foreach ($this->m_subItems as $subItem) {
            $match =& $subItem->getMatchingItem($p_id);
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
    function addItemAfter($p_item, $p_id)
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
    function &addSplit($p_attrs = null)
    {
        $className = DynMenuItem::SetMenuType();
        if (!class_exists($className)) {
            return;
        }
        $newItem =& new $className('[[split]]', '', $p_attrs);
        if (isset($newItem->m_attrs['id'])) {
            $this->m_subItems[$newItem->m_attrs['id']] =& $newItem;
        }
        else {
            $this->m_subItems[] =& $newItem;
        }
        return $newItem;
    } // fn addSplit

    
    /**
     * Create the menu, return it as a string.
     * @return string
     */
    function createMenu($p_name = null, $p_extraArgs = null) {  } 
        
} // class DynMenuItem


class DynMenuItem_JsCook extends DynMenuItem {
    
    /**
     * Create the javascript for the menu.
     * @param string $p_name
     * @return string
     */
    function createMenu($p_name, $p_extraArgs = null) 
    {
    	$str = "<SCRIPT LANGUAGE=\"JavaScript\"><!--\n";
        $str .= "var $p_name =\n";
        $str .= "[\n";
        $str .= $this->__recurseBuild(1);
        $str .= "];\n";
        $str .= "--></SCRIPT>";
        return $str;
    } // fn createMenu
    

    function __recurseBuild($p_level) 
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
                $str .= "['" . $attrs['icon'] . "', '" . $subItem->m_title . "', '"
                             . $subItem->m_url . "', '" . $attrs['target'] . "', '"
                             . $attrs['description']. "'";
                if (count($subItem->m_subItems) > 0) {
                    $str .= ",\n". $subItem->__recurseBuild($p_level+1);
                    $str .= str_repeat("\t", $p_level)."],\n";
                }
                else {
                    $str .= "],\n";
                }
            }
            else {
                $str .= str_repeat("\t", $p_level)."_cmSplit,\n";
            }
        }
        return $str;
    } // fn __recurseBuild
        
} // class DynMenuItem_JsCook



//class User {
//    function hasPermission($str) {
//        return ($str == "foo");
//    }
//}
//
//$user =& new User();
//DynMenuItem::SetMenuType('DynMenuItem_JsCook_Rights');
//DynMenuItem_JsCook_Rights::SetUser($user);
//
//$root =& DynMenuItem::Create('root', '');
//$home =& DynMenuItem::Create('Home', 'URL', array('id' => 'home', 'icon' => "ICON"));
//$root->addItem($home);
//$root->addSplit();
//$content =& DynMenuItem::Create('Content', 'URL', array('id' => 'content', 'icon' => 'ICON', 'rights' => 'foo'));
//$root->addItem($content);             
//$pub =& DynMenuItem::Create('Publications', 'URL', array('id' => 'pub', 'icon' => 'ICON'));
//
//$content->addItem($pub);
//$content->addSplit();
////$content->addItem('ImageManager', 'Image Manager', 'URL','ICON');
//
//echo "<pre>";
//echo htmlspecialchars($root->createMenu('myMenu'));
//echo "</pre>";
//$modules =& DynMenuItem::Create('Modules', 'url', array('id' => 'modules'));
//$root->addItemAfter($modules, 'home');
//
//echo "<pre>";
//echo htmlspecialchars($root->createMenu('myMenu'));
//echo "</pre>";
?>