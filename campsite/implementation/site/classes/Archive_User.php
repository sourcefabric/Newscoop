<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/XR_CcClient.php');

/**
 * @package Campsite
 */
class Archive_User
{
    const DEFAULT_GROUP = 'Admins';

    /**
     * @return mixed
     */
    public static function Init()
    {
        global $mdefs;
        $xrc = XR_CcClient::Factory($mdefs, true);
        $sessid = camp_session_get(CS_FILEARCHIVE_SESSION_VAR_NAME, '');
        return array('xrc' => $xrc, 'sessid' => $sessid);
    } // fn Init


    /**
     * @param string p_login
     * @param string p_passwd
     * @param string p_name
     *
     * @return mixed
     *      int uid - The user identifier, on success
     *      PEAR Error on failure
     */
    public static function Create($p_login, $p_passwd, $p_name)
    {
        $init = self::Init();
        if (PEAR::isError($init['xrc'])) {
            return $init['xrc'];
        }

        $xrc = $init['xrc'];
        $sessid = $init['sessid'];
        $result = $xrc->xr_createUser($sessid, $p_login, $p_passwd, $p_name);
        if (PEAR::isError($result)) {
            return $result;
        }
        $xrc->xr_addToGroup($sessid, $p_login, self::DEFAULT_GROUP);

        return $result['uid'];
    } // fn Create


    /**
     * @param string p_login
     *
     * @return boolean
     */
    public static function Delete($p_login)
    {
        $init = self::Init();
        if (PEAR::isError($init['xrc'])) {
            return $init['xrc'];
        }

        $xrc = $init['xrc'];
        $sessid = $init['sessid'];
        return $xrc->xr_deleteUser($sessid, $p_login);
    } // fn Delete


    /**
     * @param string p_login
     * @param string p_old
     * @param string p_new
     *
     * @return boolean
     */
    public static function ChangePassword($p_login, $p_old, $p_new)
    {
        $init = self::Init();
        if (PEAR::isError($init['xrc'])) {
            return $init['xrc'];
        }

        $xrc = $init['xrc'];
        $sessid = $init['sessid'];
        return $xrc->xr_changePassword($sessid, $p_login, $p_old, $p_new);
    } // fn ChangePassword

} // class Archive_User

?>