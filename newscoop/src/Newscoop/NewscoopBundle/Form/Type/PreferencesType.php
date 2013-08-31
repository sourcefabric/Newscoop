<?php
/**
 * @packageNewscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

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

        $availableCacheEngines = \CacheEngine::AvailableEngines();
        $availableTemplateCacheHandlers = \CampTemplateCache::availableHandlers();
        $cacheEngines = array();
        $cacheTemplate = array();
        foreach ($availableCacheEngines as $cacheEngineName => $engineData) {
            $cacheEngines[$cacheEngineName] = $cacheEngineName;
        }

        foreach ($availableTemplateCacheHandlers as $handler => $value) {
            $cacheTemplate[$handler] = $handler;
        }

        $cacheLifetime = array();
        foreach (array(0 => 'disabled',
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

        $builder
        ->add('siteonline', 'choice', array(
            'choices'   => array('Y' => getGS("Yes"), 'N' => getGS("No")),
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
            'empty_value' => getGS('disabled'),
            'required' => false
        ))
        ->add('cache_engine', 'choice', array(
            'choices'   => $cacheEngines,
            'empty_value' => getGS('disabled'),
            'required' => false
        ))
        ->add('cache_template', 'choice', array(
            'choices'   => $cacheTemplate,
            'empty_value' => getGS('disabled'),
            'required' => false
        ))
        ->add('cache_image', 'choice', array(
            'choices'   => $cacheLifetime,
            'required' => true
        ))
        ->add('allow_recovery', 'choice', array(
            'choices'   => array('Y' => getGS("Yes"), 'N' => getGS("No")),
            'error_bubbling' => true,
            'multiple' => false,
            'expanded' => true,
            'required' => true,
        ))
        ->add('password_recovery_form', 'email', array(
            'attr' => array('maxlength' => '80', 'size' => '64'),
            'required' => false
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
            'choices'   => array('Y' => getGS("Yes"), 'N' => getGS("No")),
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
        ->add('email_contact', 'email', array(
            'attr' => array('maxlength' => 100, 'size' => 64),
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
            'choices'   => array('Y' => getGS("Yes"), 'N' => getGS("No")),
            'error_bubbling' => true,
            'multiple' => false,
            'expanded' => true,
            'required' => true,
        ))
        ->add('external_management', 'choice', array(
            'choices'   => array('Y' => getGS("Yes"), 'N' => getGS("No")),
            'error_bubbling' => true,
            'multiple' => false,
            'expanded' => true,
            'required' => true,
        ))
        ->add('use_replication', 'choice', array(
            'choices'   => array('Y' => getGS("Yes"), 'N' => getGS("No")),
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
            'error_bubbling' => true,
            'required' => false
        ))
        ->add('template_filter', null, array(
            'attr' => array('maxlength' => 50, 'size' => 30),
            'error_bubbling' => true,
            'required' => false
        ))
        ->add('external_cron_management', 'choice', array(
            'choices'   => array('Y' => getGS("Yes"), 'N' => getGS("No")),
            'error_bubbling' => true,
            'multiple' => false,
            'expanded' => true,
            'required' => true,
        ));
    }

    public function getName()
    {
        return 'preferencesform';
    }
}