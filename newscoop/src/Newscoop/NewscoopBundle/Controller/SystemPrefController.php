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
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        if(empty($max_upload_filesize) || $max_upload_filesize == 0 || $max_upload_filesize != ini_get('upload_max_filesize')) {
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
            'secret_key' => \SystemPref::Get('SiteSecretKey'),
            'session_lifetime' => $sp_session_lifetime,
            'separator' => \SystemPref::Get("KeywordSeparator"),
            'captcha' => \SystemPref::Get("LoginFailedAttemptsNum"),
            'max_upload_size' => \SystemPref::Get("MaxUploadFileSize"),
            'automatic_collection' => \SystemPref::Get('CollectStatistics'),
            'smtp_host' => \SystemPref::Get('SMTPHost'),
            'smtp_port' => \SystemPref::Get('SMTPPort'),
            'email_from' => \SystemPref::Get('EmailFromAddress'),
            'image_ratio' => \SystemPref::Get('EditorImageRatio'),
            'image_width' => (int)\SystemPref::Get('EditorImageResizeWidth'),     
            'image_height' => (int)\SystemPref::Get('EditorImageResizeHeight'),
            'zoom' => \SystemPref::Get('EditorImageZoom'),
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
                    camp_html_display_error($translator->trans('newscoop.preferences.error.noaccess'));
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
                // Max Upload File Size
                $uploadSettings = $this->maxUpload($data['max_upload_size'], $translator);

                if ($uploadSettings instanceof RedirectResponse) {
                    return $uploadSettings;
                }
                //geolocation
                $geolocationSettings = $this->geolocation($data['center_latitude_default'], $data['center_longitude_default'], $geoLocation, $translator);
                
                if ($geolocationSettings instanceof RedirectResponse) {
                    return $geolocationSettings;
                }

                if($hasManagePermission) {
                    // DB Caching
                    $databaseCacheSettings = $this->databaseCache($data['cache_engine'], $translator);

                    if ($databaseCacheSettings instanceof RedirectResponse) {
                        return $databaseCacheSettings;
                    }
                    // Template Caching
                    $templateCacheSettings = $this->templateCache($data['cache_template'], $translator);

                    if ($templateCacheSettings instanceof RedirectResponse) {
                        return $templateCacheSettings;
                    }

                    $replicationSettings = $this->useReplication($data['use_replication_user'], $data['use_replication_host'], $data['use_replication_password'], 
                        $data['use_replication'], $data['use_replication_port'], $translator);

                    if ($replicationSettings instanceof RedirectResponse) {
                        return $replicationSettings;
                    }
                    // Statistics collecting
                    $this->collectStats($data['automatic_collection']);
                    // SMTP Host/Port
                    $this->smtpConfiguration($data['smtp_host'], $data['smtp_port']);
                    // Image resizing for WYSIWYG editor
                    $this->imageResizing($data['image_ratio'], $data['image_width'], $data['image_height'], $data['zoom']);
                    // Replication
                    
                    // template filter
                    $this->templateFilter($data['template_filter']);
                    // External cron management
                    $this->cronManagement($data['external_cron_management']);
                }
                // General Settings
                $this->generalSettings($data['title'], $data['meta_keywords'], $data['meta_description'], $data['timezone'], $data['cache_image'], $data['allow_recovery'], $data['email_from'], 
                    $data['secret_key'], $data['session_lifetime'], $data['separator'], $data['captcha'], $data['mysql_client_command_path']);
                //Mailchimp
                $this->mailchimp($data['mailchimp_apikey'], $data['mailchimp_listid']);
                //Facebook
                $this->facebook($data['facebook_appid'], $data['facebook_appsecret']);
                $this->get('session')->getFlashBag()->add('success', $translator->trans('newscoop.preferences.success.saved'));

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

    /**
     * Sets database caching
     *
     * @param string                                   $cache_engine Values 1 or 0
     * @param Symfony\Component\Translation\Translator $translator   Translator
     *
     * @return void|RedirectResponse
     */
    private function databaseCache($cache_engine, $translator) {

        if (\SystemPref::Get('DBCacheEngine') != $cache_engine) {

            if (!$cache_engine || \CampCache::IsSupported($cache_engine)) {
                \SystemPref::Set('DBCacheEngine', $cache_engine);
                \CampCache::singleton()->clear('user');
                \CampCache::singleton()->clear();
            } else {
                $this->get('session')->getFlashBag()->add('error', $translator->trans('newscoop.preferences.error.cache',
                    array('%cache%' => $cache_engine)
                ));

                return $this->redirect($this->generateUrl('newscoop_newscoop_systempref_index'));
            }
        }
    }

    /**
     * Sets templates caching
     *
     * @param string                                   $cache_template Values 1 or 0
     * @param Symfony\Component\Translation\Translator $translator     Translator
     *
     * @return void|RedirectResponse
     */
    private function templateCache($cache_template, $translator) {
        if (\SystemPref::Get('TemplateCacheHandler') !=  $cache_template && $cache_template) {
            $handler = \CampTemplateCache::factory($cache_template);

            if ($handler && \CampTemplateCache::factory($cache_template)->isSupported()) {
                \SystemPref::Set('TemplateCacheHandler', $cache_template);
                \CampTemplateCache::factory($cache_template)->clean();
            } else {
                $this->get('session')->getFlashBag()->add('error', $translator->trans('newscoop.preferences.error.cache',
                    array('%cache%' => $cache_template)
                ));

                return $this->redirect($this->generateUrl('newscoop_newscoop_systempref_index'));
            }
        } else {
            \SystemPref::Set('TemplateCacheHandler', $cache_template);
        }
    }

    /**
     * Sets replication settings
     *
     * @param string                                   $user            Replication server user
     * @param string                                   $host            Replication server hostname
     * @param string                                   $user            Replication server password
     * @param string                                   $use_replication Defines if replication is on or off
     * @param int                                      $port            Replication server port
     * @param Symfony\Component\Translation\Translator $translator      Translator
     *
     * @return void|RedirectResponse
     */
    private function useReplication($user, $host, $password, $use_replication, $port, $translator) {
        if ($use_replication == 'Y') {
            // Database Replication Host, User and Password
            if (!empty($host) && !empty($user)) {
                \SystemPref::Set("DBReplicationHost", strip_tags($host));
                \SystemPref::Set("DBReplicationUser", strip_tags($user));
                \SystemPref::Set("DBReplicationPass", strip_tags($password));
                \SystemPref::Set("UseDBReplication", $use_replication);
            } else {
                $this->get('session')->getFlashBag()->add('error', $translator->trans('newscoop.preferences.error.replication'));

                return $this->redirect($this->generateUrl('newscoop_newscoop_systempref_index'));
            }
            // Database Replication Port
            if (empty($port) || !is_int($port)) {
                $port = 3306;
            }

            \SystemPref::Set("DBReplicationPort", $port);
        } else {
            \SystemPref::Set("UseDBReplication", 'N');
        }
    }

    /**
     * Sets scheduled tasks externally - options
     *
     * @param string $cron Values 1 or 0
     *
     * @return void
     */
    private function cronManagement($cron) {
        if ($cron != 'Y' && $cron != 'N') {
            $cron = \SystemPref::Get('ExternalCronManagement');
        }

        if ($cron == 'N' && !is_readable(CS_INSTALL_DIR.DIR_SEP.'cron_jobs'.DIR_SEP.'all_at_once')) {
            $cron = 'Y';
        }

        \SystemPref::Set('ExternalCronManagement', $cron);
    }

    /**
     * Defines and sets max upload file size
     *
     * @param string $max_size                                     Max upload file size
     * @param Symfony\Component\Translation\Translator $translator Translator
     *
     * @return void|RedirectResponse
     */
    private function maxUpload($max_size, $translator) {
        $max_upload_filesize_bytes = trim($max_size);

        if ($max_upload_filesize_bytes > 0 &&
            $max_upload_filesize_bytes <= min(trim(ini_get('post_max_size')), trim(ini_get('upload_max_filesize')))) {
            \SystemPref::Set("MaxUploadFileSize", strip_tags($max_size));
        } else {
            $this->get('session')->getFlashBag()->add('error', $translator->trans('newscoop.preferences.error.maxupload'));

            return $this->redirect($this->generateUrl('newscoop_newscoop_systempref_index'));
        }
    }

    /**
     * Sets geolocation options
     *
     * @param point                                    $latitude    Latitude
     * @param point                                    $longitude   Longitude
     * @param array                                    $geoLocation Geolocation data
     * @param Symfony\Component\Translation\Translator $translator  Translator
     *
     * @return void|RedirectResponse
     */
    private function geolocation($latitude, $longitude, $geoLocation, $translator) {
        if ($latitude > 90 || $latitude < -90 || 
            $longitude > 180 || $longitude < -180) {

            $this->get('session')->getFlashBag()->add('error', $translator->trans('newscoop.preferences.error.geolocation'));

            return $this->redirect($this->generateUrl('newscoop_newscoop_systempref_index'));
        } else {
            \SystemPref::Set('MapCenterLatitudeDefault', $latitude);
            \SystemPref::Set('MapCenterLongitudeDefault', $longitude);
        }

        foreach ($geoLocation as $key => $value) {
            $name = '';
            foreach (explode('_', $key) as $part) {
                $name .= ucfirst($part);
         
            \SystemPref::Set($name, $value);
            }
        }
    }

    /**
     * Sets mailchimp options
     *
     * @param string $apiKey Mailchimp API key
     * @param string $listId Mailchimp List ID
     *
     * @return void
     */
    private function mailchimp($apiKey, $listId) {
        \SystemPref::Set('mailchimp_apikey', strip_tags($apiKey));
        \SystemPref::Set('mailchimp_listid', strip_tags($listId));            
    }

    /**
     * Sets facebook options
     *
     * @param string $appId  Facebook application ID
     * @param string $secret Facebook Secret key
     *
     * @return void
     */
    private function facebook($appId, $secret) {
        \SystemPref::Set('facebook_appid', strip_tags($appId));
        \SystemPref::Set('facebook_appsecret', strip_tags($secret));
    }

    /**
     * Sets automatic statistics collection options
     *
     * @param string $automatic_collection Values 1 or 0
     *
     * @return void
     */
    private function collectStats($automatic_collection){
        \SystemPref::Set('CollectStatistics', $automatic_collection);
    }

    /**
     * Sets SMTP options
     *
     * @param string $host      SMTP host
     * @param int    $port      SMTP port
     *
     * @return void
     */
    private function smtpConfiguration($host, $port) {
        \SystemPref::Set('SMTPHost', strip_tags($host));
        \SystemPref::Set('SMTPPort', $port);
    }

    /**
     * Sets images resizing options
     *
     * @param string $ratio        Image ratio
     * @param int    $image_width  Image width
     * @param int    $image_height Image height
     * @param int    $zoom         Image zoom
     *
     * @return void
     */
    private function imageResizing($ratio, $image_width, $image_height, $zoom) {
        \SystemPref::Set('EditorImageRatio', $ratio);
        \SystemPref::Set('EditorImageResizeWidth', $image_width);
        \SystemPref::Set('EditorImageResizeHeight', $image_height);
        \SystemPref::Set('EditorImageZoom', $zoom);
    }

    /**
     * Sets template filter
     *
     * @param string $template_filter Template filter
     *
     * @return void
     */
    private function templateFilter($template_filter) {
        \SystemPref::Set("TemplateFilter", strip_tags($template_filter));
    }

    /**
     * Sets general options
     *
     * @param string $title                     Website title
     * @param string $meta_keywords             Website meta keywords
     * @param string $meta_description          Website meta description
     * @param string $timezone                  Website timezone
     * @param int    $cache_image               Image cache lifetime
     * @param string $allow_recovery            Password recovery
     * @param string $emailFrom                 Email address for system notifications
     * @param string $secret_key                Newscoop secret key
     * @param int    $session_lifetime          Session lifetime
     * @param string $separator                 Keyword separator
     * @param int    $captcha                   Number of failed login attempts before showing CAPTCHA
     * @param string $mysql_client_command_path MySQL client command path
     *
     * @return void
     */
    private function generalSettings($title, $meta_keywords, $meta_description, $timezone, $cache_image, $allow_recovery, 
        $emailFrom, $secret_key, $session_lifetime, $separator, $captcha, $mysql_client_command_path) {
        \SystemPref::Set('SiteTitle', strip_tags($title));
        \SystemPref::Set('SiteMetaKeywords', strip_tags($meta_keywords));
        \SystemPref::Set('SiteMetaDescription', strip_tags($meta_description));
        \SystemPref::Set('TimeZone', (string)$timezone);
        \SystemPref::Set('ImagecacheLifetime', $cache_image);
        \SystemPref::Set('PasswordRecovery', $allow_recovery);
        \SystemPref::Set('EmailFromAddress', $emailFrom);
        \SystemPref::Set('SiteSecretKey', strip_tags($secret_key));
        \SystemPref::Set('SiteSessionLifeTime', $session_lifetime);
        \SystemPref::Set("KeywordSeparator", strip_tags($separator));
        \SystemPref::Set("LoginFailedAttemptsNum", $captcha);
        if (strip_tags($mysql_client_command_path)) {
            \SystemPref::Set('MysqlClientCommandPath', strip_tags($mysql_client_command_path));
        }
    }            
}