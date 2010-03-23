<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once('MMAUser.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/XR_CcClient.php');

/**
 * @package Campsite
 */
class MMAUser_ALib extends MMAUser
{
    /**
     * @var object
     */
    private $m_xrc = null;

    /**
     * @var string
     */
    private $m_userName = null;

    /**
     * @var integer
     */
    private $m_userId = null;


    /**
     * Class constructor
     *
     * @param string $p_userName
     */
    public function __construct($p_userName = null)
    {
        global $mdefs;

        $this->m_xrc = XR_CcClient::Factory($mdefs, true);
        if (!is_null($p_userName)) {
            $this->m_userName = $p_userName;
        }
    } // fn __construct
	

    /**
     * @param string $p_userName
     * @param string $p_password
     * @param string $p_realName
     * @param boolean $p_isAdmin
     *
     * @return mixed
     *      array The user identifier, on success
     *      PEAR Error on failure
     */
    public function create($p_userName, $p_password, $p_realName, $p_isAdmin = false)
    {
        if (!is_null($p_userName)) {
            $this->m_userName = $p_userName;
        }
        if (empty($this->m_userName)) {
            return false;
        }

        $sessid = camp_session_get(CS_FILEARCHIVE_SESSION_VAR_NAME, '');
        if (empty($sessid)) {
            return new PEAR_Error(getGS('Can not $1 the archive user', getGS('create'))
                .': '.getGS('the connection to $1 was not established.', 'File Archive'));
        }
        $result = $this->m_xrc->xr_createUser($sessid, $this->m_userName, $p_password, $p_realName);
        if (PEAR::isError($result)) {
            return $result;
        }
        if ($p_isAdmin) {
            $this->m_xrc->xr_addToGroup($sessid, $this->m_userName, 'Admins');
        }
        return $result;
    } // fn create


    /**
     * @return mixed
     *      boolean True on success, False on failure
     *      PEAR Error
     */
    public function delete()
    {
        if (empty($this->m_userName)) {
            return false;
        }

        $sessid = camp_session_get(CS_FILEARCHIVE_SESSION_VAR_NAME, '');
        if (empty($sessid)) {
            return new PEAR_Error(getGS('Can not $1 the archive user', getGS('delete'))
                .': '.getGS('the connection to $1 was not established.', 'File Archive'));
        }
        return $this->m_xrc->xr_deleteUser($sessid, $this->m_userName);
    } // fn delete


    /**
     * @param string $p_oldPassword
     * @param string $p_password
     *
     * @return mixed
     *      boolean True on success, False on failure
     *      PEAR Error
     */
    public function changePassword($p_oldPassword, $p_password)
    {
        if (empty($this->m_userName)) {
            return false;
        }

        $sessid = camp_session_get(CS_FILEARCHIVE_SESSION_VAR_NAME, '');
        if (empty($sessid)) {
            return new PEAR_Error(getGS('Can not $1 the archive user', getGS('change password of'))
                .': '.getGS('the connection to $1 was not established.', 'File Archive'));
        }
        return $this->m_xrc->xr_changePassword($sessid, $this->m_userName, $p_oldPassword, $p_password);
    } // fn changePassword


    /**
     * @param string $p_group
     *
     * @return mixed
     *      boolean True on success, False on failure
     *      PEAR Error
     */
    public function addToGroup($p_group)
    {
        if (empty($this->m_userName) || empty($p_group)) {
            return false;
        }

        $sessid = camp_session_get(CS_FILEARCHIVE_SESSION_VAR_NAME, '');
        if (empty($sessid)) {
            return new PEAR_Error(getGS('Can not add the archive user to the group $1', $p_group)
                .': '.getGS('the connection to $1 was not established.', 'File Archive'));
        }
        return $this->m_xrc->xr_addToGroup($sessid, $this->m_userName, $p_group);
    } // fn addToGroup


    /**
     * @param string $p_group
     *
     * @return mixed
     *      boolean True on success, False on failure
     *      PEAR Error
     */
    public function removeFromGroup($p_group)
    {
        if (empty($this->m_userName) || empty($p_group)) {
            return false;
        }

        $sessid = camp_session_get(CS_FILEARCHIVE_SESSION_VAR_NAME, '');
        if (empty($sessid)) {
            return new PEAR_Error(getGS('Can not remove the archive user from the group $1', $p_group)
                .': '.getGS('the connection to $1 was not established.', 'File Archive'));
        }
        return $this->m_xrc->xr_removeFromGroup($sessid, $this->m_userName, $p_group);
    }
} // class MMAUser_Alib

?>