<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Newscoop\NewscoopBundle\Form\Type\PreferencesType;

class SystemPrefController extends Controller
{
    /**
     * @Route("/admin/preferences")
     * @Template()
     */
    public function indexAction(Request $request)
    {   
        /*if (!\SecurityToken::isValid()) {
            camp_html_display_error(getGS('Invalid security token!'));
            exit;
        }*/
        $em = $this->container->get('em');
        $locations = $em->getRepository('Newscoop\NewscoopBundle\Entity\CityLocations')
            ->createQueryBuilder('a')
            ->select('count(a)')
            ->getQuery()
            ->getOneOrNullResult();

        $hasManagePermission = false;
        
        if(\SaaS::singleton()->hasPermission('ManageSystemPreferences')) {
            $hasManagePermission = true;
        }

        $max_upload_filesize = \SystemPref::Get("MaxUploadFileSize");

        if(empty($max_upload_filesize) || $max_upload_filesize == 0) {
            \SystemPref::Set("MaxUploadFileSize", ini_get('upload_max_filesize'));
        }

        $currentUser = $this->get('user')->getCurrentUser();

        $sp_session_lifetime = 0 + \SystemPref::Get('SiteSessionLifeTime');
        $php_ini_max_seconds = 0;
        $php_ini_gc_works = ini_get('session.gc_probability');

        if (!empty($php_ini_gc_works)) {
            $php_ini_max_seconds = 0 + ini_get('session.gc_maxlifetime');
            if (!empty($php_ini_max_seconds)) {
                if ($sp_session_lifetime > $php_ini_max_seconds) {
                    $sp_session_lifetime = $php_ini_max_seconds;
                }
            }
        }

        $upload_min_filesize = min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
        $mysql_client_command_path = \SystemPref::Get('MysqlClientCommandPath');

        if (!$locations) {
            $mysql_client_command_path_def = '/usr/bin/mysql';
            if (empty($mysql_client_command_path) && file_exists($mysql_client_command_path_def)) {
                $mysql_client_command_path = $mysql_client_command_path_def;
            }
        }
        //var_dump(camp_geodata_loaded($g_ado_db));die;
        /*if (!\camp_geodata_loaded($g_ado_db)) {

            $mysql_client_command_path_def = '/usr/bin/mysql';
            if ((empty($mysql_client_command_path)) && (file_exists($mysql_client_command_path_def))) {
                $mysql_client_command_path = $mysql_client_command_path_def;
            }
        }*/
        /*function camp_geodata_loaded($g_conn)
        {
            $queryStr_loc = 'SELECT count(*) AS cnt FROM CityLocations';
            $queryStr_nam = 'SELECT count(*) AS cnt FROM CityNames';

            $got_data = true;
            foreach (array($queryStr_loc, $queryStr_nam) as $one_query) {
                $rows = $g_conn->GetAll($one_query);
                foreach ((array) $rows as $row) {
                    if (0 == $row['cnt']) {
                        $got_data = false;
                        break;
                    }
                }
            }

            return $got_data;
        }*/

        $form = $this->container->get('form.factory')->create(new PreferencesType(), array(
            'siteonline' => \SystemPref::Get("SiteOnline"),
            'title' => \SystemPref::Get('SiteTitle'),
            'meta_keywords' => \SystemPref::Get('SiteMetaKeywords'),
            'meta_description' => \SystemPref::Get('SiteMetaDescription'),
            'timezone' => \SystemPref::Get('TimeZone'),
            'cache_engine' => \SystemPref::Get('DBCacheEngine'),
            'cache_template' => \SystemPref::Get('TemplateCacheHandler'),
            'cache_image' => \SystemPref::Get('ImagecacheLifetime'),
            'allow_recovery' => \SystemPref::Get('PasswordRecovery'),
            'password_recovery_form' => \SystemPref::Get('PasswordRecoveryFrom'),
            'secret_key' => \SystemPref::Get('SiteSecretKey'),
            'session_lifetime' => $sp_session_lifetime,
            'separator' => \SystemPref::Get("KeywordSeparator"),
            'captcha' => \SystemPref::Get("LoginFailedAttemptsNum"),
            'max_upload_size' => \SystemPref::Get("MaxUploadFileSize"),
            'automatic_collection' => \SystemPref::Get('CollectStatistics'),
            'smtp_host' => \SystemPref::Get('SMTPHost'),
            'smtp_port' => \SystemPref::Get('SMTPPort'),
            'email_contact' => \SystemPref::Get('EmailContact'),
            'email_from' => \SystemPref::Get('EmailFromAddress'),
            'image_ratio' => \SystemPref::Get('EditorImageRatio'),
            'image_width' => (int)\SystemPref::Get('EditorImageResizeWidth'),     
            'image_height' => (int)\SystemPref::Get('EditorImageResizeHeight'),
            'zoom' => \SystemPref::Get('EditorImageZoom'),
            'external_management' => \SystemPref::Get('ExternalSubscriptionManagement'),
            'use_replication_host' => \SystemPref::Get("DBReplicationHost"),
            'use_replication_user' => \SystemPref::Get("DBReplicationUser"),
            'use_replication_password' => \SystemPref::Get("DBReplicationPass"),
            'use_replication' => \SystemPref::Get("UseDBReplication"),
            'use_replication_port' => \SystemPref::Get("DBReplicationPort"),
            'template_filter' => \SystemPref::Get("TemplateFilter"),
            'external_cron_management' => \SystemPref::Get('ExternalCronManagement'),
     
        ), array());

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                if (!$currentUser->hasPermission('ChangeSystemPreferences')) {
                    camp_html_display_error(getGS("You do not have the right to change system preferences."));
                    exit;
                }

                $data = $form->getData();

                \SystemPref::Set('TimeZone', (string)$data['timezone']);

                if($hasManagePermission) {
                    // DB Caching
                    if (\SystemPref::Get('DBCacheEngine') != $data['cache_engine']) {
                        if (!$data['cache_engine'] || \CampCache::IsSupported($data['cache_engine'])) {
                            \SystemPref::Set('DBCacheEngine', $data['cache_engine']);
                            \CampCache::singleton()->clear('user');
                            \CampCache::singleton()->clear();
                        } else {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                getGS('Invalid: You need PHP $1 enabled in order to use the caching system.', $data['cache_engine'])
                            );

                            return $this->redirect($this->generateUrl('newscoop_newscoop_systempref_index'));
                        }
                    }

                    // Template Caching
                    if (\SystemPref::Get('TemplateCacheHandler') !=  $data['cache_template'] && $data['cache_template']) {
                        $handler = \CampTemplateCache::factory($data['cache_template']);
                        if ($handler && \CampTemplateCache::factory($data['cache_template'])->isSupported()) {
                            \SystemPref::Set('TemplateCacheHandler', $data['cache_template']);
                            \CampTemplateCache::factory($data['cache_template'])->clean();
                        } else {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                getGS('Invalid: You need PHP $1 enabled in order to use the template caching system.', $data['cache_template'])
                            );

                            return $this->redirect($this->generateUrl('newscoop_newscoop_systempref_index'));
                        }
                    } else {
                        \SystemPref::Set('TemplateCacheHandler', $data['cache_template']);
                    }

