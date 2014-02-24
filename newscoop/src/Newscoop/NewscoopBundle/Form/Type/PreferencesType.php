<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PreferencesType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $timeZoneCities = array(
            0 => 'London, Lisbon, Casablanca',
            1 => 'Brussels, Copenhagen, Madrid, Paris',
            2 => 'Athens, Istanbul, Jerusalem',
            3 => 'Baghdad, Riyadh, Moscow, St. Petersburg',
            4 => 'Abu Dhabi, Muscat, Baku, Tbilisi',
            5 => 'Ekaterinburg, Islamabad, Karachi, Tashkent',
            6 => 'Almaty, Dhaka, Colombo',
            7 => 'Bangkok, Hanoi, Jakarta',
            8 => 'Beijing, Perth, Singapore, Hong Kong',
            9 => 'Tokyo, Seoul, Osaka, Sapporo, Yakutsk',
            10 => 'Eastern Australia, Guam, Vladivostok',
            11 => 'Magadan, Solomon Islands, New Caledonia',
            12 => 'Auckland, Wellington, Fiji, Kamchatka',
            -1 => 'Azores, Cape Verde Islands',
            -2 => 'Mid-Atlantic',
            -3 => 'Brazil, Buenos Aires, Georgetown',
            -4 => 'Atlantic Time (Canada), Caracas, La Paz',
            -5 => 'Eastern Time (US & Canada), Bogota, Lima',
            -6 => 'Central Time (US & Canada), Mexico City',
            -7 => 'Mountain Time (US & Canada)',
            -8 => 'Pacific Time (US & Canada)',
            -9 => 'Alaska',
            -10 => 'Hawaii',
            -11 => 'Midway Island, Samoa',
            -12 => 'Eniwetok, Kwajalein',
        );

        $timezones = array();
        for ($k = -12; $k < 13; $k++) {
            $v = $k < 0 ? $k : '+' . $k;
            if ($timeZoneCities[$k] != '') {
                $timezones[$v] = "GMT $v:00 ({$timeZoneCities[$k]})";
            } else {
                $timezones[$v] = "GMT $v:00";
            }
        }

        $availableCacheEngines = $options['cacheService']->getAvailableCacheEngines();
        $availableTemplateCacheHandlers = \CampTemplateCache::availableHandlers();
        $cacheEngines = array();
        $cacheTemplate = array();
        foreach ($availableCacheEngines as $cacheEngineName => $engineValue) {
            $cacheEngines[$engineValue] = $cacheEngineName;
        }

        foreach ($availableTemplateCacheHandlers as $handler => $value) {
            $cacheTemplate[$handler] = $handler;
        }

        $cacheLifetime = array();
        foreach (array(0 => 'newscoop.preferences.label.disabled',
                       30   => '30 Seconds',
                       60   => '1 Minute',
                       300  => '5 Minutes',
                       900  => '15 Minutes',
                       1800 => '30 Minutes',
                       3600 => '1 Hour',
                       3600*24 => '1 Day',
                       3600*24*2 => '2 Days',
                       3600*24*3 => '3 Days',
                       3600*24*4 => '4 Days',
                       3600*24*5 => '5 Days',
                       3600*24*6 => '6 Days',
                       3600*24*7 => '1 Week',
                       3600*24*14 => '2 Weeks',
                       3600*24*21 => '3 Weeks',
                       3600*24*31 => '1 Month',
                       3600*24*61 => '2 Months',
                       3600*24*91 => '3 Months',
                       3600*24*183 => '6 Months',
                       3600*24*365 => '1 Year',
                       -1          => 'Infinite') as $k => $v) {
            $cacheLifetime[$k] = $v;
        }

        $language_codes_639_1 = \Language::Get6391List();

        asort($language_codes_639_1);
        $languages = array();

        foreach ($language_codes_639_1 as $geo_lang_code => $geo_lang_name) {
            $languages[$geo_lang_code] = $geo_lang_name;
        }

        $builder
        ->add('siteonline', 'choice', array(
            'choices'   => array(
                'Y' => 'newscoop.preferences.label.yesoption', 
                'N' => 'newscoop.preferences.label.nooption'
            ),
            'error_bubbling' => true,
            'multiple' => false,
            'expanded' => true,
            'required' => true,
        ))
        ->add('title', null, array(
            'attr' => array('maxlength'=>'100', 'size' => '64'),
            'error_bubbling' => true,
            'required' => true
        ))
        ->add('meta_keywords', null, array(
            'attr' => array('maxlength'=>'100', 'size' => '64'),
            'error_bubbling' => true,
            'required' => true
        ))
        ->add('meta_description', null, array(
            'attr' => array('maxlength' => '100', 'size' => '64'),
            'error_bubbling' => true,
            'required' => true
        ))
        ->add('timezone', 'choice', array(
            'choices'   => $timezones,
            'empty_value' => 'newscoop.preferences.label.disabled',
            'required' => false
        ))
        ->add('cache_engine', 'choice', array(
            'choices'   => $cacheEngines,
            'empty_value' => 'Array',
            'required' => false
        ))
        ->add('cache_engine_host', 'text', array(
            'error_bubbling' => true,
            'required' => false
        ))
        ->add('cache_engine_port', 'text', array(
            'error_bubbling' => true,
            'required' => false
        ))
        ->add('cache_template', 'choice', array(
            'choices'   => $cacheTemplate,
            'empty_value' => 'newscoop.preferences.label.disabled',
            'required' => false
        ))
        ->add('cache_image', 'choice', array(
            'choices'   => $cacheLifetime,
            'required' => true
        ))
        ->add('allow_recovery', 'choice', array(
            'choices'   => array(
                'Y' => 'newscoop.preferences.label.yesoption', 
                'N' => 'newscoop.preferences.label.nooption'
            ),
            'error_bubbling' => true,
            'multiple' => false,
            'expanded' => true,
            'required' => true,
        ))
        ->add('secret_key', null, array(
            'attr' => array('maxlength' => '32', 'size' => '64'),
            'error_bubbling' => true,
            'required' => true
        ))
        ->add('session_lifetime', 'integer', array(
            'attr' => array('maxlength' => '4', 'max' => '9999', 'min' => 0),
            'error_bubbling' => true,
            'required' => true
        ))
        ->add('separator', null, array(
            'attr' => array('maxlength' => '2', 'size' => '5'),
            'error_bubbling' => true,
            'required' => true
        ))
        ->add('captcha', 'integer', array(
            'attr' => array('max' => 99, 'min' => 0),
            'error_bubbling' => true,
            'required' => true
        ))
        ->add('max_upload_size', null, array(
            'attr' => array('maxlength' => '12', 'size' => '5'),
            'error_bubbling' => true,
            'required' => true
        ))
        ->add('automatic_collection', 'choice', array(
            'choices'   => array(
                'Y' => 'newscoop.preferences.label.yesoption', 
                'N' => 'newscoop.preferences.label.nooption'
            ),
            'error_bubbling' => true,
            'multiple' => false,
            'expanded' => true,
            'required' => true,
        ))
        ->add('smtp_host', null, array(
            'attr' => array('maxlength' => 100, 'size' => 64),
            'error_bubbling' => true,
            'required' => true
        ))
        ->add('smtp_port', 'integer', array(
            'attr' => array('max' => 999999, 'min' => 1),
            'error_bubbling' => true,
            'required' => true
        ))
        ->add('email_from', 'email', array(
            'attr' => array('maxlength' => 100, 'size' => 64),
            'error_bubbling' => true,
            'required' => true
        ))
        ->add('image_ratio', 'integer', array(
            'attr' => array('max' => 100, 'min' => 1),
            'error_bubbling' => true,
            'required' => true
        ))
        ->add('image_width', 'integer', array(
            'attr' => array('max' => 999999, 'min' => 0),
            'error_bubbling' => true,
            'required' => false
        ))
        ->add('image_height', 'integer', array(
            'attr' => array('max' => 999999, 'min' => 0),
            'error_bubbling' => true,
            'required' => false
        ))
        ->add('zoom', 'choice', array(
            'choices'   => array(
                'Y' => 'newscoop.preferences.label.yesoption', 
                'N' => 'newscoop.preferences.label.nooption'
            ),
            'error_bubbling' => true,
            'multiple' => false,
            'expanded' => true,
            'required' => true,
        ))
        ->add('use_replication', 'choice', array(
            'choices'   => array(
                'Y' => 'newscoop.preferences.label.yesoption', 
                'N' => 'newscoop.preferences.label.nooption'
            ),
            'error_bubbling' => true,
            'multiple' => false,
            'expanded' => true,
            'required' => true,
        ))
        ->add('use_replication_host', null, array(
            'attr' => array('maxlength' => 60, 'size' => 30),
            'error_bubbling' => true,
            'required' => false
        ))
        ->add('use_replication_user', null, array(
            'attr' => array('maxlength' => 20, 'size' => 30),
            'error_bubbling' => true,
            'required' => false
        ))
        ->add('use_replication_password', null, array(
            'attr' => array('maxlength' => 20, 'size' => 30),
            'error_bubbling' => true,
            'required' => false
        ))
        ->add('use_replication_port', 'integer', array(
            'attr' => array('max' => 999999, 'min' => 0),
            'required' => false
        ))
        ->add('template_filter', 'text', array(
            'attr' => array('maxlength' => 50, 'size' => 30),
            'error_bubbling' => true,
            'required' => true
        ))
        ->add('mysql_client_command_path', 'text', array(
            'attr' => array('maxlength' => 200, 'size' => 40),
            'error_bubbling' => true,
            'required' => false
        ))
        ->add('center_latitude_default', 'number', array(
            'attr' => array('size' => 10),
            'error_bubbling' => true,
            'invalid_message' => 'newscoop.preferences.error.latitude',
            'precision' => 6,
            'required' => true
        ))
        ->add('center_longitude_default', 'number', array(
            'attr' => array('size' => 10),
            'error_bubbling' => true,
            'invalid_message' => 'newscoop.preferences.error.longitude',
            'precision' => 6,
            'required' => true
        ))
        ->add('map_display_resolution_default', 'integer', array(
            'attr' => array('max' => 99, 'min' => 0),
            'required' => true
        ))
        ->add('map_view_width_default', 'integer', array(
            'attr' => array('max' => 999, 'min' => 0),
            'required' => true
        ))
        ->add('map_view_height_default', 'integer', array(
            'attr' => array('max' => 999, 'min' => 0),
            'required' => true
        ))
        ->add('map_auto_cSS_file', null, array(
            'attr' => array('maxlength' => 80, 'size' => 50),
            'error_bubbling' => true,
            'required' => true
        ))
        ->add('map_auto_focus_default', 'checkbox', array(
            'required' => false
        ))
        ->add('map_auto_focus_max_zoom', 'integer', array(
            'attr' => array('max' => 18, 'min' => 0),
            'required' => true
        ))
        ->add('map_auto_focus_border', 'integer', array(
            'attr' => array('max' => 999, 'min' => -99),
            'required' => true
        ))
        ->add('map_provider_available_google_v3', 'checkbox', array(
            'required' => false
        ))
        ->add('map_provider_available_map_quest', 'checkbox', array(
            'required' => false
        ))
        ->add('map_provider_available_oSM', 'checkbox', array(
            'required' => false
        ))
        ->add('map_provider_default', 'choice', array(
            'choices'   => array(
                'GoogleV3' => 'Google Maps', 
                'MapQuest' => 'MapQuest Open',
                'OSM' => 'OpenStreetMap',
            ),
            'required' => true
        ))
        ->add('geo_search_local_geonames', 'checkbox', array(
            'required' => false
        ))
        ->add('geo_search_mapquest_nominatim', 'checkbox', array(
            'required' => false
        ))
        ->add('geo_search_preferred_language', 'choice', array(
            'choices'   => $languages,
            'required' => true
        ))
        ->add('map_marker_directory', null, array(
            'attr' => array('maxlength' => 80, 'size' => 50),
            'error_bubbling' => true,
            'required' => true
        ))
        ->add('map_marker_source_default', null, array(
            'required' => false
        ))
        ->add('map_popup_width_min', 'integer', array(
            'attr' => array('max' => 999, 'min' => 0),
            'required' => true
        ))
        ->add('map_popup_height_min', 'integer', array(
            'attr' => array('max' => 999, 'min' => 0),
            'required' => true
        ))
        ->add('map_video_width_you_tube', 'integer', array(
            'attr' => array('max' => 999, 'min' => 0),
            'required' => true
        ))
        ->add('map_video_height_you_tube', 'integer', array(
            'attr' => array('max' => 999, 'min' => 0),
            'required' => true
        ))
        ->add('map_video_width_vimeo', 'integer', array(
            'attr' => array('max' => 999, 'min' => 0),
            'required' => true
        ))
        ->add('map_video_height_vimeo', 'integer', array(
            'attr' => array('max' => 999, 'min' => 0),
            'required' => true
        ))
        ->add('map_video_width_flash', 'integer', array(
            'attr' => array('max' => 999, 'min' => 0),
            'required' => true
        ))
        ->add('map_video_height_flash', 'integer', array(
            'attr' => array('max' => 999, 'min' => 0),
            'required' => true
        ))
        ->add('geo_flash_server', null, array(
            'attr' => array('maxlength' => 80, 'size' => 40),
            'error_bubbling' => true,
            'required' => false
        ))
        ->add('geo_flash_directory', null, array(
            'attr' => array('maxlength' => 80, 'size' => 40),
            'error_bubbling' => true,
            'required' => false
        ))
        ->add('facebook_appid', null, array(
            'attr' => array('maxlength' => 200, 'size' => 40),
            'error_bubbling' => true,
            'required' => false
        ))
        ->add('facebook_appsecret', null, array(
            'attr' => array('maxlength' => 200, 'size' => 40),
            'error_bubbling' => true,
            'required' => false
        ))
        ->add('recaptchaPublicKey', null, array(
            'attr' => array('maxlength' => 200, 'size' => 40),
            'error_bubbling' => true,
            'required' => false
        ))
        ->add('recaptchaPrivateKey', null, array(
            'attr' => array('maxlength' => 200, 'size' => 40),
            'error_bubbling' => true,
            'required' => false
        ))
        ->add('recaptchaSecure', 'choice', array(
            'choices'   => array(
                'Y' => 'newscoop.preferences.label.yesoption',
                'N' => 'newscoop.preferences.label.nooption'
            ),
            'data' => 'N',
            'error_bubbling' => true,
            'multiple' => false,
            'expanded' => true,
            'required' => true,
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'system_pref'
        ));

        $resolver->setRequired(array(
            'cacheService',
        ));


    }

    public function getName()
    {
        return 'preferencesform';
    }
}
