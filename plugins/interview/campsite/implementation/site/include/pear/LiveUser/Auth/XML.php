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
 * @version CVS: $Id: XML.php,v 1.44 2006/08/22 17:11:43 lsmith Exp $
 * @link http://pear.php.net/LiveUser
 */

/**
 * Require parent class definition and XML::Tree class.
 */
require_once 'LiveUser/Auth/Common.php';
require_once 'XML/Tree.php';

/**
 * XML driver for authentication
 *
 * This is a XML backend driver for the LiveUser class.
 *
 * @category authentication
 * @package LiveUser
 * @author  Björn Kraus <krausbn@php.net>
 * @copyright 2002-2006 Markus Wolff
 * @license http://www.gnu.org/licenses/lgpl.txt
 * @version Release: @package_version@
 * @link http://pear.php.net/LiveUser
 */
class LiveUser_Auth_XML extends LiveUser_Auth_Common
{
    /**
     * XML file in which the auth data is stored.
     *
     * @var    string
     * @access private
     */
    var $file = '';

    /**
     * XML::Tree object.
     *
     * @var    XML_Tree
     * @access private
     */
    var $tree = false;

    /**
     * XML::Tree object of the user logged in.
     *
     * @var    XML_Tree
     * @access private
     * @see    readUserData()
     */
    var $userObj = null;

    /**
     * Load the storage container
     *
     * @param   array  array containing the configuration.
     * @param string  name of the container that should be used
     * @return bool true on success or false on failure
     *
     * @access public
     */
    function init(&$conf, $containerName)
    {
        parent::init($conf, $containerName);

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
     * Writes current values for user back to the database.
     *
     * @return bool true on success or false on failure
     *
     * @access private
     */
    function _updateUserData()
    {
        if (!array_key_exists('lastlogin', $this->tables['users']['fields'])) {
            return true;
        }

        $index = 0;
        foreach ($this->userObj->children as $value) {
            if ($value->name == $this->alias['lastlogin']) {
                $el =& $this->userObj->getElement(array($index));
                $el->setContent($this->currentLogin);
            }
            $index++;
        }

        $success = false;
        do {
          if (!is_writable($this->file)) {
              $errorMsg = 'Auth freeze failure. Cannot write to the xml file';
              break;
          }
          $fp = fopen($this->file, 'wb');
          if (!$fp) {
              $errorMsg = "Auth freeze failure. Failed to open the xml file.";
              break;
          }
          if (!flock($fp, LOCK_EX)) {
              $errorMsg = "Auth freeze failure. Couldn't get an exclusive lock on the file.";
              break;
          }
          if (!fwrite($fp, $this->tree->get())) {
              $errorMsg = "Auth freeze failure. Write error when writing back the file.";
              break;
          }
          @fflush($fp);
          $success = true;
        } while (false);

        @flock($fp, LOCK_UN);
        @fclose($fp);

        if (!$success) {
            $this->stack->push(LIVEUSER_ERROR, 'exception',
                array(), 'Cannot read XML Auth file: '.$errorMsg);
        }

        return $success;
    }

    /**
     * Reads user data from the given data source
     * If only $handle is given, it will read the data
     * from the first user with that handle and return
     * true on success.
     * If $handle and $passwd are given, it will try to
     * find the first user with both handle and password
     * matching and return true on success (this allows
     * multiple users having the same handle but different
     * passwords - yep, some people want this).
     * if only an auth_user_id is passed it will try to read the data based on the id
     * If no match is found, false is being returned.
     *
     * @param  string user handle
     * @param  string user password
     * @param  bool|int if the user data should be read using the auth user id
     * @return bool true on success or false on failure
     *
     * @access public
     */
    function readUserData($handle = '', $passwd = '', $auth_user_id = false)
    {
        $success = false;
        $index = 0;

        foreach ($this->tree->root->children as $user) {
            $result = array();
            $names = array_flip($this->alias);
            foreach ($user->children as $value) {
                if (array_key_exists($value->name, $names)) {
                    $result[$names[$value->name]] = $value->content;
                }
            }

            if ($auth_user_id) {
                if (array_key_exists('auth_user_id', $result)
                    && $auth_user_id === $result['auth_user_id']
                ) {
                    $success = true;
                    break;
                }
            } elseif (array_key_exists('handle', $result) && $handle === $result['handle']) {
                if (!is_null($this->tables['users']['fields']['passwd'])) {
                    if (array_key_exists('passwd', $result)
                        && $this->encryptPW($passwd) === $result['passwd']
                    ) {
                        $success = true;
                        break;
                    } elseif (is_string($this->tables['users']['fields']['handle'])) {
                        // dont look for any further matching handles
                        break;
                    }
                } else {
                    $success = true;
                    break;
                }
            }

            $index++;
        }

        if (!$success) {
            return null;
        }

        $this->propertyValues = $result;

        $this->userObj      =& $this->tree->root->getElement(array($index));

        return true;
    }

    /**
     * Properly disconnect from resources
     *
     * @return bool true on success or false on failure
     *
     * @access public
     */
    function disconnect()
    {
        $this->tree = false;
        $this->userObj = null;
        return true;
    }
}
?>
