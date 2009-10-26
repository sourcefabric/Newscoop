<?php

require_once('MMAUser.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/XR_CcClient.php');

class MMAUser_ALib extends MMAUser
{
	private $m_xrc = null;
	private $m_userName = null;
	private $m_userId = null;


	public function __construct($p_userName = null)
	{
        global $mdefs;

        $this->m_xrc = XR_CcClient::Factory($mdefs);
        if (!is_null($p_userName)) {
            $this->m_userName = $p_userName;
        }
	}
	
	
	public function create($p_userName, $p_password, $p_realName, $p_isAdmin = false)
	{
        if (!is_null($p_userName)) {
            $this->m_userName = $p_userName;
        }
        if (empty($this->m_userName)) {
            return false;
        }
        
        $sessid = camp_session_get('cc_sessid', '');
        if (empty($sessid)) {
            return new PEAR_Error(getGS('Can not $1 the archive user', getGS('create'))
            .': '.getGS('the connection to $1 was not established.', 'Campcaster'));
        }
        $group = $p_isAdmin ? 'Admins' : null;
        return $this->m_xrc->xr_createUser($sessid, $this->m_userName, $p_password, $p_realName, $group);
	}


    public function delete()
    {
        if (empty($this->m_userName)) {
            return false;
        }
        
        $sessid = camp_session_get('cc_sessid', '');
        if (empty($sessid)) {
            return new PEAR_Error(getGS('Can not $1 the archive user', getGS('delete'))
            .': '.getGS('the connection to $1 was not established.', 'Campcaster'));
        }
        return $this->m_xrc->xr_deleteUser($sessid, $this->m_userName);
    }


    public function changePassword($p_oldPassword, $p_password)
    {
        if (empty($this->m_userName)) {
            return false;
        }
        
        $sessid = camp_session_get('cc_sessid', '');
        if (empty($sessid)) {
            return new PEAR_Error(getGS('Can not $1 the archive user', getGS('change password of'))
            .': '.getGS('the connection to $1 was not established.', 'Campcaster'));
        }
        return $this->m_xrc->xr_changePassword($sessid, $this->m_userName, $p_oldPassword, $p_password);
    }


    public function addToGroup($p_group)
    {
        if (empty($this->m_userName) || empty($p_group)) {
            return false;
        }
        
        $sessid = camp_session_get('cc_sessid', '');
        if (empty($sessid)) {
            return new PEAR_Error(getGS('Can not add the archive user to the group $1', $p_group)
            .': '.getGS('the connection to $1 was not established.', 'Campcaster'));
        }
        return $this->m_xrc->xr_addToGroup($sessid, $this->m_userName, $p_group);
    }


    public function removeFromGroup($p_group)
    {
        if (empty($this->m_userName) || empty($p_group)) {
            return false;
        }
        
        $sessid = camp_session_get('cc_sessid', '');
        if (empty($sessid)) {
            return new PEAR_Error(getGS('Can not remove the archive user from the group $1', $p_group)
            .': '.getGS('the connection to $1 was not established.', 'Campcaster'));
        }
        return $this->m_xrc->xr_removeFromGroup($sessid, $this->m_userName, $p_group);
    }
}

?>