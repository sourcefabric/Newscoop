<?php


/**
 * @package Campsite
 */
class Saas
{
    // Hold an instance of the class
    private static $instance;

    private static $saasConfig = array();

    // A private constructor; prevents direct creation of object
    private function __construct()
    {
        if ( file_exists( $GLOBALS['g_campsiteDir'] . '/conf/saas_config.php' )) {
            require_once($GLOBALS['g_campsiteDir'] . '/conf/saas_config.php');
        } else {
        	$this->saasConfig = array();
        }

    }

    // The singleton method
    public static function singleton()
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    }

    /**
     * Return true if the user has the permission specified.
     *
     * @param string $p_permissionString
     *
     * @return boolean
     */
    public function hasPermission($p_permissionString)
    {
        if (array_key_exists('permissions', $this->saasConfig)) {
            $permissions = $this->saasConfig['permissions'];
        } else {
            $permissions = array();
        }

        if (!in_array($p_permissionString, $permissions)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Return TRUE if the resource->action is granted by Saas
     *
     * @param string $p_resourceName
     *
     * @param string $p_privilegeName
     *
     * @return boolean
     */
    public function hasPrivilege($p_resourceName = null, $p_privilegeName)
    {
        //for the plugins the action is something like manage.php and we only need the 'manage' part
        if (count($p_privilegeNameArray = explode('.', $p_privilegeName))) {
            $p_privilegeName = $p_privilegeNameArray[0];
        }

        $hasPrivilege = TRUE;
        if (array_key_exists('privileges', $this->saasConfig)) {
            $privileges = $this->saasConfig['privileges'];
        } else {
            $privileges = array();
        }

        foreach ($privileges as $privilege) {
            if ($privilege['resource'] == $p_resourceName) {
                if ( ($privilege['privilege'] == $p_privilegeName) || ($privilege['privilege'] == '*') ) {
                    $hasPrivilege = FALSE;
                    continue;
                }
            }
        }
        return $hasPrivilege;
    }

    /**
     * Return array of allowed resource -> action combinations
     *
     * @param string $p_resourceName
     *
     * @param string $p_privilegeName
     *
     * @return array
     */
    public function filterPrivileges($p_resourceName = null, $p_privilegeName = null)
    {
        $returnArray = array();
        if (array_key_exists('privileges', $this->saasConfig)) {
            $privileges = $this->saasConfig['privileges'];
        } else {
            $privileges = array();
        }

        if (is_array($p_privilegeName)) {
            foreach ($p_privilegeName as $p_name) {
                $found = FALSE;
                foreach ($privileges as $privilege) {
                    if ($privilege['resource'] == $p_resourceName) {
                        if ( ($privilege['privilege'] == $p_name) || ($privilege['privilege'] == '*') ) {
                            $found = TRUE;
                            continue;
                        }
                    }
                }
                if (!$found) {
                    $returnArray[] = $p_name;
                }
            }
        }
        return $returnArray;
    }

    // Prevent users to clone the instance
    public function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

}
