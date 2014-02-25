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
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $preferencesService = $this->container->get('system_preferences_service');

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

        if (\SaaS::singleton()->hasPermission('ManageSystemPreferences')) {
            $hasManagePermission = true;
        }

        $max_upload_filesize = $preferencesService->MaxUploadFileSize;
        if (empty($max_upload_filesize) || $max_upload_filesize == 0 || $max_upload_filesize != ini_get('upload_max_filesize')) {
            $preferencesService->MaxUploadFileSize = ini_get('upload_max_filesize');
        }

        $currentUser = $this->get('user')->getCurrentUser();
        $translator = $this->get('translator');

        $sp_session_lifetime = 0 + $preferencesService->SiteSessionLifeTime;
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
        $mysql_client_command_path = $preferencesService->MysqlClientCommandPath;

        if (!$locations || !$cities) {
            $mysql_client_command_path_def = '/usr/bin/mysql';
            if (empty($mysql_client_command_path) && file_exists($mysql_client_command_path_def)) {
                $mysql_client_command_path = $mysql_client_command_path_def;
            }
        }

        $geo_preferred_lang = $preferencesService->GeoSearchPreferredLanguage;
        if (empty($geo_preferred_lang)) {
            $geo_preferred_lang = 'en';
        }

        $default_marker_source = $preferencesService->MapMarkerSourceDefault;

        $marker_icons = \Geo_Preferences::GetIconsFiles();
        if (0 < count($marker_icons)) {
            $default_marker_source = array();
            foreach ($marker_icons as $one_icon) {
                $default_marker_source[$one_icon] = $one_icon;
            }
        }

        $form = $this->container->get('form.factory')->create(new PreferencesType(), array(
            'siteonline' => $preferencesService->SiteOnline,
            'title' => $preferencesService->SiteTitle,
            'meta_keywords' => $preferencesService->SiteMetaKeywords,
            'meta_description' => $preferencesService->SiteMetaDescription,
            'timezone' => $preferencesService->TimeZone,
            'cache_engine' => $preferencesService->DBCacheEngine,
            'cache_engine_host' => $preferencesService->DBCacheEngineHost,
            'cache_engine_port' => $preferencesService->DBCacheEnginePort,
            'cache_template' => $preferencesService->TemplateCacheHandler,
            'cache_image' => $preferencesService->ImagecacheLifetime,
            'allow_recovery' => $preferencesService->PasswordRecovery,
            'secret_key' => $preferencesService->SiteSecretKey,
            'session_lifetime' => $sp_session_lifetime,
            'separator' => $preferencesService->KeywordSeparator,
            'captcha' => $preferencesService->LoginFailedAttemptsNum,
            'max_upload_size' => $preferencesService->MaxUploadFileSize,
            'automatic_collection' => $preferencesService->CollectStatistics,
            'smtp_host' => $preferencesService->SMTPHost,
            'smtp_port' => $preferencesService->SMTPPort,
            'email_from' => $preferencesService->EmailFromAddress,
            'image_ratio' => $preferencesService->EditorImageRatio,
            'image_width' => (int)$preferencesService->EditorImageResizeWidth,
            'image_height' => (int)$preferencesService->EditorImageResizeHeight,
            'zoom' => $preferencesService->EditorImageZoom,
            'use_replication_host' => $preferencesService->DBReplicationHost,
            'use_replication_user' => $preferencesService->DBReplicationUser,
            'use_replication_password' => $preferencesService->DBReplicationPass,
            'use_replication' => $preferencesService->UseDBReplication,
            'use_replication_port' => $preferencesService->DBReplicationPort,
            'template_filter' => $preferencesService->TemplateFilter,
            'mysql_client_command_path' => $mysql_client_command_path,
            'center_latitude_default' => (float)$preferencesService->MapCenterLatitudeDefault,
            'center_longitude_default' => (float)$preferencesService->MapCenterLongitudeDefault,
            'map_display_resolution_default' => (int)$preferencesService->MapDisplayResolutionDefault,
            'map_view_width_default' => $preferencesService->MapViewWidthDefault,
            'map_view_height_default' => $preferencesService->MapViewHeightDefault,
            'map_auto_focus_default' => $preferencesService->MapAutoFocusDefault == '0' ? false : true,
            'map_auto_focus_max_zoom' => $preferencesService->MapAutoFocusMaxZoom,
            'map_auto_focus_border' => $preferencesService->MapAutoFocusBorder,
            'map_auto_cSS_file' => $preferencesService->MapAutoCSSFile,
            'map_provider_available_google_v3' => $preferencesService->MapProviderAvailableGoogleV3 == '0' ? false : true,
            'map_provider_available_map_quest' => $preferencesService->MapProviderAvailableMapQuest == '0' ? false : true,
            'map_provider_available_oSM' => $preferencesService->MapProviderAvailableOSM == '0' ? false : true,
            'map_provider_default' => $preferencesService->MapProviderDefault,
            'geo_search_local_geonames' => $preferencesService->GeoSearchLocalGeonames == '0' ? false : true,
            'geo_search_mapquest_nominatim' => $preferencesService->GeoSearchMapquestNominatim == '0' ? false : true,
            'geo_search_preferred_language' => $geo_preferred_lang,
            'map_marker_directory' => $preferencesService->MapMarkerDirectory,
            'map_popup_width_min' => $preferencesService->MapPopupWidthMin,
            'map_popup_height_min' => $preferencesService->MapPopupHeightMin,
            'map_video_width_you_tube' => $preferencesService->MapVideoWidthYouTube,
            'map_video_height_you_tube' => $preferencesService->MapVideoHeightYouTube,
            'map_video_width_vimeo' => $preferencesService->MapVideoWidthVimeo,
            'map_video_height_vimeo' => $preferencesService->MapVideoHeightVimeo,
            'map_video_width_flash' => $preferencesService->MapVideoWidthFlash,
            'map_video_height_flash' => $preferencesService->MapVideoHeightFlash,
            'geo_flash_server' => $preferencesService->FlashServer,
            'geo_flash_directory' => $preferencesService->FlashDirectory,
            'facebook_appid' => $preferencesService->facebook_appid,
            'facebook_appsecret' => $preferencesService->facebook_appsecret,
            'recaptchaPublicKey' => $preferencesService->RecaptchaPublicKey,
            'recaptchaPrivateKey' => $preferencesService->RecaptchaPrivateKey,
            'recaptchaSecure' => $preferencesService->RecaptchaSecure,
        )
        , array(
            'cacheService' => $this->container->get('newscoop.cache')
        ));

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                if (!$currentUser->hasPermission('ChangeSystemPreferences')) {
                    camp_html_display_error($translator->trans('newscoop.preferences.error.noaccess', array(), 'system_pref'));
                    exit;
                }

                $data = $form->getData();
                $geoLocation = array(
                    'map_display_resolution_default' => $data['map_display_resolution_default'],
                    'map_view_width_default' => $hasManagePermission ? $data['map_view_width_default'] : $preferencesService->MapViewWidthDefault,
                    'map_view_height_default' => $hasManagePermission ? $data['map_view_height_default'] : $preferencesService->MapViewHeightDefault,
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
                    'map_marker_directory' => $hasManagePermission ? strip_tags($data['map_marker_directory']) : $preferencesService->MapMarkerDirectory,
                    'map_marker_source_default' => strip_tags($data['map_marker_source_default']),
                    'map_popup_width_min' => $hasManagePermission ? $data['map_popup_width_min'] : $preferencesService->MapPopupWidthMin,
                    'map_popup_height_min' => $hasManagePermission ? $data['map_popup_height_min'] : $preferencesService->MapPopupHeightMin,
                    'map_video_width_you_tube' => $hasManagePermission ? $data['map_video_width_you_tube'] : $preferencesService->MapVideoWidthYouTube,
                    'map_video_height_you_tube' => $hasManagePermission ? $data['map_video_height_you_tube'] : $preferencesService->MapVideoHeightYouTube,
                    'map_video_width_vimeo' => $hasManagePermission ? $data['map_video_width_vimeo'] : $preferencesService->MapVideoWidthVimeo,
                    'map_video_height_vimeo' => $hasManagePermission ? $data['map_video_height_vimeo'] : $preferencesService->MapVideoHeightVimeo,
                    'map_video_width_flash' => $hasManagePermission ? $data['map_video_width_flash'] : $preferencesService->MapVideoWidthFlash,
                    'map_video_height_flash' => $hasManagePermission ? $data['map_video_height_flash'] : $preferencesService->MapVideoHeightFlash,
                    'flash_server' => $hasManagePermission ? strip_tags($data['geo_flash_server']) : $preferencesService->FlashServer,
                    'flash_directory' => $hasManagePermission ? strip_tags($data['geo_flash_directory']) : $preferencesService->FlashDirectory,
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
                    $databaseCacheSettings = $this->databaseCache($data['cache_engine'], $data['cache_engine_host'], $data['cache_engine_port']);

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
                    // template filter
                    $this->templateFilter($data['template_filter']);
                }
                // General Settings
                $this->generalSettings($data['siteonline'], $data['title'], $data['meta_keywords'], $data['meta_description'], $data['timezone'], $data['cache_image'], $data['allow_recovery'], $data['email_from'], 
                    $data['secret_key'], $data['session_lifetime'], $data['separator'], $data['captcha'], $data['mysql_client_command_path']);
                //Facebook
                $this->facebook($data['facebook_appid'], $data['facebook_appsecret']);
                //ReCaptcha
                $this->recaptcha($data['recaptchaPublicKey'], $data['recaptchaPrivateKey'], $data['recaptchaSecure']);
                $this->get('session')->getFlashBag()->add('success', $translator->trans('newscoop.preferences.success.saved', array(), 'system_pref'));

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
            'map_marker_source_selected' => $preferencesService->MapMarkerSourceDefault,
        );
    }

    /**
     * @Route("/admin/preferences/cache/clear", options={"expose"=true})
     * @Template()
     */
    public function clearDatabaseCacheAction(Request $request)
    {
        $cacheService = $this->get('newscoop.cache');
        $cacheDriver = $cacheService->getCacheDriver();

        try {
            $cacheDriver->deleteAll();
        } catch (Exception $e) {
            return new JsonResponse(array($e->getMessage()), 404);
        }

        return new JsonResponse(array('status' => 'success'), 200);
    }

    /**
     * Sets database caching
     *
     * @param string $cache_engine
     * @param string $cache_engine_host
     * @param string $cache_engine_port
     *
     * @return void|RedirectResponse
     */
    private function databaseCache($cache_engine, $cache_engine_host, $cache_engine_port) {
        $preferencesService = $this->container->get('system_preferences_service');
        $preferencesService->set('DBCacheEngine', $cache_engine);
        $preferencesService->set('DBCacheEngineHost', $cache_engine_host);
        $preferencesService->set('DBCacheEnginePort', $cache_engine_port);
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
        $preferencesService = $this->container->get('system_preferences_service');

        if ($preferencesService->TemplateCacheHandler !=  $cache_template && $cache_template) {
            $handler = \CampTemplateCache::factory($cache_template);

            if ($handler && \CampTemplateCache::factory($cache_template)->isSupported()) {
                $preferencesService->TemplateCacheHandler = $cache_template;
                \CampTemplateCache::factory($cache_template)->clean();
            } else {
                $this->get('session')->getFlashBag()->add('error', $translator->trans('newscoop.preferences.error.cache',
                    array('%cache%' => $cache_template), 'system_pref'
                ));

                return $this->redirect($this->generateUrl('newscoop_newscoop_systempref_index'));
            }
        } else {
            $preferencesService->TemplateCacheHandler = $cache_template;
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
        $preferencesService = $this->container->get('system_preferences_service');

        if ($use_replication == 'Y') {
            // Database Replication Host, User and Password
            if (!empty($host) && !empty($user)) {
                $preferencesService->DBReplicationHost = strip_tags($host);
                $preferencesService->DBReplicationUser = strip_tags($user);
                $preferencesService->DBReplicationPass = strip_tags($password);
                $preferencesService->UseDBReplication = $use_replication;
            } else {
                $this->get('session')->getFlashBag()->add('error', $translator->trans('newscoop.preferences.error.replication', array(), 'system_pref'));

                return $this->redirect($this->generateUrl('newscoop_newscoop_systempref_index'));
            }
            // Database Replication Port
            if (empty($port) || !is_int($port)) {
                $port = 3306;
            }

            $preferencesService->DBReplicationPort = $port;
        } else {
            $preferencesService->UseDBReplication = 'N';
        }
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
        $preferencesService = $this->container->get('system_preferences_service');

        if ($max_upload_filesize_bytes > 0 &&
            $max_upload_filesize_bytes <= min(trim(ini_get('post_max_size')), trim(ini_get('upload_max_filesize')))) {
            $preferencesService->MaxUploadFileSize = strip_tags($max_size);
        } else {
            $this->get('session')->getFlashBag()->add('error', $translator->trans('newscoop.preferences.error.maxupload', array(), 'system_pref'));

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

        $preferencesService = $this->container->get('system_preferences_service');

        if ($latitude > 90 || $latitude < -90 || 
            $longitude > 180 || $longitude < -180) {

            $this->get('session')->getFlashBag()->add('error', $translator->trans('newscoop.preferences.error.geolocation', array(), 'system_pref'));

            return $this->redirect($this->generateUrl('newscoop_newscoop_systempref_index'));
        } else {
            $preferencesService->MapCenterLatitudeDefault = $latitude;
            $preferencesService->MapCenterLongitudeDefault = $longitude;
        }

        foreach ($geoLocation as $key => $value) {
            $name = '';
            foreach (explode('_', $key) as $part) {
                $name .= ucfirst($part);
                $preferencesService->$name = $value;
            }
        }
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
        $preferencesService = $this->container->get('system_preferences_service');
        $preferencesService->facebook_appid = strip_tags($appId);
        $preferencesService->facebook_appsecret = strip_tags($secret);
    }

    /**
     * Sets recaptcha options
     *
     * @param string $publicKey  ReCaptcha public key
     * @param string $privateKey ReCaptcha private key
     * @param string $secure     Secure ReCaptcha
     *
     * @return void
     */
    private function recaptcha($publicKey, $privateKey, $secure) {
        $preferencesService = $this->container->get('system_preferences_service');
        $preferencesService->RecaptchaPublicKey = strip_tags($publicKey);
        $preferencesService->RecaptchaPrivateKey = strip_tags($privateKey);
        $preferencesService->RecaptchaSecure = strip_tags($secure);
    }

    /**
     * Sets automatic statistics collection options
     *
     * @param string $automatic_collection Values Y or N
     *
     * @return void
     */
    private function collectStats($automatic_collection){
        $preferencesService = $this->container->get('system_preferences_service');
        $preferencesService->CollectStatistics = $automatic_collection;
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
        $preferencesService = $this->container->get('system_preferences_service');
        $preferencesService->SMTPHost = strip_tags($host);
        $preferencesService->SMTPPort = $port;
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
        $preferencesService = $this->container->get('system_preferences_service');
        $preferencesService->EditorImageRatio = $ratio;
        $preferencesService->EditorImageResizeWidth = $image_width;
        $preferencesService->EditorImageResizeHeight = $image_height;
        $preferencesService->EditorImageZoom = $zoom;
    }

    /**
     * Sets template filter
     *
     * @param string $template_filter Template filter
     *
     * @return void
     */
    private function templateFilter($template_filter) {
        $preferencesService = $this->container->get('system_preferences_service');
        $preferencesService->TemplateFilter = strip_tags($template_filter);
    }

    /**
     * Sets general options
     *
     * @param string $siteOnline                Website status
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
    private function generalSettings($siteOnline, $title, $meta_keywords, $meta_description, $timezone, $cache_image, $allow_recovery, 
        $emailFrom, $secret_key, $session_lifetime, $separator, $captcha, $mysql_client_command_path) {

        $preferencesService = $this->container->get('system_preferences_service');
        $preferencesService->SiteOnline = strip_tags($siteOnline);
        $preferencesService->SiteTitle = strip_tags($title);
        $preferencesService->SiteMetaKeywords = strip_tags($meta_keywords);
        $preferencesService->SiteMetaDescription = strip_tags($meta_description);
        $preferencesService->TimeZone = (string)$timezone;
        $preferencesService->ImagecacheLifetime = $cache_image;
        $preferencesService->PasswordRecovery = $allow_recovery;
        $preferencesService->EmailFromAddress = $emailFrom;
        $preferencesService->SiteSecretKey = strip_tags($secret_key);
        $preferencesService->SiteSessionLifeTime = $session_lifetime;
        $preferencesService->KeywordSeparator = strip_tags($separator);
        $preferencesService->LoginFailedAttemptsNum = $captcha;
        if (strip_tags($mysql_client_command_path)) {
            $preferencesService->MysqlClientCommandPath = strip_tags($mysql_client_command_path);
        }
    }
}
