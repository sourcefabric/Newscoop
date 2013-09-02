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
        $em = $this->container->get('em');
        $locations = $em->getRepository('Newscoop\NewscoopBundle\Entity\CityLocations')
            ->createQueryBuilder('a')
            ->select('count(a)')
            ->getQuery()
            ->getOneOrNullResult();

        $cities = $em->getRepository('Newscoop\NewscoopBundle\Entity\CityNames')
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
        $translator = $this->get('translator');

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

        if (!$locations || !$cities) {
            $mysql_client_command_path_def = '/usr/bin/mysql';
            if (empty($mysql_client_command_path) && file_exists($mysql_client_command_path_def)) {
                $mysql_client_command_path = $mysql_client_command_path_def;
            }
        }

        $geo_preferred_lang = \SystemPref::Get('GeoSearchPreferredLanguage');
        if (empty($geo_preferred_lang)) {
            $geo_preferred_lang = 'en';
        }

        $default_marker_source = \SystemPref::Get('MapMarkerSourceDefault');

        $marker_icons = \Geo_Preferences::GetIconsFiles();
        if (0 < count($marker_icons)) {
            $default_marker_source = array();
            foreach ($marker_icons as $one_icon) {
                $default_marker_source[$one_icon] = $one_icon;
            }
        }

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
            'mysql_client_command_path' => $mysql_client_command_path,
            'center_latitude_default' => (float)\SystemPref::Get('MapCenterLatitudeDefault'),
            'center_longitude_default' => (float)\SystemPref::Get('MapCenterLongitudeDefault'),
            'map_display_resolution_default' => (int)\SystemPref::Get('MapDisplayResolutionDefault'),
            'map_view_width_default' => \SystemPref::Get('MapViewWidthDefault'),
            'map_view_height_default' => \SystemPref::Get('MapViewHeightDefault'),
            'map_auto_focus_default' => \SystemPref::Get('MapAutoFocusDefault') == '0' ? false : true,
            'map_auto_focus_max_zoom' => \SystemPref::Get('MapAutoFocusMaxZoom'),
            'map_auto_focus_border' => \SystemPref::Get('MapAutoFocusBorder'),
            'map_auto_cSS_file' => \SystemPref::Get("MapAutoCSSFile"),
            'map_provider_available_google_v3' => \SystemPref::Get('MapProviderAvailableGoogleV3') == '0' ? false : true,
            'map_provider_available_map_quest' => \SystemPref::Get('MapProviderAvailableMapQuest') == '0' ? false : true,
            'map_provider_available_oSM' => \SystemPref::Get('MapProviderAvailableOSM') == '0' ? false : true,
            'map_provider_default' => \SystemPref::Get('MapProviderDefault'),
            'geo_search_local_geonames' => \SystemPref::Get('GeoSearchLocalGeonames') == '0' ? false : true,
            'geo_search_mapquest_nominatim' => \SystemPref::Get('GeoSearchMapquestNominatim') == '0' ? false : true,
            'geo_search_preferred_language' => $geo_preferred_lang,
            'map_marker_directory' => \SystemPref::Get('MapMarkerDirectory'),
            'map_popup_width_min' => \SystemPref::Get('MapPopupWidthMin'),
            'map_popup_height_min' => \SystemPref::Get('MapPopupHeightMin'),
            'map_video_width_you_tube' => \SystemPref::Get('MapVideoWidthYouTube'),
            'map_video_height_you_tube' => \SystemPref::Get('MapVideoHeightYouTube'),
            'map_video_width_vimeo' => \SystemPref::Get('MapVideoWidthVimeo'),
            'map_video_height_vimeo' => \SystemPref::Get('MapVideoHeightVimeo'),
            'map_video_width_flash' => \SystemPref::Get('MapVideoWidthFlash'),
            'map_video_height_flash' => \SystemPref::Get('MapVideoHeightFlash'),
            'geo_flash_server' => \SystemPref::Get('FlashServer'),
            'geo_flash_directory' => \SystemPref::Get('FlashDirectory'),
            'facebook_appid' => \SystemPref::Get('facebook_appid'),
            'facebook_appsecret' => \SystemPref::Get('facebook_appsecret'),
            'mailchimp_apikey' => \SystemPref::Get('mailchimp_apikey'),
            'mailchimp_listid' => \SystemPref::Get('mailchimp_listid'),
        )
        , array());

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                if (!$currentUser->hasPermission('ChangeSystemPreferences')) {
                    camp_html_display_error(getGS("You do not have the right to change system preferences."));
                    exit;
                }

                $data = $form->getData();
                $geoLocation = array(
                    'map_display_resolution_default' => $data['map_display_resolution_default'],
                    'map_view_width_default' => $hasManagePermission ? $data['map_view_width_default'] : \SystemPref::Get('MapViewWidthDefault'),
                    'map_view_height_default' => $hasManagePermission ? $data['map_view_height_default'] : \SystemPref::Get('MapViewHeightDefault'),
                    'map_auto_cSS_file' => strip_tags($data['map_auto_cSS_file']),
                    'map_auto_focus_default' => $data['map_auto_focus_default'] ? '1' : '0',
                    'map_auto_focus_max_zoom' => $data['map_auto_focus_max_zoom'],
                    'map_auto_focus_border' => $data['map_auto_focus_border'],
                    'map_provider_available_google_v3' => $data['map_provider_available_google_v3'] ? '1' : '0',
                    'map_provider_available_map_quest' => $data['map_provider_available_map_quest'] ? '1' : '0',
                    'map_provider_available_oSM' => $data['map_provider_available_oSM'] ? '1' : '0',
                    'map_provider_default' => $data['map_provider_default'],
                    'geo_search_local_geonames' => $data['geo_search_local_geonames'] ? '1' : '0',
                    'geo_search_mapquest_nominatim' => $data['geo_search_mapquest_nominatim'] ? '1' : '0',
                    'geo_search_preferred_language' => $data['geo_search_preferred_language'],
                    'map_marker_directory' => $hasManagePermission ? strip_tags($data['map_marker_directory']) : \SystemPref::Get('MapMarkerDirectory'),
                    'map_marker_source_default' => strip_tags($data['map_marker_source_default']),
                    'map_popup_width_min' => $hasManagePermission ? $data['map_popup_width_min'] : \SystemPref::Get('MapPopupWidthMin'),
                    'map_popup_height_min' => $hasManagePermission ? $data['map_popup_height_min'] : \SystemPref::Get('MapPopupHeightMin'),
                    'map_video_width_you_tube' => $hasManagePermission ? $data['map_video_width_you_tube'] : \SystemPref::Get('MapVideoWidthYouTube'),
                    'map_video_height_you_tube' => $hasManagePermission ? $data['map_video_height_you_tube'] : \SystemPref::Get('MapVideoHeightYouTube'),
                    'map_video_width_vimeo' => $hasManagePermission ? $data['map_video_width_vimeo'] : \SystemPref::Get('MapVideoWidthVimeo'),
                    'map_video_height_vimeo' => $hasManagePermission ? $data['map_video_height_vimeo'] : \SystemPref::Get('MapVideoHeightVimeo'),
                    'map_video_width_flash' => $hasManagePermission ? $data['map_video_width_flash'] : \SystemPref::Get('MapVideoWidthFlash'),
                    'map_video_height_flash' => $hasManagePermission ? $data['map_video_height_flash'] : \SystemPref::Get('MapVideoHeightFlash'),
                    'flash_server' => $hasManagePermission ? strip_tags($data['geo_flash_server']) : \SystemPref::Get('FlashServer'),
                    'flash_directory' => $hasManagePermission ? strip_tags($data['geo_flash_directory']) : \SystemPref::Get('FlashDirectory'),
                );
                // Site title
                \SystemPref::Set('SiteTitle', strip_tags($data['title']));

                // Site Meta Keywords
                \SystemPref::Set('SiteMetaKeywords', strip_tags($data['meta_keywords']));

                // Site Meta Description
                \SystemPref::Set('SiteMetaDescription', strip_tags($data['meta_description']));

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
                                $translator->trans(
                                    'newscoop.preferences.error.cache',
                                    array('%cache%' => $data['cache_engine'])
                                )
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
                                $translator->trans(
                                    'newscoop.preferences.error.cache',
                                    array('%cache%' => $data['cache_template'])
                                )
                            );

                            return $this->redirect($this->generateUrl('newscoop_newscoop_systempref_index'));
                        }
                    } else {
                        \SystemPref::Set('TemplateCacheHandler', $data['cache_template']);
                    }

                    // Statistics collecting
                    \SystemPref::Set('CollectStatistics', $data['automatic_collection']);

                    // SMTP Host/Port
                    \SystemPref::Set('SMTPHost', strip_tags($data['smtp_host']));
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
                            \SystemPref::Set("DBReplicationHost", strip_tags($data['use_replication_host']));
                            \SystemPref::Set("DBReplicationUser", strip_tags($data['use_replication_user']));
                            \SystemPref::Set("DBReplicationPass", strip_tags($data['use_replication_password']));
                            \SystemPref::Set("UseDBReplication", $data['use_replication']);
                        } else {;
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                $translator->trans('newscoop.preferences.error.replication')
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
                    \SystemPref::Set("TemplateFilter", strip_tags($data['template_filter']));

                    // External cron management
                    if ($data['external_cron_management'] != 'Y' && $data['external_cron_management'] != 'N') {
                        $data['external_cron_management'] = SystemPref::Get('ExternalSubscriptionManagement');
                    }
                    if ($data['external_cron_management'] != 'Y' && $data['external_cron_management'] != 'N') {
                        $data['external_cron_management'] = SystemPref::Get('ExternalCronManagement');
                    }
                    if ($data['external_cron_management'] == 'N'
                            && !is_readable(CS_INSTALL_DIR.DIR_SEP.'cron_jobs'.DIR_SEP.'all_at_once')) {
                        $data['external_cron_management'] = 'Y';
                    }
                    
                    \SystemPref::Set('ExternalCronManagement', $data['external_cron_management']);
                }

                \SystemPref::Set('ImagecacheLifetime', $data['cache_image']);

                // Allow Password Recovery
                \SystemPref::Set('PasswordRecovery', $data['allow_recovery']);
                \SystemPref::Set('PasswordRecoveryFrom', $data['password_recovery_form']);

                // Secret key
                \SystemPref::Set('SiteSecretKey', strip_tags($data['secret_key']));

                // Session life time
                \SystemPref::Set('SiteSessionLifeTime', $data['session_lifetime']);

                // Keyword Separator
                \SystemPref::Set("KeywordSeparator", strip_tags($data['separator']));

                // Number of failed login attempts
                \SystemPref::Set("LoginFailedAttemptsNum", $data['captcha']);

                // Max Upload File Size
                $max_upload_filesize_bytes = camp_convert_bytes($data['max_upload_size']);
                if ($max_upload_filesize_bytes > 0 &&
                        $max_upload_filesize_bytes <= min(camp_convert_bytes(ini_get('post_max_size')), camp_convert_bytes(ini_get('upload_max_filesize')))) {
                    \SystemPref::Set("MaxUploadFileSize", strip_tags($data['max_upload_size']));
                } else {
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        $translator->trans('newscoop.preferences.error.maxupload')
                    );
                }
                
                if (strip_tags($data['mysql_client_command_path'])) {
                    \SystemPref::Set('MysqlClientCommandPath', strip_tags($data['mysql_client_command_path']));
                }
                
                if ($data['center_latitude_default'] > 90 || $data['center_latitude_default'] < -90 || 
                    $data['center_longitude_default'] > 180 || $data['center_longitude_default'] < -180) {
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        $translator->trans('newscoop.preferences.error.geolocation')
                    );
                } else {
                    \SystemPref::Set('MapCenterLatitudeDefault', $data['center_latitude_default']);
                    \SystemPref::Set('MapCenterLongitudeDefault', $data['center_longitude_default']);
                }

                // geolocation
                foreach ($geoLocation as $key => $value) {
                    $name = '';
                        foreach (explode('_', $key) as $part) {
                            $name .= ucfirst($part);
                        }

                        \SystemPref::Set($name, $value);
                }

                //Mailchimp
                \SystemPref::Set('mailchimp_apikey', strip_tags($data['mailchimp_apikey']));
                \SystemPref::Set('mailchimp_listid', strip_tags($data['mailchimp_listid']));

                //Facebook
                \SystemPref::Set('facebook_appid', strip_tags($data['facebook_appid']));
                \SystemPref::Set('facebook_appsecret', strip_tags($data['facebook_appsecret']));

                $this->get('session')->getFlashBag()->add(
                    'success',
                    $translator->trans('newscoop.preferences.success.saved')
                );
                return $this->redirect($this->generateUrl('newscoop_newscoop_systempref_index'));
            }
        }

        return array(
            'form' => $form->createView(),
            'php_ini_max_seconds' => $php_ini_max_seconds,
            'upload_min_filesize' => $upload_min_filesize,
            'hasManagePermission' => $hasManagePermission,
            'mysql_client_command_path' => $mysql_client_command_path,
            'map_marker_source_default' => $default_marker_source,
            'map_marker_source_selected' => \SystemPref::Get('MapMarkerSourceDefault'),
        );
    }
}