                    // Statistics collecting
                    \SystemPref::Set('CollectStatistics', $data['automatic_collection']);

                    // SMTP Host/Port
                    \SystemPref::Set('SMTPHost', $data['smtp_host']);
                    \SystemPref::Set('SMTPPort', $data['smtp_port']);
                    \SystemPref::Set('EmailContact', $data['email_contact']);
                    \SystemPref::Set('EmailFromAddress', $data['email_from']);

                    // Image resizing for WYSIWYG editor
                    \SystemPref::Set('EditorImageRatio', $data['image_ratio']);
                    \SystemPref::Set('EditorImageResizeWidth', $data['image_width']);
                    \SystemPref::Set('EditorImageResizeHeight', $data['image_height']);
                    \SystemPref::Set('EditorImageZoom', $data['zoom']);

                    // External subscription management
                    \SystemPref::Set('ExternalSubscriptionManagement', $data['external_management']);

                    // Replication
                    if ($data['use_replication'] == 'Y') {
                        // Database Replication Host, User and Password
                        if (!empty($data['use_replication_host']) && !empty($data['use_replication_user'])) {
                            \SystemPref::Set("DBReplicationHost", $data['use_replication_host']);
                            \SystemPref::Set("DBReplicationUser", $data['use_replication_user']);
                            \SystemPref::Set("DBReplicationPass", $data['use_replication_password']);
                            \SystemPref::Set("UseDBReplication", $data['use_replication']);
                        } else {;
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                getGS("Database Replication data incomplete")
                            );

                            return $this->redirect($this->generateUrl('newscoop_newscoop_systempref_index'));
                        }
                        // Database Replication Port
                        if (empty($data['use_replication_port']) || !is_int($data['use_replication_port'])) {
                            $data['use_replication_port'] = 3306;
                        }
                        \SystemPref::Set("DBReplicationPort", $data['use_replication_port']);
                    } else {
                        \SystemPref::Set("UseDBReplication", 'N');
                    }

                    // template filter
                    \SystemPref::Set("TemplateFilter", $data['template_filter']);

                    // External cron management
                    \SystemPref::Set('ExternalCronManagement', $data['external_cron_management']);
                }

                \SystemPref::Set('ImagecacheLifetime', $data['cache_image']);

                // Allow Password Recovery
                \SystemPref::Set('PasswordRecovery', $data['allow_recovery']);
                \SystemPref::Set('PasswordRecoveryFrom', $data['password_recovery_form']);

                // Secret key
                \SystemPref::Set('SiteSecretKey', $data['secret_key']);

                // Session life time
                \SystemPref::Set('SiteSessionLifeTime', $data['session_lifetime']);

                // Keyword Separator
                \SystemPref::Set("KeywordSeparator", $data['separator']);

                // Number of failed login attempts
                \SystemPref::Set("LoginFailedAttemptsNum", $data['captcha']);

                // Max Upload File Size
                $max_upload_filesize_bytes = camp_convert_bytes($data['max_upload_size']);
                if ($max_upload_filesize_bytes > 0 &&
                        $max_upload_filesize_bytes <= min(camp_convert_bytes(ini_get('post_max_size')), camp_convert_bytes(ini_get('upload_max_filesize')))) {
                    \SystemPref::Set("MaxUploadFileSize", $data['max_upload_size']);
                } else {
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        getGS('Invalid Max Upload File Size value submitted')
                    );
                }
            }
        }

        return array(
            'form' => $form->createView(),
            'php_ini_max_seconds' => $php_ini_max_seconds,
            'upload_min_filesize' => $upload_min_filesize,
            'hasManagePermission' => $hasManagePermission,
            'mysql_client_command_path' => $mysql_client_command_path,
        );
    }
}