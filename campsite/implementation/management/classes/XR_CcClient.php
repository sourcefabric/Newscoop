<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
// is not defined in these cases.
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/classes/SystemPref.php');
require_once('XML/RPC.php');

/**
 *  Array with methods description
 *
 *  Each element has method name as key and contains four subfields:
 *  <ul>
 *   <li>m</li> full method name (include optional prefix)
 *   <li>p</li> array of input parameter names
 *   <li>t</li> array of input parameter types
 *   <li>r</li> array of result element names (not used there at present)
 *   <li>e</li> array of error codes/messages (not used there at present)
 *  </ul>
 */
$mdefs = array(
    "xr_getVersion" => array(
        'm'=>'locstor.getVersion',
        'p'=>array(),
        't'=>array(),
        'r'=>array('version'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower layer'
        )
    ),
    "xr_authenticate" => array(
        'm'=>'locstor.authenticate',
        'p'=>array('login'/*string*/, 'pass'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('authenticate'/*bool*/),
        'e'=>array(
            '2000'=>'Bad parameters',
            '2001'=>'Invalid argument format',
            '2005'=>'Database error'
        )
    ),
    "xr_login" => array(
        'm'=>'locstor.login',
        'p'=>array('login'/*string*/, 'pass'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('sessid'/*string*/),
        'e'=>array(
            '2001'=>'invalid argument format',
            '2002'=>'missing login argument',
            '2003'=>'missing password argument',
            '2004'=>'the authentication server reported an error',
            '802' =>'incorrect username or password'
        )
    ),
    "xr_logout" => array(
        'm'=>'locstor.logout',
        'p'=>array('sessid'/*string*/),
        't'=>array('string'),
        'r'=>array('status'/*bool*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '803'=>'logout failed - not logged'
        )
     ),
    "xr_storeAudioClipOpen" => array(
        'm'=>'locstor.storeAudioClipOpen',
        'p'=>array('sessid'/*string*/, 'gunid'/*string*/, 'metadata'/*string*/, 'fname'/*string*/, 'chsum'/*string*/),
        't'=>array('string', 'string', 'string', 'string', 'string'),
        'r'=>array('url'/*string*/, 'token'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_storeAudioClipClose" => array(
        'm'=>'locstor.storeAudioClipClose',
        'p'=>array('sessid'/*string*/, 'token'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('gunid'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_storeWebstream" => array(
        'm'=>'locstor.storeWebstream',
        'p'=>array('sessid'/*string*/, 'gunid'/*string*/, 'metadata'/*string*/, 'fname'/*string*/, 'url'/*string*/),
        't'=>array('string', 'string', 'string', 'string', 'string'),
        'r'=>array('gunid'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_accessRawAudioData" => array(
        'm'=>'locstor.accessRawAudioData',
        'p'=>array('sessid'/*string*/, 'gunid'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('url'/*string*/, 'token'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_releaseRawAudioData" => array(
        'm'=>array('locstor.releaseRawAudioData'),
        'p'=>array('token'/*string*/),
        't'=>array('string'),
        'r'=>array('status'/*bool*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_downloadRawAudioDataOpen" => array(
        'm'=>'locstor.downloadRawAudioDataOpen',
        'p'=>array('sessid'/*string*/, 'gunid'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('url'/*string*/, 'token'/*string*/, 'chsum'/*string*/, 'size'/*int*/, 'filename'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later',
            '847'=>'invalid gunid'
        )
    ),
    "xr_downloadRawAudioDataClose" => array(
        'm'=>'locstor.downloadRawAudioDataClose',
        'p'=>array('sessid'/*string*/, 'token'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('gunid'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_downloadMetadataOpen" => array(
        'm'=>'locstor.downloadMetadataOpen',
        'p'=>array('sessid'/*string*/, 'gunid'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('url'/*string*/, 'token'/*string*/, 'chsum'/*string*/, 'filename'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_downloadMetadataClose" => array(
        'm'=>'locstor.downloadMetadataClose',
        'p'=>array('sessid'/*string*/, 'token'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('gunid'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_deleteAudioClip" => array(
        'm'=>'locstor.deleteAudioClip',
        'p'=>array('sessid'/*string*/, 'gunid'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('status'/*bool*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_createPlaylist" => array(
        'm'=>'locstor.createPlaylist',
        'p'=>array('sessid'/*string*/, 'plid'/*string*/, 'fname'/*string*/),
        't'=>array('string', 'string', 'string'),
        'r'=>array('plid'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_editPlaylist" => array(
        'm'=>'locstor.editPlaylist',
        'p'=>array('sessid'/*string*/, 'plid'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('url'/*string*/, 'token'/*string*/, 'chsum'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_savePlaylist" => array(
        'm'=>'locstor.savePlaylist',
        'p'=>array('sessid'/*string*/, 'token'/*string*/, 'newPlaylist'/*string*/),
        't'=>array('string', 'string', 'string'),
        'r'=>array('plid'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_revertEditedPlaylist" => array(
        'm'=>'locstor.revertEditedPlaylist',
        'p'=>array('sessid'/*string*/, 'token'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('plid'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_deletePlaylist" => array(
        'm'=>'locstor.deletePlaylist',
        'p'=>array('sessid'/*string*/, 'plid'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('status'/*bool*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_accessPlaylist" => array(
        'm'=>'locstor.accessPlaylist',
        'p'=>array('sessid'/*string*/, 'plid'/*string*/, 'recursive'/*bool*/),
        't'=>array('string', 'string', 'bool'),
        'r'=>array('url'/*string*/, 'token'/*sring*/, 'chsum'/*string*/, 'content'/*array*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later',
            '847'=>'invalid plid'
        )
    ),
    "xr_releasePlaylist" => array(
        'm'=>'locstor.releasePlaylist',
        'p'=>array('token'/*string*/, 'recursive'/*bool*/),
        't'=>array('string', 'bool'),
        'r'=>array('plid'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_existsPlaylist" => array(
        'm'=>'locstor.existsPlaylist',
        'p'=>array('sessid'/*string*/, 'plid'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('exists'/*bool*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_playlistIsAvailable" => array(
        'm'=>'locstor.playlistIsAvailable',
        'p'=>array('sessid'/*string*/, 'plid'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('available'/*bool*/, 'ownerid'/*int*/, 'ownerlogin'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_exportPlaylistOpen" => array(
        'm'=>'locstor.exportPlaylistOpen',
        'p'=>array('sessid'/*string*/, 'plids'/*array*/, 'type'/*string*/, 'standalone'/*bool*/),
        't'=>array('string', 'array', 'string', 'bool'),
        'r'=>array('url'/*string*/, 'token'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_exportPlaylistClose" => array(
        'm'=>'locstor.exportPlaylistClose',
        'p'=>array('token'/*string*/),
        't'=>array('string'),
        'r'=>array('status'/*bool*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_importPlaylistOpen" => array(
        'm'=>'locstor.importPlaylistOpen',
        'p'=>array('sessid'/*string*/, 'chsum'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('url'/*string*/, 'token'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_importPlaylistClose" => array(
        'm'=>'locstor.importPlaylistClose',
        'p'=>array('token'/*string*/),
        't'=>array('string'),
        'r'=>array('gunid'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_renderPlaylistToFileOpen" => array(
        'm'=>'locstor.renderPlaylistToFileOpen',
        'p'=>array('sessid'/*string*/, 'plid'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('token'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_renderPlaylistToFileCheck" => array(
        'm'=>'locstor.renderPlaylistToFileCheck',
        'p'=>array('token'/*string*/),
        't'=>array('string'),
        'r'=>array('status'/*string*/, 'url'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_renderPlaylistToFileClose" => array(
        'm'=>'locstor.renderPlaylistToFileClose',
        'p'=>array('token'/*string*/),
        't'=>array('string'),
        'r'=>array('status'/*bool*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_renderPlaylistToStorageOpen" => array(
        'm'=>'locstor.renderPlaylistToStorageOpen',
        'p'=>array('sessid'/*string*/, 'plid'/*string*/),
        't'=>array('string','string'),
        'r'=>array('token'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_renderPlaylistToStorageCheck" => array(
        'm'=>'locstor.renderPlaylistToStorageCheck',
        'p'=>array('token'/*string*/),
        't'=>array('string'),
        'r'=>array('status'/*string*/, 'gunid'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_renderPlaylistToRSSOpen" => array(
        'm'=>'locstor.renderPlaylistToRSSOpen',
        'p'=>array('sessid'/*string*/, 'plid'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('token'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_renderPlaylistToRSSCheck" => array(
        'm'=>'locstor.renderPlaylistToRSSCheck',
        'p'=>array('token'/*string*/),
        't'=>array('string'),
        'r'=>array('status'/*string*/, 'url'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_renderPlaylistToRSSClose" => array(
        'm'=>'locstor.renderPlaylistToRSSClose',
        'p'=>array('token'/*string*/),
        't'=>array('string'),
        'r'=>array('status'/*bool*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_createBackupOpen" => array(
        'm'=>'locstor.createBackupOpen',
        'p'=>array('sessid'/*string*/, 'criteria'/*struct*/),
        't'=>array('string', 'struct'),
        'r'=>array('token'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_createBackupCheck" => array(
        'm'=>'locstor.createBackupCheck',
        'p'=>array('token'/*string*/),
        't'=>array('string'),
        'r'=>array('status'/*string*/, 'url'/*string*/, 'metafile'/*string*/, 'faultString'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_createBackupList" => array(
        'm'=>'locstor.createBackupList',
        'p'=>array('stat'/*string*/),
        't'=>array('string'),
        'r'=>array('status'/*string*/, 'url'/*string*/, 'metafile'/*string*/, 'faultString'/*string*/)
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_createBackupClose" => array(
        'm'=>'locstor.createBackupClose',
        'p'=>array('token'/*string*/),
        't'=>array('string'),
        'r'=>array('status'/*bool*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_restoreBackupOpen" => array(
        'm'=>'locstor.restoreBackupOpen',
        'p'=>array('sessid'/*string*/, 'filename'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('token'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_restoreBackupCheck" => array(
        'm'=>'locstor.restoreBackupCheck',
        'p'=>array('token'/*string*/),
        't'=>array('string'),
        'r'=>array('status'/*array*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_restoreBackupClose" => array(
        'm'=>'locstor.restoreBackupClose',
        'p'=>array('token'/*string*/),
        't'=>array('string'),
        'r'=>array('status'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_existsAudioClip" => array(
        'm'=>'locstor.existsAudioClip',
        'p'=>array('sessid'/*string*/, 'gunid'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('exists'/*bool*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_getAudioClip" => array(
        'm'=>'locstor.getAudioClip',
        'p'=>array('sessid'/*string*/, 'gunid'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('metadata'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_updateAudioClipMetadata" => array(
        'm'=>'locstor.updateAudioClipMetadata',
        'p'=>array('sessid'/*string*/, 'gunid'/*string*/, 'metadata'/*string*/),
        't'=>array('string', 'string', 'string'),
        'r'=>array('status'/*bool*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_searchMetadata" => array(
        'm'=>'locstor.searchMetadata',
        'p'=>array('sessid'/*string*/, 'criteria'/*array*/),
        't'=>array('string', 'array'),
        'r'=>array('cnt'/*int*/, 'results'/*array*/)
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_browseCategory" => array(
        'm'=>'locstor.browseCategory',
        'p'=>array('sessid'/*string*/, 'category'/*string*/, 'criteria'/*array*/),
        't'=>array('string', 'string', 'array'),
        'r'=>array('results'/*array*/, 'cnt'/*int*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
        )
    ),
    "xr_loadPref" => array(
        'm'=>'locstor.loadPref',
        'p'=>array('sessid'/*string*/, 'key'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('value'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later',
            '848'=>'invalid session id',
            '849'=>'invalid preference key'
        )
    ),
    "xr_savePref" => array(
        'm'=>'locstor.savePref',
        'p'=>array('sessid'/*string*/, 'key'/*string*/, 'value'/*string*/),
        't'=>array('string', 'string', 'string'),
        'r'=>array('status'/*bool*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later',
            '848'=>'invalid session id'
        )
    ),
    "xr_delPref" => array(
        'm'=>'locstor.delPref',
        'p'=>array('sessid'/*string*/, 'key'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('status'/*bool*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later',
            '848'=>'invalid session id',
            '849'=>'invalid preference key'
        )
    ),
    "xr_loadGroupPref" => array(
        'm'=>'locstor.loadGroupPref',
        'p'=>array('sessid'/*string*/, 'group'/*string*/, 'key'/*string*/),
        't'=>array('string', 'string', 'string'),
        'r'=>array('value'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later',
            '820'=>'invalid group name',
            '848'=>'invalid session id',
            '849'=>'invalid preference key'
        )
    ),
    "xr_saveGroupPref" => array(
        'm'=>'locstor.saveGroupPref',
        'p'=>array('sessid'/*string*/, 'group'/*string*/, 'key'/*string*/, 'value'/*string*/),
        't'=>array('string', 'string', 'string', 'string'),
        'r'=>array('status'/*bool*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later',
            '820'=>'invalid group name',
            '848'=>'invalid session id'
        )
    ),
    "xr_getTransportInfo" => array(
        'm'=>'locstor.getTransportInfo',
        'p'=>array('trtok'/*string*/),
        't'=>array('string'),
        'r'=>array('trtype'/*string*/, 'direction'/*string*/, 'state'/*string*/, 'expectedsize'/*int*/, 'realsize'/*int*/, 'expectedchsum'/*string*/, 'realchsum'/*string*/, 'title'/*string*/, 'errmsg'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later'
            '848'=>'invalid session id',
            '872'=>'invalid transport token'
        )
    ),
    "xr_turnOnOffTransports" => array(
        'm'=>'locstor.turnOnOffTransports',
        'p'=>array('sessid'/*string*/, 'onOff'/*bool*/),
        't'=>array('string', 'bool'),
        'r'=>array('state'/*bool*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later',
            '848'=>'invalid session id',
            '872'=>'invalid transport token'
        )
    ),
    "xr_doTransportAction" => array(
        'm'=>'locstor.doTransportAction',
        'p'=>array('sessid'/*string*/, 'trtok'/*string*/, 'action'/*string*/),
        't'=>array('string', 'string', 'string'),
        'r'=>array('state'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later',
            '848'=>'invalid session id',
            '872'=>'invalid transport token'
        )
    ),
    "xr_uploadFile2Hub" => array(
        'm'=>'locstor.uploadFile2Hub',
        'p'=>array('sessid'/*string*/, 'filePath'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('trtok'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later',
            '848'=>'invalid session id',
            '872'=>'invalid transport token'
        )
    ),
    "xr_getHubInitiatedTransfers" => array(
        'm'=>'locstor.getHubInitiatedTransfers',
        'p'=>array('sessid'/*string*/),
        't'=>array('string'),
        'r'=>array(array('trtok'/*string*/)),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later',
            '848'=>'invalid session id',
            '872'=>'invalid transport token'
        )
    ),
    "xr_startHubInitiatedTransfer" => array(
        'm'=>'locstor.startHubInitiatedTransfer',
        'p'=>array('trtok'/*string*/),
        't'=>array('string'),
        'r'=>array('trtok'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later',
            '848'=>'invalid session id',
            '872'=>'invalid transport token'
        )
    ),
    "xr_upload2Hub" => array(
        'm'=>'locstor.upload2Hub',
        'p'=>array('sessid'/*string*/, 'gunid'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('trtok'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later',
            '848'=>'invalid session id',
            '872'=>'invalid transport token'
        )
    ),
    "xr_downloadFromHub" => array(
        'm'=>'locstor.downloadFromHub',
        'p'=>array('sessid'/*string*/, 'gunid'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('trtok'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later',
            '848'=>'invalid session id',
            '872'=>'invalid transport token'
        )
    ),
    "xr_globalSearch" => array(
        'm'=>'locstor.globalSearch',
        'p'=>array('sessid'/*string*/, 'criteria'/*array*/),
        't'=>array('string', 'array'),
        'r'=>array('trtok'/*string*/),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later',
            '848'=>'invalid session id',
            '872'=>'invalid transport token'
        )
    ),
    "xr_getSearchResults" => array(
        'm'=>'locstor.getSearchResults',
        'p'=>array('trtok'/*string*/),
        't'=>array('string'),
        'r'=>array(array('gunid'/*string*/, 'type'/*string*/, 'title'/*string*/, 'creator'/*string*/, 'length'/*string*/)),
        'e'=>array(
            '3'=>'incorrect parameters',
            '801'=>'bad params',
            '805'=>'message from lower later',
            '848'=>'invalid session id',
            '872'=>'invalid transport token'
        )
    )
);

/**
 * XR_CcClient provides a XML RPC client for the Campcaster
 * XML RPC API.
 *
 * @package Campsite
 */
class XR_CcClient {
    /**
     *  Array with methods description
     */
    var $mdefs = array();
    /**
     *  XMLRPC client object reference
     */
    var $client = NULL;
    /**
     *  Verbosity flag
     */
    var $verbose = FALSE;
    /**
     *  XMLRPC debug flag
     */
    var $debug = 0;

    /**
     *  Constructor - pelase DON'T CALL IT, use factory method instead
     *
     *  @param mdefs array, hash array with methods description
     *  @param debug int, XMLRPC debug flag
     *  @param verbose boolean, verbosity flag
     *
     *  @return this
     */
    function XR_CcClient ($mdefs, $debug=0, $verbose=FALSE)
    {
        $this->mdefs = $mdefs;
        $this->debug = $debug;
        $this->verbose = $verbose;

        $serverPath = "http://localhost:8080/livesupport/storageServer/var/xmlrpc/xrLocStor.php";
        /*
        $serverPath = "http://"
            . SystemPref::Get('CampcasterHost') . ":"
            . SystemPref::Get('CampcasterPort')
            . SystemPref::Get('CampcasterPath') . "/"
            . SystemPref::Get('CampcasterXRPC');

        $confPrefix = "storage";
        $serverPath = "http://{$config["{$confPrefix}UrlHost"]}"
                     .":{$config["{$confPrefix}UrlPort"]}"
                     ."{$config["{$confPrefix}UrlPath"]}"
                     ."/{$config["{$confPrefix}XMLRPC"]}";
        */
        if($this->verbose) echo "serverPath: $serverPath\n";
        $url = parse_url($serverPath);
        $this->client = new XML_RPC_Client($url['path'], $url['host'], $url['port']);
    } // constructor

    /**
     *  Factory, create object instance
     *
     *  In fact it doesn't create instance of SchedulerPhpClient, but
     *  dynamically extend this class with set of methods based on $mdefs array
     *  (using eval function) and instantiate resulting class
     *  SchedulerPhpClientCore instead.
     *  Each new method in this subclass accepts parameters according to $mdefs
     *  array, call wrapper callMethod(methodname, parameters) and return its
     *  result.
     *
     *  @param mdefs array, hash array with methods description
     *  @param debug int, XMLRPC debug flag
     *  @param verbose boolean, verbosity flag
     *
     *  @return object, created object instance
     */
    function &factory($mdefs, $debug=0, $verbose=FALSE)
    {
        $f = '';
        foreach($mdefs as $fn=>$farr){
            $f .=
                '    function '.$fn.'(){'."\n".
                '        $pars = func_get_args();'."\n".
                '        $r = $this->callMethod("'.$fn.'", $pars);'."\n".
                '        return $r;'."\n".
                '    }'."\n";
        }
        $e =
            "class XR_CcClientCore extends XR_CcClient{\n".
            "$f\n".
            "}\n";
        if(FALSE === eval($e)) return false; //$dbc->raiseError("Eval failed");
        $spc =& new XR_CcClientCore($mdefs, $debug, $verbose);

        return $spc;
    } // fn factory

    /**
     *  XMLRPC methods wrapper
     *  Encode XMLRPC request message, send it, receive and decode response.
     *
     *  @param method string, method name
     *  @param gettedPars array, returned by func_get_args() in called method
     *
     *  @return array, PHP hash with response
     */
    function callMethod($method, $gettedPars)
    {
        $parr = array();
        $XML_RPC_val = new XML_RPC_Value;
        foreach($this->mdefs[$method]['p'] as $i=>$p){
            $parr[$p] = new XML_RPC_Value;
            $parr[$p]->addScalar($gettedPars[$i], $this->mdefs[$method]['t'][$i]);
        }
        $XML_RPC_val->addStruct($parr);
        $fullmethod = $this->mdefs[$method]['m'];
        $msg = new XML_RPC_Message($fullmethod, array($XML_RPC_val));
        if($this->verbose){
            echo "parr:\n";
            var_dump($parr);
            echo "message:\n";
            echo $msg->serialize()."\n";
        }
        $this->client->setDebug($this->debug);
        $res = $this->client->send($msg);
        if($res->faultCode() > 0) {
            return PEAR::raiseError(
                "XR_CcClient::$method:".$res->faultString()." ".
                $res->faultCode()."\n", $res->faultCode(),
                PEAR_ERROR_RETURN
            );
        }
        if($this->verbose){
            echo "result:\n";
            echo $res->serialize();
        }
        $val = $res->value();
        $resp = XML_RPC_decode($res->value());

        return $resp;
    } // fn callMethod
    
} // class XR_CcClient

?>