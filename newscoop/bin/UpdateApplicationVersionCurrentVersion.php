<?php
/*
 * This file is part of the project RMT
 *
 * Copyright (c) 2013, Liip AG, http://www.liip.ch
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Liip\RMT\Context;

/**
 * Class UpdateApplicationVersionCurrentVersion
 *
 * Custom pre-release action for updating the version number in the application
 */
class UpdateApplicationVersionCurrentVersion extends \Liip\RMT\Action\BaseAction
{
    public function getTitle() {
        return "Application version update";
    }

    public function execute()
    {
        // Output for devs
        $newVersion = Context::getParam('new-version');

        // newscoop/library/Newscoop/Version.php
        $appFile = realpath(__DIR__.'/../library/Newscoop/Version.php');
        Context::get('output')->writeln("New version [<yellow>$newVersion</yellow>] udpated into $appFile: ");
        $fileContent = file_get_contents($appFile);
        $fileContent = preg_replace('/(.*const VERSION = .*;)/', "    const VERSION = '$newVersion';", $fileContent);
        file_put_contents($appFile, $fileContent);

        // newscoop/template_engine/classes/CampVersion.php
        $appFile = realpath(__DIR__.'/../template_engine/classes/CampVersion.php');
        Context::get('output')->writeln("New version [<yellow>$newVersion</yellow>] udpated into $appFile: ");
        $fileContent = file_get_contents($appFile);
        $fileContent = preg_replace('/(.*private \$m_release = .*;)/', '    private $m_release = \''.$newVersion.'\';', $fileContent);
        $fileContent = preg_replace('/(.*private \$m_releaseDate = .*;)/', '    private $m_releaseDate = \''.date('Y-m-d').'\';', $fileContent);
        $fileContent = preg_replace('/(.*private \$m_copyrightYear = .*;)/', '    private $m_copyrightYear = '.date('Y').';', $fileContent);
        file_put_contents($appFile, $fileContent);

        $this->confirmSuccess();
    }
}
