<?php
/**
 * StorageServer configuration file
 *
 *  configuration structure:
 *
 *  <dl>
 *   <dt>dsn<dd> datasource setting
 *   <dt>tblNamePrefix <dd>prefix for table names in the database
 *   <dt>authCookieName <dd>secret token cookie name
 *   <dt>AdminsGr <dd>name of admin group
 *   <dt>StationPrefsGr <dd>name of station preferences group
 *   <dt>AllGr <dd>name of 'all users' group
 *   <dt>TrashName <dd>name of trash folder (subfolder of the storageRoot)
 *   <dt>storageDir <dd>main directory for storing binary media files
 *   <dt>bufferDir <dd>directory for temporary files
 *   <dt>transDir <dd>directory for incomplete transferred files
 *   <dt>accessDir <dd>directory for symlinks to accessed files
 *   <dt>isArchive <dd>local/central flag
 *   <dt>validate <dd>enable/disable validator
 *   <dt>useTrash <dd>enable/disable safe delete (move to trash)
 *   <dt>storageUrlPath<dd>path-URL-part of storageServer base dir
 *   <dt>storageXMLRPC<dd>XMLRPC server script address relative to storageUrlPath
 *   <dt>storageUrlHost, storageUrlPort<dd>host and port of storageServer
 *   <dt>archiveUrlPath<dd>path-URL-part of archiveServer base dir
 *   <dt>archiveXMLRPC<dd>XMLRPC server script address relative to archiveUrlPath
 *   <dt>archiveUrlHost, archiveUrlPort<dd>host and port of archiveServer
 *   <dt>archiveAccountLogin, archiveAccountPass <dd>account info
 *           for login to archive
 *   <dt>sysSubjs<dd>system users/groups - cannot be deleted
 *  </dl>
 */

// these are the default values for the config
global $CC_CONFIG;

$mmaDir = dirname(__FILE__);

require_once(dirname($mmaDir).'/conf/configuration.php');
require_once(dirname($mmaDir).'/conf/db_connect.php');

$CC_CONFIG = array(
    /* ================================================== basic configuration */
    'dsn'           => array(
        'username'      => $Campsite['db']['user'],
        'password'      => $Campsite['db']['pass'],
        'hostspec'      => $Campsite['db']['host'],
        'phptype'       => $Campsite['db']['type'],
        'database'      => $Campsite['db']['name'],
    ),
    'tblNamePrefix' => 'ls_',
    /* ================================================ storage configuration */
    'authCookieName'=> 'campcaster_session_id',
    'AdminsGr'      => 'Admins',
    'StationPrefsGr'=> 'StationPrefs',
    'AllGr'         => 'All',
    'TrashName'     => 'trash_',
    'storageDir'    =>  $mmaDir.'/stor',
    'bufferDir'     =>  $mmaDir.'/stor/buffer',
    'transDir'      =>  $mmaDir.'/trans',
    'accessDir'     =>  $mmaDir.'/access',
    'pearPath'      =>  $mmaDir.'/../include/pear',
    'cronDir'       =>  $mmaDir.'/cron',
    'isArchive'     =>  FALSE,
    'validate'      =>  TRUE,
    'useTrash'      =>  TRUE,

    /* ==================================================== URL configuration */
    'storageUrlPath'        => '/campcasterStorageServer',
    'storageXMLRPC'         => 'xmlrpc/xrLocStor.php',
    'storageUrlHost'        => $Campsite['HOSTNAME'],
    'storageUrlPort'        => 80,

    /* ================================================ archive configuration */
    'archiveUrlPath'        => '/campcasterArchiveServer',
    'archiveXMLRPC'         => 'xmlrpc/xrArchive.php',
    'archiveUrlHost'        => $Campsite['HOSTNAME'],
    'archiveUrlPort'        => 80,
    'archiveAccountLogin'   => 'root',
    'archiveAccountPass'    => 'q',

    /* ============================================== scheduler configuration */
    'schedulerUrlPath'        => '',
    'schedulerXMLRPC'         => 'RC2',
    'schedulerUrlHost'        => $Campsite['HOSTNAME'],
    'schedulerUrlPort'        => 3344,
    'schedulerPass'           => 'change_me',

    /* ==================================== aplication-specific configuration */
    'objtypes'      => array(
        'RootNode'      => array('Folder'),
        'Storage'       => array('Folder', 'File', 'Replica'),
        'Folder'        => array('Folder', 'File', 'Replica'),
        'File'          => array(),
        'audioclip'     => array(),
        'playlist'      => array(),
        'Replica'       => array(),
    ),
    'allowedActions'=> array(
        'RootNode'      => array('classes', 'subjects'),
        'Folder'        => array('editPrivs', 'write', 'read'),
        'File'          => array('editPrivs', 'write', 'read'),
        'audioclip'     => array('editPrivs', 'write', 'read'),
        'playlist'      => array('editPrivs', 'write', 'read'),
        'Replica'       => array('editPrivs', 'write', 'read'),
        '_class'        => array('editPrivs', 'write', 'read'),
    ),
    'allActions'    =>  array(
        'editPrivs', 'write', 'read', 'classes', 'subjects'
    ),

    /* ============================================== auxiliary configuration */
    'RootNode'      => 'RootNode',
    'tmpRootPass'   => 'q',

    /* =================================================== cron configuration */
    'cronUserName'      => $Campsite['APACHE_USER'],
    'lockfile'     		=> $mmaDir.'/stor/buffer/cron.lock',
    'cronfile'          => $mmaDir.'/cron/croncall.php',
    'paramdir'          => $mmaDir.'/cron/params',
);

