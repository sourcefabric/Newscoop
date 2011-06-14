<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Upgrade plugin
 */
class Application_Plugin_Upgrade extends Zend_Controller_Plugin_Abstract
{
    /**
     * Register plugins by settings
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $module = $request->getModuleName();

        if (!file_exists(APPLICATION_PATH . '/../upgrade.php')) {
            return;
        }

        switch ($module) {
            case 'admin':
                $this->printMessage("Site is down for upgrade. Please initiate upgrade process.");
                break;

            default:
                $this->printMessage("The website you are trying to view is currently down for maintenance.<br>Normal service will resume shortly.");
                break;
        }
    }

    /**
     * Print message
     *
     * @param string $message
     * @return void
     */
    private function printMessage($message)
    {
        header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');

        echo '<!DOCTYPE html>';
        echo '<html><head><charset="utf-8" />';
        echo '<title>Upgrade</title>';
        echo '<meta http-equiv="Refresh" content="10">';
        echo '</head><body>';
        echo '<h2>', $message, '</h2>';
        echo '</body></html>';

        exit;
    }
}
