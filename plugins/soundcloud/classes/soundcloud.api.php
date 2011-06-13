<?php
/**
 * @package Newscoop
 * @subpackage Soundcloud plugin
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

class SoundcloudAPI
{
    public $error = null;
    private $_token = null;

    public function SoundcloudAPI()
    {
        self::_checkCURL();
        $this->_token = SystemPref::Get('PLUGIN_SOUNDCLOUD_ACCESS_TOKEN');
    }

    public function login()
    {
        $ch = curl_init();
        $url = 'https://api.soundcloud.com/oauth2/token';
        $data = 'client_id=' . SystemPref::Get('PLUGIN_SOUNDCLOUD_CLIENT_ID')
              . '&client_secret=' . SystemPref::Get('PLUGIN_SOUNDCLOUD_CLIENT_SECRET')
              . '&grant_type=password'
              . '&username=' . SystemPref::Get('PLUGIN_SOUNDCLOUD_USERNAME')
              . '&password=' . SystemPref::Get('PLUGIN_SOUNDCLOUD_PASSWORD');

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $result = curl_exec($ch);
        curl_close($ch);
        $aToken = @json_decode($result, true);
        if (empty($aToken['error']) && !empty($aToken['access_token'])) {
            SystemPref::Set('PLUGIN_SOUNDCLOUD_ACCESS_TOKEN', $aToken['access_token']);
            $this->_token = $aToken['access_token'];
            $this->error = null;
            $userid = SystemPref::Get('PLUGIN_SOUNDCLOUD_USER_ID');
            if (empty($userid)) {
                $this->profile();
            }
            return true;
        } else {
            $this->_setError($aToken);
            return false;
        }
    }

    function trackSearch(array $aParams)
    {
        if (!$this->_token && !$this->login()) {
            return array();
        }
        $search = null;
        foreach ($aParams as $key => $value) {
            if (!empty($value)) {
                $search .= $key . '=' . urlencode($value) . '&';
            }
        }
        $ch = curl_init();
        $id = SystemPref::Get('PLUGIN_SOUNDCLOUD_USER_ID');
        //$url = "https://api.soundcloud.com/tracks.json?" . $search;
        $url = "https://api.soundcloud.com/users/$id/tracks.json?" . $search;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $this->_token));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $result = curl_exec($ch);
        curl_close($ch);
        $aResult = json_decode($result, true);
        if (empty($aResult['error']) && empty($aResult['errors']) && is_array($aResult)) {
            return $aResult;
        } elseif (@$aResult['error'] == '401 - Unauthorized' || empty($aResult)) {
            $this->_token = null;
            return $this->trackSearch($aParams);
        }
        $this->_setError($aResult);
        return array();
    }

    function trackLoad($id)
    {
        if (!$this->_token && !$this->login()) {
            return array();
        }
        $ch = curl_init();
        $url = "https://api.soundcloud.com/tracks/$id.json?";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $this->_token));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $result = curl_exec($ch);
        curl_close($ch);
        $aResult = json_decode($result, true);
        if (empty($aResult['error']) && empty($aResult['errors']) && is_array($aResult)) {
            return $aResult;
        } elseif (@$aResult['error'] == '401 - Unauthorized' || empty($aResult)) {
            $this->_token = null;
            return $this->trackLoad($id);
        }
        $this->_setError($aResult);
        return array();
    }

    function trackDelete($id)
    {
        if (!$this->_token && !$this->login()) {
            return array();
        }
        $ch = curl_init();
        $url = "https://api.soundcloud.com/tracks/$id.json?";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $this->_token));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $result = curl_exec($ch);
        curl_close($ch);
        $aResult = json_decode($result, true);
        if (empty($aResult['error']) && empty($aResult['errors']) && is_array($aResult)) {
            return $aResult;
        } elseif (@$aResult['error'] == '401 - Unauthorized' || empty($aResult)) {
            $this->_token = null;
            return $this->trackDelete($id);
        }
        $this->_setError($aResult);
        return array();
    }

    function profile()
    {
        if (!$this->_token && !$this->login()) {
            return array();
        }
        $ch = curl_init();
        $url = "https://api.soundcloud.com/me.json";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $this->_token));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $result = curl_exec($ch);
        curl_close($ch);
        $aResult = json_decode($result, true);
        if (empty($aResult['error']) && empty($aResult['errors']) && is_array($aResult)) {
            SystemPref::Set('PLUGIN_SOUNDCLOUD_USER_ID', $aResult['id']);
            return $aResult;
        } elseif (@$aResult['error'] == '401 - Unauthorized' || empty($aResult)) {
            $this->_token = null;
            return $this->profile();
        }
        $this->_setError($aResult);
        return array();
    }

    function trackUpload(array $aParams)
    {
        set_time_limit(0);
        if (!$this->_token && !$this->login()) {
            return array();
        }
        $data = array();
        foreach ($aParams as $key => $value) {
            $data["track[$key]"] = $value;
        }
        $ch = curl_init();
        $url = "https://api.soundcloud.com/tracks.json";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $this->_token));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);
        $aResult = json_decode($result, true);
        if (empty($aResult['error']) && empty($aResult['errors']) && is_array($aResult)) {
            return $aResult;
        } elseif (@$aResult['error'] == '401 - Unauthorized' || empty($aResult)) {
            $this->_token = null;
            return $this->trackUpload($aParams);
        }
        $this->_setError($aResult);
        return array();
    }

    function trackUpdate(array $aParams)
    {
        if (!$this->_token && !$this->login()) {
            return array();
        }
        $data = array();
        foreach ($aParams as $key => $value) {
            $data["track[$key]"] = $value;
        }
        $ch = curl_init();
        $url = "https://api.soundcloud.com/tracks/{$aParams['id']}.json";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $this->_token));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);
        $aResult = json_decode($result, true);
        if (empty($aResult['error']) && empty($aResult['errors']) && is_array($aResult)) {
            return $aResult;
        } elseif (@$aResult['error'] == '401 - Unauthorized' || empty($aResult)) {
            $this->_token = null;
            return $this->trackUpdate($aParams);
        }
        $this->_setError($aResult);
        return array();
    }

    function setList()
    {
        if (!$this->_token && !$this->login()) {
            return array();
        }
        $ch = curl_init();
        $id = SystemPref::Get('PLUGIN_SOUNDCLOUD_USER_ID');
        $url = "https://api.soundcloud.com/users/$id/playlists.json?";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $this->_token));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $result = curl_exec($ch);
        curl_close($ch);
        $aResult = json_decode($result, true);
        if (empty($aResult['error']) && empty($aResult['errors']) && is_array($aResult)) {
            return $aResult;
        } elseif (@$aResult['error'] == '401 - Unauthorized' || empty($aResult)) {
            $this->_token = null;
            return $this->setList();
        }
        $this->_setError($aResult);
        return array();
    }

    function setLoad($id)
    {
        if (!$this->_token && !$this->login()) {
            return array();
        }
        $ch = curl_init();
        $url = "https://api.soundcloud.com/playlists/$id.json?";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $this->_token));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $result = curl_exec($ch);
        curl_close($ch);
        $aResult = json_decode($result, true);
        if (empty($aResult['error']) && empty($aResult['errors']) && is_array($aResult)) {
            return $aResult;
        } elseif (@$aResult['error'] == '401 - Unauthorized' || empty($aResult)) {
            $this->_token = null;
            return $this->setLoad($id);
        }
        $this->_setError($aResult);
        return array();
    }

    function setUpdate(array $aParams)
    {
        if (!$this->_token && !$this->login()) {
            return array();
        }
        $data = 'playlist[id]=' . $aParams['id'];
        foreach ($aParams as $key => $value) {
            if ($key != 'tracks') {
                $data .= "&playlist[$key]=" . urlencode($value);
            } else {
                foreach ($value as $track) {
                    $data .= "&playlist[tracks][][id]=" . urlencode($track['id']);
                }
            }
        }
        $ch = curl_init();
        $url = "https://api.soundcloud.com/playlists/{$aParams['id']}.json";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $this->_token));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);
        $aResult = json_decode($result, true);
        if (empty($aResult['error']) && empty($aResult['errors']) && is_array($aResult)) {
            return $aResult;
        } elseif (@$aResult['error'] == '401 - Unauthorized' || empty($aResult)) {
            $this->_token = null;
            return $this->setUpdate($aParams);
        }
        $this->_setError($aResult);
        return array();
    }

    private static function _checkCURL()
    {
        if (!function_exists('curl_init')) {
            camp_html_display_error(getGS('Soundcloud plugin requires php_curl module.'));
            exit;
        }
        return true;
    }

    private function _setError($result)
    {
        if (!empty($result['error'])) {
            $this->error = $result['error'];
        } elseif (!empty($result['errors'])) {
            $this->error = $result['errors']['error'];
        } else {
            $this->error = getGS('connection error');
        }
    }
}

?>