unset($mmaDir);

// Add database table names
$CC_CONFIG['filesTable'] = $CC_CONFIG['tblNamePrefix'].'files';
$CC_CONFIG['mdataTable'] = $CC_CONFIG['tblNamePrefix'].'mdata';
$CC_CONFIG['accessTable'] = $CC_CONFIG['tblNamePrefix'].'access';
$CC_CONFIG['permTable'] = $CC_CONFIG['tblNamePrefix'].'perms';
$CC_CONFIG['sessTable'] = $CC_CONFIG['tblNamePrefix'].'sess';
$CC_CONFIG['subjTable'] = $CC_CONFIG['tblNamePrefix'].'subjs';
$CC_CONFIG['smembTable'] = $CC_CONFIG['tblNamePrefix'].'smemb';
$CC_CONFIG['classTable'] = $CC_CONFIG['tblNamePrefix'].'classes';
$CC_CONFIG['cmembTable'] = $CC_CONFIG['tblNamePrefix'].'cmemb';
$CC_CONFIG['treeTable'] = $CC_CONFIG['tblNamePrefix'].'tree';
$CC_CONFIG['structTable'] = $CC_CONFIG['tblNamePrefix'].'struct';
$CC_CONFIG['transTable'] = $CC_CONFIG['tblNamePrefix'].'trans';
$CC_CONFIG['prefTable'] = $CC_CONFIG['tblNamePrefix'].'pref';
$CC_CONFIG['playlogTable'] = 'playlog';
$CC_CONFIG['scheduleTable'] = 'schedule';
$CC_CONFIG['backupTable'] = 'backup';

$CC_CONFIG['sysSubjs'] = array(
    'root', $CC_CONFIG['AdminsGr'], $CC_CONFIG['AllGr'], $CC_CONFIG['StationPrefsGr']
);
$old_ip = get_include_path();
set_include_path('.'.PATH_SEPARATOR.$CC_CONFIG['pearPath'].PATH_SEPARATOR.$old_ip);


// workaround for missing folders
foreach (array('storageDir', 'bufferDir', 'transDir', 'accessDir', 'pearPath', 'cronDir') as $d) {
    $test = file_exists($CC_CONFIG[$d]);
    if ( $test === FALSE ) {
        @mkdir($CC_CONFIG[$d], 02775);
        if (file_exists($CC_CONFIG[$d])) {
            $rp = realpath($CC_CONFIG[$d]);
            //echo " * Directory $rp created\n";
        } else {
            echo " * Failed creating {$CC_CONFIG[$d]}\n";
            exit(1);
        }
    } else {
        $rp = realpath($CC_CONFIG[$d]);
    }
    $CC_CONFIG[$d] = $rp;
}

?>