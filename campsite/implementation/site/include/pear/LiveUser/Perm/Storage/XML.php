<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * A framework for authentication and authorization in PHP applications
 *
 * LiveUser is an authentication/permission framework designed
 * to be flexible and easily extendable.
 *
 * Since it is impossible to have a
 * "one size fits all" it takes a container
 * approach which should enable it to
 * be versatile enough to meet most needs.
 *
 * PHP version 4 and 5
 *
 * LICENSE: This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston,
 * MA  02111-1307  USA
 *
 *
 * @category authentication
 * @package LiveUser
 * @author  Markus Wolff <wolff@21st.de>
 * @author  Helgi Þormar Þorbjörnsson <dufuz@php.net>
 * @author  Lukas Smith <smith@pooteeweet.org>
 * @author  Arnaud Limbourg <arnaud@php.net>
 * @author  Pierre-Alain Joye <pajoye@php.net>
 * @author  Bjoern Kraus <krausbn@php.net>
 * @copyright 2002-2006 Markus Wolff
 * @license http://www.gnu.org/licenses/lgpl.txt
 * @version CVS: $Id: XML.php,v 1.26 2006/03/14 13:10:04 lsmith Exp $
 * @link http://pear.php.net/LiveUser
 */

/**
 * Require parent class definition.
 */
require_once 'LiveUser/Perm/Storage.php';
require_once 'XML/Tree.php';

/**
 * XML container for permission handling
 *
 * This is a XML backend driver for the LiveUser class.
 *
 * Requirements:
 * - File "Liveuser.php" (contains the parent class "LiveUser")
 * - XML_Parser
 *
 * @category authentication
 * @package LiveUser
 * @author  Lukas Smith <smith@pooteeweet.org>
 * @author  Bjoern Kraus <krausbn@php.net>
 * @copyright 2002-2006 Markus Wolff
 * @license http://www.gnu.org/licenses/lgpl.txt
 * @version Release: @package_version@
 * @link http://pear.php.net/LiveUser
 */
class LiveUser_Perm_Storage_XML extends LiveUser_Perm_Storage
{
    /**
     * XML file in which the auth data is stored.
     *
     * @var string
     * @access private
     */
    var $file = '';

    /**
     * XML::Tree object.
     *
     * @var    XML_Tree
     * @access private
     */
    var $tree = null;

    /**
     * XML::Tree object of the user logged in.
     *
     * @var    XML_Tree
     *
     * @access private
     * @see    readUserData()
     */
    var $userObj = null;

    /**
     * Initialize the storage container
     *
     * @param array Array with the storage configuration
     * @return bool true on success, false on failure.
     *
     * @access public
     */
    function init(&$storageConf)
    {
        parent::init($storageConf);

        if (!is_file($this->file)) {
            if (!is_file(getenv('DOCUMENT_ROOT') . $this->file)) {
                $this->stack->push(LIVEUSER_ERROR_MISSING_DEPS, 'exception', array(),
                    "Perm initialisation failed. Can't find xml file.");
                return false;
            }
            $this->file = getenv('DOCUMENT_ROOT') . $this->file;
        }

        $tree =& new XML_Tree($this->file);
        $err =& $tree->getTreeFromFile();
        if (PEAR::isError($err)) {
            $this->stack->push(LIVEUSER_ERROR, 'exception', array(),
                "Perm initialisation failed. Can't get tree from file");
            return false;
        }
        $this->tree =& $tree;

        if (!is_a($this->tree, 'xml_tree')) {
            $this->stack->push(LIVEUSER_ERROR_INIT_ERROR, 'error',
                array('container' => 'storage layer configuration missing'));
            return false;
        }

        return true;
    }

    /**
     * map an auth user to a perm user
     *
     * @param int $auth_user_id
     * @param string $containerName
     * @return array requested data or false on failure
     *
     * @access public
     */
    function mapUser($auth_user_id, $containerName)
    {
        $nodeIndex = 0;
        $userIndex = 0;

        if (isset($this->tree->root->children) && is_array($this->tree->root->children)) {
            foreach ($this->tree->root->children as $node) {
                if ($node->name == 'users') {
                    foreach ($node->children as $user) {
                        if ($user->name == 'user'
                            && $auth_user_id == $user->attributes['authUserId']
                            && $containerName == $user->attributes['authContainerName']
                        ) {
                            $result['perm_user_id'] = $user->attributes['userId'];
                            $result['perm_type'] = $user->attributes['type'];
                            $this->userObj =& $this->tree->root->getElement(array($nodeIndex, $userIndex));
                            return $result;
                        }
                        $userIndex++;
                    }
                }
                $nodeIndex++;
            }
        }

        return false;
    }

    /**
     * Reads all rights of current user into a
     * two-dimensional associative array, having the
     * area names as the key of the 1st dimension.
     * Group rights and invididual rights are being merged
     * in the process.
     *
     * @param int perm user id
     * @return array requested data or false on failure
     *
     * @access public
     */
    function readUserRights($perm_user_id)
    {
        $result = array();

        foreach ($this->userObj->children as $node) {
            if ($node->name == 'rights') {
                $tmp = explode(',', $node->content);
                foreach ($tmp as $value) {
                    $level = LIVEUSER_MAX_LEVEL;
                    // level syntax: 10(2) => right id 10 at level 2
                    $match = array();
                    if (preg_match('/(\d+)\((\d+)\)/', $value, $match)) {
                        $value = $match[1];
                        $level = $match[2];
                    }
                    $result[$value] = $level;
                }
            }
        }

        return $result;
    }

    /**
     * properly disconnect from resources
     *
     * @return bool true on success and false on failure
     *
     * @access public
     */
    function disconnect()
    {
        $this->tree = null;
        $this->userObj = null;
        return true;
    }
}
?>