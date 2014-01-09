<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Installer\Services;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Newscoop\Entity\Resource;
use Newscoop\Service\IThemeManagementService;
use Newscoop\Service\IPublicationService;
use Newscoop\Service\Implementation\ThemeManagementServiceLocal;

/**
 * Demosite service
 */
class DemositeService
{
    private $logger;
    private $templatesDir;
    private $installDir;
    private $filesystem;

    /**
     * @param object $logger
     */
    public function __construct($logger)
    {
        $this->logger = $logger;
        $this->templatesDir = __DIR__ . '/../../../../themes';
        $this->installDir = __DIR__ . '/../../../../install';
        $this->newscoopDir = __DIR__ . '/../../../..';
        $this->filesystem = new Filesystem();
    }

    /**
     * Copy templates
     *
     * @param string $templateName Choosen template name
     */
    public function copyTemplate($templateName)
    {
        // copies template files to corresponding directory
        $source = $this->installDir.'/Resources/sample_templates/'.$templateName.'/';
        $target = $this->templatesDir.'/'.ThemeManagementServiceLocal::FOLDER_UNASSIGNED.'/'.$templateName;
        $this->filesystem->mirror($source, $target);
        $this->filesystem->mirror($this->installDir.'/Resources/sample_data/files', $this->newscoopDir.'/public/files');
        $this->filesystem->mirror($this->installDir.'/Resources/sample_data/images', $this->newscoopDir.'/images');

        $resourceId = new \Newscoop\Service\Resource\ResourceId(__CLASS__);
        $themeService = $resourceId->getService(IThemeManagementService::NAME_1);
        $publicationService = $resourceId->getService(IPublicationService::NAME);
        foreach ($themeService->getUnassignedThemes() as $theme) {
            foreach ($publicationService->getEntities() as $publication) {
                $themeService->assignTheme($theme, $publication);
            }
        }

        $this->filesystem->mirror($this->installDir.'/Resources/sample_templates', $this->templatesDir.'/'.ThemeManagementServiceLocal::FOLDER_UNASSIGNED);
    }

    /**
     * Install empty theme
     */
    public function installEmptyTheme()
    {
        $emptyDir = $this->templatesDir.'/'.ThemeManagementServiceLocal::FOLDER_UNASSIGNED.'/empty/';
        $themeXml = <<<XML
<theme name="Empty" designer="default" version="1.0" require="3.6">
    <description>This is an empty theme</description>
    <presentation-img src="preview-front.jpg" name="Front page"/>
    <presentation-img src="preview-section.jpg" name="Section page"/>
    <presentation-img src="preview-article.jpg" name="Article page"/>
    <output name="Web">
        <frontPage src="front.tpl"/>
        <sectionPage src="section.tpl"/>
        <articlePage src="article.tpl"/>
        <errorPage src="404.tpl"/>
    </output>
</theme>
XML;

        $this->filesystem->mkdir($emptyDir);
        $sxml = new \SimpleXMLElement($themeXml);
        $sxml->asXML($emptyDir.'theme.xml');

        // creating preview images
        $preview = @imagecreatetruecolor(210, 130);
        $logoPoints = array( 159, 9,   113, 34,   86, 99,   150, 121,   203, 99,   138, 78 );
        $textColor = imagecolorallocate($preview, 191, 191, 191);
        imagefill($preview, 0, 0, imagecolorallocate($preview, 255, 255, 255));
        imagefilledpolygon($preview, $logoPoints, 6, imagecolorallocate($preview, 239, 239, 239));
        imagestring($preview, 5, 10, 100, 'Empty Theme', $textColor);
        imagejpeg($preview, $emptyDir."preview-front.jpg", 100);
        imagejpeg($preview, $emptyDir."preview-article.jpg", 100);
        imagejpeg($preview, $emptyDir."preview-section.jpg", 100);
        imagedestroy($preview);

        // put empty templates in theme
        file_put_contents($emptyDir."front.tpl", "<!-- Front page template -->");
        file_put_contents($emptyDir."section.tpl", "<!-- Section page template -->");
        file_put_contents($emptyDir."article.tpl", "<!-- Article page template -->");
        file_put_contents($emptyDir."404.tpl", "<!-- Error page template -->");
    }
}
