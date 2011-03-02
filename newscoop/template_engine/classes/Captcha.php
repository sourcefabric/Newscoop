<?php
/**
 * @package Newscoop
 */

abstract class Captcha
{
    /**
     * Loads the captcha handler specified by the given name.
     * @param $p_handler
     * @return object
     */
    public static function factory($p_handler)
    {
        $pluginName = strtolower($p_handler);
        $path = WWW_DIR . '/plugins/' . $pluginName . '/classes';
        $filePath = "$path/Captcha_$p_handler.php";
        if (!file_exists($filePath)) {
            throw new InvalidCaptchaHandler($p_handler);
        }

        // check whether the plugin exists and is enabled
        $plugin = new CampPlugin($pluginName);
        if (!$plugin->exists() || !$plugin->isEnabled()) {
            return NULL;
        }

        require_once($filePath);
        $className = "Captcha_$p_handler";
        if (!class_exists($className)) {
            throw new InvalidCaptchaHandler($p_handler);
        }
        $captchaObj = new $className;
        return $captchaObj;
    }

    /**
     * @return void
     */
    abstract public function render();

    /**
     * @return void
     */
    abstract public function validate();

}

class InvalidCaptchaHandler extends Exception {}
