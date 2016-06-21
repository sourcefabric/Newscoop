<?php

/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PreferencesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $timeZones = array();
        $timezoneIdentifiers = timezone_identifiers_list();
        array_walk($timezoneIdentifiers, function ($timeZone) use (&$timeZones) {
            $timeZoneGroup = (strpos($timeZone, '/') !== false) ? substr($timeZone, 0, strpos($timeZone, '/')) : $timeZone;
            $value = (strpos($timeZone, '/') !== false) ? substr($timeZone, strpos($timeZone, '/') + 1) : $timeZone;
            $value = str_replace('_', ' ', $value);
            $value = str_replace('/', ' - ', $value);
            $timeZones[$timeZoneGroup][$timeZone] = $value;
        });

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
                       30 => '30 Seconds',
                       60 => '1 Minute',
                       300 => '5 Minutes',
                       900 => '15 Minutes',
                       1800 => '30 Minutes',
                       3600 => '1 Hour',
                       3600 * 24 => '1 Day',
                       3600 * 24 * 2 => '2 Days',
                       3600 * 24 * 3 => '3 Days',
                       3600 * 24 * 4 => '4 Days',
                       3600 * 24 * 5 => '5 Days',
                       3600 * 24 * 6 => '6 Days',
                       3600 * 24 * 7 => '1 Week',
                       3600 * 24 * 14 => '2 Weeks',
                       3600 * 24 * 21 => '3 Weeks',
                       3600 * 24 * 31 => '1 Month',
                       3600 * 24 * 61 => '2 Months',
                       3600 * 24 * 91 => '3 Months',
                       3600 * 24 * 183 => '6 Months',
                       3600 * 24 * 365 => '1 Year',
                       -1 => 'Infinite', ) as $k => $v) {
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
            'choices' => array(
                'Y' => 'newscoop.preferences.label.yesoption',
                'N' => 'newscoop.preferences.label.nooption',
            ),
            'multiple' => false,
            'expanded' => true,
            'constraints' => array(
                new Assert\NotBlank(),
            ),
        ))
        ->add('title', null, array(
            'attr' => array('size' => '64'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'string')),
                new Assert\Length(array(
                    'max' => 100,
                )),
            ),
        ))
        ->add('meta_keywords', null, array(
            'attr' => array('size' => '64'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'string')),
                new Assert\Length(array(
                    'max' => 100,
                )),
            ),
        ))
        ->add('meta_description', null, array(
            'attr' => array('size' => '64'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'string')),
                new Assert\Length(array(
                    'max' => 100,
                )),
            ),
        ))
        ->add('timezone', 'choice', array(
            'choices' => $timeZones,
            'empty_value' => 'newscoop.preferences.label.disabled',
            'required' => false,
        ))
        ->add('cache_engine', 'choice', array(
            'choices' => $cacheEngines,
            'empty_value' => 'Array',
            'required' => false,
        ))
        ->add('cache_engine_host', 'text')
        ->add('cache_engine_port', 'text')
        ->add('cache_template', 'choice', array(
            'choices' => $cacheTemplate,
            'empty_value' => 'newscoop.preferences.label.disabled',
            'required' => false,
        ))
        ->add('cache_image', 'choice', array(
            'choices' => $cacheLifetime,
            'constraints' => array(
                new Assert\NotBlank(),
            ),
        ))
        ->add('allow_recovery', 'choice', array(
            'choices' => array(
                'Y' => 'newscoop.preferences.label.yesoption',
                'N' => 'newscoop.preferences.label.nooption',
            ),
            'multiple' => false,
            'expanded' => true,
            'constraints' => array(
                new Assert\NotBlank(),
            ),
        ))
        ->add('secret_key', null, array(
            'attr' => array('size' => '64'),
            'constraints' => array(
                new Assert\Length(array(
                    'max' => 32,
                )),
            ),
        ))
        ->add('session_lifetime', 'integer', array(
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'integer')),
                new Assert\Range(array(
                    'min' => 0,
                    'max' => 86400,
                )),
            ),
        ))
        ->add('separator', null, array(
            'attr' => array('size' => '5'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'string')),
                new Assert\Length(array(
                    'max' => 2,
                )),
            ),
        ))
        ->add('captcha', 'integer', array(
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'integer')),
                new Assert\Range(array(
                    'min' => 0,
                    'max' => 99,
                )),
            ),
        ))
        ->add('max_upload_size', null, array(
            'attr' => array('size' => '5'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'string')),
                new Assert\Length(array(
                    'max' => 12,
                )),
            ),
        ))
        ->add('automatic_collection', 'choice', array(
            'choices' => array(
                'Y' => 'newscoop.preferences.label.yesoption',
                'N' => 'newscoop.preferences.label.nooption',
            ),
            'multiple' => false,
            'expanded' => true,
            'constraints' => array(
                new Assert\NotBlank(),
            ),
        ))
        ->add('smtp_host', null, array(
            'attr' => array('size' => 64),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'string')),
                new Assert\Length(array(
                    'max' => 100,
                )),
            ),
        ))
        ->add('smtp_port', 'integer', array(
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'integer')),
                new Assert\Range(array(
                    'min' => 1,
                    'max' => 999999,
                )),
            ),
        ))
        ->add('email_from', 'email', array(
            'attr' => array('size' => 64),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Email(),
                new Assert\Type(array('type' => 'string')),
                new Assert\Length(array(
                    'max' => 100,
                )),
            ),
        ))
        ->add('image_ratio', 'integer', array(
            'attr' => array('max' => 100, 'min' => 1),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'integer')),
                new Assert\Range(array(
                    'min' => 1,
                    'max' => 100,
                )),
            ),
        ))
        ->add('image_width', 'integer', array(
            'constraints' => array(
                new Assert\Range(array(
                    'min' => 0,
                    'max' => 999999,
                )),
            ),
        ))
        ->add('image_height', 'integer', array(
            'constraints' => array(
                new Assert\Range(array(
                    'min' => 0,
                    'max' => 999999,
                )),
            ),
        ))
        ->add('zoom', 'choice', array(
            'choices' => array(
                'Y' => 'newscoop.preferences.label.yesoption',
                'N' => 'newscoop.preferences.label.nooption',
            ),
            'multiple' => false,
            'expanded' => true,
            'constraints' => array(
                new Assert\NotBlank(),
            ),
        ))
        ->add('mediaRichTextCaptions', 'choice', array(
            'choices' => array(
                'Y' => 'newscoop.preferences.label.yesoption',
                'N' => 'newscoop.preferences.label.nooption',
            ),
            'multiple' => false,
            'expanded' => true,
            'constraints' => array(
                new Assert\NotBlank(),
            ),
        ))
         ->add('mediaCaptionLength', 'integer', array(
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'integer')),
                new Assert\Range(array(
                    'min' => 0,
                    'max' => 999999,
                )),
            ),
        ))
        ->add('use_replication', 'choice', array(
            'choices' => array(
                'Y' => 'newscoop.preferences.label.yesoption',
                'N' => 'newscoop.preferences.label.nooption',
            ),
            'multiple' => false,
            'expanded' => true,
            'constraints' => array(
                new Assert\NotBlank(),
            ),
        ))
        ->add('use_replication_host', null, array(
            'attr' => array('size' => 30),
            'constraints' => array(
                new Assert\Length(array(
                    'max' => 60,
                )),
            ),
        ))
        ->add('use_replication_user', null, array(
            'attr' => array('size' => 30),
            'constraints' => array(
                new Assert\Length(array(
                    'max' => 20,
                )),
            ),
        ))
        ->add('use_replication_password', null, array(
            'attr' => array('size' => 30),
            'constraints' => array(
                new Assert\Length(array(
                    'max' => 20,
                )),
            ),
        ))
        ->add('use_replication_port', 'integer', array(
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'integer')),
                new Assert\Range(array(
                    'min' => 0,
                    'max' => 999999,
                )),
            ),
        ))
        ->add('template_filter', 'text', array(
            'attr' => array('size' => 30),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'string')),
                new Assert\Length(array(
                    'max' => 50,
                )),
            ),
        ))
        ->add('mysql_client_command_path', 'text', array(
            'attr' => array('size' => 40),
            'constraints' => array(
                new Assert\Length(array(
                    'max' => 200,
                )),
            ),
        ))
        ->add('center_latitude_default', 'number', array(
            'attr' => array('size' => 10),
            'precision' => 6,
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float')),
            ),
        ))
        ->add('center_longitude_default', 'number', array(
            'attr' => array('size' => 10),
            'precision' => 6,
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float')),
            ),
        ))
        ->add('map_display_resolution_default', 'integer', array(
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'integer')),
                new Assert\Range(array(
                    'min' => 0,
                    'max' => 99,
                )),
            ),
        ))
        ->add('map_view_width_default', 'integer', array(
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'integer')),
                new Assert\Range(array(
                    'min' => 0,
                    'max' => 999,
                )),
            ),
        ))
        ->add('map_view_height_default', 'integer', array(
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'integer')),
                new Assert\Range(array(
                    'min' => 0,
                    'max' => 999,
                )),
            ),
        ))
        ->add('map_auto_cSS_file', null, array(
            'attr' => array('size' => 50),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'string')),
                new Assert\Length(array(
                    'max' => 80,
                )),
            ),
        ))
        ->add('map_auto_focus_default', 'checkbox')
        ->add('map_auto_focus_max_zoom', 'integer', array(
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'integer')),
                new Assert\Range(array(
                    'min' => 0,
                    'max' => 18,
                )),
            ),
        ))
        ->add('map_auto_focus_border', 'integer', array(
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'integer')),
                new Assert\Range(array(
                    'min' => -99,
                    'max' => 999,
                )),
            ),
        ))
        ->add('map_provider_available_google_v3', 'checkbox')
        ->add('map_provider_available_map_quest', 'checkbox')
        ->add('map_provider_available_oSM', 'checkbox')
        ->add('map_provider_default', 'choice', array(
            'choices' => array(
                'GoogleV3' => 'Google Maps',
                'MapQuest' => 'MapQuest Open',
                'OSM' => 'OpenStreetMap',
            ),
            'constraints' => array(
                new Assert\NotBlank(),
            ),
        ))
        ->add('geo_search_local_geonames', 'checkbox', array(
            'required' => false,
        ))
        ->add('geo_search_mapquest_nominatim', 'checkbox', array(
            'required' => false,
        ))
        ->add('geo_search_preferred_language', 'choice', array(
            'choices' => $languages,
            'constraints' => array(
                new Assert\NotBlank(),
            ),
        ))
        ->add('map_marker_directory', null, array(
            'attr' => array('size' => 50),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'string')),
                new Assert\Length(array(
                    'max' => 80,
                )),
            ),
        ))
        ->add('map_marker_source_default', null, array())
        ->add('map_popup_width_min', 'integer', array(
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'integer')),
                new Assert\Range(array(
                    'min' => 0,
                    'max' => 999,
                )),
            ),
        ))
        ->add('map_popup_height_min', 'integer', array(
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'integer')),
                new Assert\Range(array(
                    'min' => 0,
                    'max' => 999,
                )),
            ),
        ))
        ->add('map_video_width_you_tube', 'integer', array(
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'integer')),
                new Assert\Range(array(
                    'min' => 0,
                    'max' => 999,
                )),
            ),
        ))
        ->add('map_video_height_you_tube', 'integer', array(
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'integer')),
                new Assert\Range(array(
                    'min' => 0,
                    'max' => 999,
                )),
            ),
        ))
        ->add('map_video_width_vimeo', 'integer', array(
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'integer')),
                new Assert\Range(array(
                    'min' => 0,
                    'max' => 999,
                )),
            ),
        ))
        ->add('map_video_height_vimeo', 'integer', array(
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'integer')),
                new Assert\Range(array(
                    'min' => 0,
                    'max' => 999,
                )),
            ),
        ))
        ->add('map_video_width_flash', 'integer', array(
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'integer')),
                new Assert\Range(array(
                    'min' => 0,
                    'max' => 999,
                )),
            ),
        ))
        ->add('map_video_height_flash', 'integer', array(
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'integer')),
                new Assert\Range(array(
                    'min' => 0,
                    'max' => 999,
                )),
            ),
        ))
        ->add('geo_flash_server', null, array(
            'attr' => array('size' => 40),
            'constraints' => array(
                new Assert\Length(array(
                    'max' => 80,
                )),
            ),
        ))
        ->add('geo_flash_directory', null, array(
            'attr' => array('size' => 40),
            'constraints' => array(
                new Assert\Length(array(
                    'max' => 200,
                )),
            ),
        ))
        ->add('facebook_appid', null, array(
            'attr' => array('size' => 40),
            'constraints' => array(
                new Assert\Length(array(
                    'max' => 200,
                )),
            ),
        ))
        ->add('facebook_appsecret', null, array(
            'attr' => array('size' => 40),
            'constraints' => array(
                new Assert\Length(array(
                    'max' => 200,
                )),
            ),
        ))
        ->add('recaptchaPublicKey', null, array(
            'attr' => array('size' => 40),
            'constraints' => array(
                new Assert\Length(array(
                    'max' => 200,
                )),
            ),
        ))
        ->add('recaptchaPrivateKey', null, array(
            'attr' => array('size' => 40),
            'constraints' => array(
                new Assert\Length(array(
                    'max' => 200,
                )),
            ),
        ))
        ->add('recaptchaSecure', 'choice', array(
            'choices' => array(
                'Y' => 'newscoop.preferences.label.yesoption',
                'N' => 'newscoop.preferences.label.nooption',
            ),
            'data' => 'N',
            'multiple' => false,
            'expanded' => true,
            'constraints' => array(
                new Assert\NotBlank(),
            ),
        ))
        ->add('userGarbageActive', 'choice', array(
            'choices' => array(
                'Y' => 'newscoop.preferences.label.yesoption',
                'N' => 'newscoop.preferences.label.nooption',
            ),
            'multiple' => false,
            'expanded' => true,
            'constraints' => array(
                new Assert\NotBlank(),
            ),
        ))
        ->add('userGarbageDays', 'integer', array(
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'integer')),
                new Assert\Range(array(
                    'min' => 1,
                    'max' => 999,
                )),
            ),
        ))
        ->add('smartyUseProtocol', 'choice', array(
            'choices' => array(
                'Y' => 'newscoop.preferences.label.yesoption',
                'N' => 'newscoop.preferences.label.nooption',
            ),
            'multiple' => false,
            'expanded' => true,
            'constraints' => array(
                new Assert\NotBlank(),
            ),
        ))
        ->add('cronJobNotificationEmail', 'email', array(
            'attr' => array('size' => 64),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Email(),
                new Assert\Length(array(
                    'max' => 255,
                )),
            ),
        ))
        ->add('cronJobSmtpSender', 'email', array(
            'attr' => array('size' => 64),
            'constraints' => array(
                new Assert\Email(),
                new Assert\Length(array(
                    'max' => 255,
                )),
            ),
        ))
        ->add('cronJobSmtpSenderName', null, array(
            'attr' => array('size' => '64'),
            'constraints' => array(
                new Assert\Length(array(
                    'max' => 100,
                )),
            ),
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'system_pref',
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
