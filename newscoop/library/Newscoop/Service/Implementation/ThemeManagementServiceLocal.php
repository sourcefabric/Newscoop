<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Implementation;

use Newscoop\Entity\ArticleTypeField;
use Doctrine\ORM\Query;
use Newscoop\Service\ISyncResourceService;
use Newscoop\Entity\Output\OutputSettingsTheme;
use Newscoop\Entity\Output\OutputSettingsIssue;
use Newscoop\Service\Exception\DuplicateNameException;
use Newscoop\Version;
use Newscoop\Service\Implementation\Exception\FailedException;
use Newscoop\Service\Error\ThemeErrors;
use Newscoop\Service\IOutputService;
use Newscoop\Service\IOutputSettingIssueService;
use Newscoop\Entity\Resource;
use Newscoop\Service\Resource\ResourceId;
use Newscoop\Service\Model\SearchTheme;
use Newscoop\Entity\OutputSettings;
use Newscoop\Entity\Output;
use Newscoop\Entity\Theme;
use Newscoop\Entity\Publication;
use Newscoop\Service\IThemeManagementService;
use Newscoop\Service\IArticleTypeService;
use Newscoop\Utils\Validation;

/**
 * Provides the management services implementation for the themes.
 * The implementation relays on the local structure, this means that this service will use also the file system and Doctrine
 * for synchronizing with the database.
 */
class ThemeManagementServiceLocal extends ThemeServiceLocalFileSystem implements IThemeManagementService
{

    /**
     * Provides the relative folder name where the exported themes are located.
     */
    const FOLDER_EXPORTS = 'exports/';

    /**
     * Provides the relative folder name where the unassigned themes are located.
     */
    const FOLDER_UNASSIGNED = 'unassigned';

    /**
     * Provides the prefix fodler name for the folders that contain themes for a publication.
     */
    const FOLDER_PUBLICATION_PREFIX = 'publication_';

    /**
     * Provides the prefix fodler name for the folders that are themes.
     */
    const FOLDER_THEME_PREFIX = 'theme_';

    /**
     * Provides the template extension.
     */
    const FILE_TEMPLATE_EXTENSION = 'tpl';

    const TAG_ROOT = 'theme';

    const TAG_OUTPUT = 'output';
    const ATTR_OUTPUT_NAME = 'name';

    const TAG_PAGE_FRONT = 'frontPage';
    const TAG_PAGE_SECTION = 'sectionPage';
    const TAG_PAGE_ARTICLE = 'articlePage';
    const TAG_PAGE_ERROR = 'errorPage';

    const TAG_ARTICLE_TYPE = 'articleType';
    const ATTR_ARTICLE_TYPE_NAME = 'name';
    const ATTR_ARTICLE_TYPE_FILED_NAME = 'name';
    const ATTR_ARTICLE_TYPE_FILED_TYPE = 'type';
    const ATTR_ARTICLE_TYPE_FILED_LENGTH = 'length';

    const ATTR_PAGE_SRC = 'src';

    /* --------------------------------------------------------------- */

    /** @var Doctrine\ORM\EntityManager */
    private $em = NULL;
    /** @var Newscoop\Service\IOutputService */
    private $outputService = NULL;
    /** @var Newscoop\Service\IOutputSettingIssueService */
    private $outputSettingIssueService = NULL;
    /** @var Newscoop\Service\ISyncResourceService */
    private $syncResourceService = NULL;
    /** @var Newscoop\Service\IArticleTypeService */
    private $articleTypeService = NULL;

    /* --------------------------------------------------------------- */

    function getUnassignedThemes(SearchTheme $search = NULL, $offset = 0, $limit = -1)
    {
        $allConfigs = $this->findAllThemesConfigPaths();
        $configs = array();

        $length = strlen(self::FOLDER_UNASSIGNED);
        foreach ($allConfigs as $id => $config){
            if(strncmp($config, self::FOLDER_UNASSIGNED, $length) == 0){
                $configs[$id] = $config;
            }
        }

        $themes = $this->loadThemes($configs);
        if($search !== NULL){
            $themes = $this->filterThemes($search, $themes);
        }

        return $this->trim($themes, $offset, $limit);
    }

    function getThemes($publication, SearchTheme $search = NULL, $offset = 0, $limit = -1)
    {
        Validation::notEmpty($publication, 'publication');
        if($publication instanceof Publication){
            Validation::notEmpty($publication->getId(), 'publication.id');
            $publicationId = $publication->getId();
        } else  {
            $publicationId = $publication;
        }

        $allConfigs = $this->findAllThemesConfigPaths();
        $configs = array();

        $pubFolder = self::FOLDER_PUBLICATION_PREFIX.$publicationId;
        $length = strlen($pubFolder);
        foreach ($allConfigs as $id => $config){
            if(strncmp($config, $pubFolder, $length) == 0){
                $configs[$id] = $config;
            }
        }

        $themes = $this->loadThemes($configs);
        if($search !== NULL){
            $themes = $this->filterThemes($search, $themes);
        }

        return $this->trim($themes, $offset, $limit);
    }

    function getTemplates($theme)
    {
        Validation::notEmpty($theme, 'theme');

        if($theme instanceof Theme){
            $themePath = $theme->getPath();
        } else {
            $themePath = $theme;
        }

        $resources = array();
        $folder = $this->toFullPath($themePath);
        if (is_dir($folder)) {
            if($dh = opendir($folder)){
                while (($file = readdir($dh)) !== false) {
                    if ($file != "." && $file != ".."){
                        if(pathinfo($file, PATHINFO_EXTENSION) === self::FILE_TEMPLATE_EXTENSION){
                            $rsc = new Resource();
                            $rsc->setName($file);
                            $rsc->setPath($themePath.$file);
                            $resources[] = $rsc;
                        }
                    }
                }
                closedir($dh);
            }
        }

        return $resources;
    }

    function findOutputSetting(Theme $theme, Output $output)
    {
        Validation::notEmpty($theme, 'theme');
        Validation::notEmpty($output, 'output');

        $xml = $this->loadXML($this->toFullPath($theme, $this->themeConfigFileName));
        if($xml != NULL){
            $nodes = $this->getNodes($xml, self::TAG_OUTPUT);
            foreach ($nodes as $node) {
                /* @var $node \SimpleXMLElement */
                try {
                    $outputName = $this->readAttribute($node, self::ATTR_OUTPUT_NAME);
                    if($output->getName() == $outputName){
                        $oset = $this->loadOutputSetting($node, $theme->getPath());
                        $oset->setOutput($output);

                        return $oset;
                    }
                }catch(FailedException $e){
                    // Nothing to do.
                }
            }
        }

        return NULL;
    }

    function getOutputSettings(Theme $theme)
    {
        Validation::notEmpty($theme, 'theme');

        return $this->loadOutputSettings($theme->getPath());
    }

    /**
     * @author mihaibalaceanu
     * @param \Newscoop\Entity\Theme $theme
     * @return object
     */
    function getArticleTypes(Theme $theme)
    {
        Validation::notEmpty($theme, 'theme');
        $xml = $this->loadXML($this->toFullPath($theme, $this->themeConfigFileName));
        $ret = new \stdClass;
        // getting the article types
        foreach( $xml->xpath( '/'.self::TAG_ROOT.'/'.self::TAG_ARTICLE_TYPE ) as $artType )
        {

            $artTypeName = (string) $this->readAttribute($artType, self::ATTR_ARTICLE_TYPE_NAME);
            /*            if( isset( $ret->$artTypeName ) ) {
             $artTypeName .= "_";
             var_dump( $artType->{self::ATTR_ARTICLE_TYPE_NAME} );
             }
             */
            // set article type name on return array
            $ret->$artTypeName = new \stdClass;
            // getting the article type fields
            foreach( $xml->xpath( '/'.self::TAG_ROOT.'/'.self::TAG_ARTICLE_TYPE.'[@'.self::ATTR_ARTICLE_TYPE_NAME.'=(\''.$artTypeName.'\')]/*' ) as $artTypeField )
            {
                try
                {
                    $ret->{$artTypeName}->{(string) $artTypeField[self::ATTR_ARTICLE_TYPE_FILED_NAME]} = (object) array
                    (
                    self::ATTR_ARTICLE_TYPE_FILED_TYPE   => (string) $artTypeField[self::ATTR_ARTICLE_TYPE_FILED_TYPE],
                    self::ATTR_ARTICLE_TYPE_FILED_LENGTH => (string) $artTypeField[self::ATTR_ARTICLE_TYPE_FILED_LENGTH],
                    );
                }
                catch (\Exception $e){}
            }
        }
        return $ret;
    }

    /* --------------------------------------------------------------- */

    function exportTheme($theme){
        Validation::notEmpty($theme, 'theme');
        if(!($theme instanceof Theme)){
            $theme = $this->findById($theme);
        }

        if( !file_exists( $xpth = $this->toFullPath(self::FOLDER_EXPORTS ) ) ) {
            mkdir( $xpth );
        }

        $zipFilePath = realpath( $xpth );
        $zipFilePath = $zipFilePath.DIR_SEP.preg_replace('([^a-zA-Z0-9_\-.]+)', '_', $theme->getName()).'.zip';

        // create object
        $zip = new \ZipArchive();
        // open archive
        if ($zip->open($zipFilePath, \ZIPARCHIVE::CREATE) !== TRUE) {
            die ("Could not open archive");
        }


        $themePath = $this->toFullPath($theme->getPath());
        $lenght = strlen($themePath);
        // initialize an iterator
        // pass it the directory to be processed
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($themePath));
        // iterate over the directory
        // add each file found to the archive
        foreach ($iterator as $key=>$value) {
            $fname = substr($key, $lenght);
            if(strlen($fname) > 0){
                $zip->addFile(realpath($key), $fname) or die ("ERROR: Could not add file: $key");
            }
        }
        // close and save archive
        $zip->close();

        return $zipFilePath;
    }

    function installTheme($filePath){
        Validation::notEmpty($filePath, 'filePath');

        $zip = new \ZipArchive;
        $res = $zip->open($filePath);
        if ($res === TRUE) {
            $themePath = $this->getNewThemeFolder(self::FOLDER_UNASSIGNED.'/');
            $zip->extractTo(realpath($this->toFullPath($themePath)));
            $zip->close();
            return true;
        } else {
            return false;
        }
    }

    function updateTheme(Theme $theme)
    {
        Validation::notEmpty($theme, 'theme');
        $allConfigs = $this->findAllThemesConfigPaths();

        $config = $allConfigs[$theme->getId()];
        if(!isset($config)){
            throw new \Exception("Unknown theme id '.$theme->getId().' to update.");
        }
        // We have to check if there is no other theme by the new theme name.
        $inFolder = $this->filterThemesConfigPathsInSameFolder($config, $allConfigs);
        // Remove the theme to be updated.
        unset($inFolder[$theme->getId()]);

        $themes = $this->loadThemes($inFolder);
        foreach($themes as $th){
            /* @var $th Theme */
            if(trim($th->getName()) === trim($theme->getName())){
                throw new DuplicateNameException();
            }
        }

        $xml = $this->loadXML($this->toFullPath($config));
        $xml[self::ATTR_THEME_NAME] = $theme->getName();
        $xml[self::ATTR_THEME_DESIGNER] = $theme->getDesigner();
        $xml[self::ATTR_THEME_VERSION] = $theme->getVersion();
        $xml[self::ATTR_THEME_NEWSCOOP_VERSION] = Version::VERSION;
        $xml->{self::TAG_DESCRIPTION} = $theme->getDescription();

        $xml->asXML($this->toFullPath($theme, $this->themeConfigFileName));
    }

    function removeTheme($theme)
    {
        Validation::notEmpty($theme, 'theme');
        if(!($theme instanceof Theme)){
            $theme = $this->findById($theme);
        }
        $themePath = $theme->getPath();
        if(!$this->getOutputSettingIssueService()->isThemeUsed($themePath)){
            $this->rrmdir($this->toFullPath($themePath));
            $this->getSyncResourceService()->clearAllFor($themePath);
            // Reset the theme configs cache so also the new theme will be avaialable
            $this->cacheThemeConfigs = NULL;
            return true;
        }
        return false;
    }

    function assignTheme(Theme $theme, Publication $publication)
    {
        Validation::notEmpty($theme, 'theme');
        Validation::notEmpty($publication, 'publication');

        // We have to check if there is no other theme by the new theme name.
        foreach($this->getThemes($publication) as $th){
            /* @var $th Theme */
            if(trim($th->getName()) === trim($theme->getName())){
                throw new DuplicateNameException();
            }
        }

        $themeFolder = $this->getNewThemeFolder(self::FOLDER_PUBLICATION_PREFIX.$publication->getId().'/');
        $themeFullFolder = $this->toFullPath($themeFolder);
        try
        {
            $this->copy($this->toFullPath($theme), $themeFullFolder);

            // Reset the theme configs cache so also the new theme will be avaialable
            $this->cacheThemeConfigs = NULL;

            // We need to persist the theme ouput setting for the new publication theme
            $em = $this->getEntityManager();


            $pathRsc = $this->getSyncResourceService()->getThemePath($themeFolder);

            // Persist the coresponding ouput settings theme to the database
            $outSets = $this->loadOutputSettings($themeFolder);
            foreach($outSets as $outSet){
                /* @var $outSet OutputSettings */
                $qb = $em->createQueryBuilder();
                $qb->select('th')->from(OutputSettingsTheme::NAME, 'th');
                $qb->where('th.publication = :publication');
                $qb->andWhere('th.themePath = :themePath');
                $qb->andWhere('th.output = :output');
                $qb->setParameter('publication', $publication);
                $qb->setParameter('themePath', $pathRsc);
                $qb->setParameter('output', $outSet->getOutput());
                $result = $qb->getQuery()->getResult();
                
                if(count($result) > 0){
                    $outTh = $result[0];
                } else {
                    $outTh = new OutputSettingsTheme();
                    $outTh->setPublication($publication);
                    $outTh->setThemePath($pathRsc);
                    $outTh->setOutput($outSet->getOutput());
                }
                $this->syncOutputSettings($outTh, $outSet);

                $em->persist($outTh);
            }
            $em->flush();
        }
        catch(\Exception $e)
        {
            $this->rrmdir($themeFullFolder);
            throw $e;
        }
        return $this->loadThemeByPath($themeFolder);
    }

    function assignOutputSetting(OutputSettings $outputSettings, Theme $theme)
    {
        Validation::notEmpty($outputSettings, 'outputSettings');
        Validation::notEmpty($theme, 'theme');

        // We update the XML config file with the new output setting.
        $xml = $this->loadXML($this->toFullPath($theme, $this->themeConfigFileName));
        if($xml == NULL){
            throw new \Exception("Unknown theme path '.$theme->gePath().' to assign to.");
        }
        $outNode = NULL;
        $nodes = $this->getNodes($xml, self::TAG_OUTPUT, self::ATTR_OUTPUT_NAME, $outputSettings->getOutput()->getName());
        if (count($nodes) == 0) {
            // The ouput node does not exist, we need to add it.
            $node = $xml->addChild(self::TAG_OUTPUT);
            $node[self::ATTR_OUTPUT_NAME] = $outputSettings->getOutput()->getName();
        } else {
            // The ouput node exists so we need to update it.
            $node = $nodes[0];
            /* @var $node \SimpleXMLElement */
            // We remove all the childens node that contain the template pages.
            $toRemove = array();
            foreach ($node->children() as $kid){
                $toRemove[] = $kid->getName();
            }
            foreach ($toRemove as $name){
                unset($node->$name);
            }
        }
        $front = $node->addChild(self::TAG_PAGE_FRONT);
        $front[self::ATTR_PAGE_SRC] = $this->getRelativePath($outputSettings->getFrontPage(), $theme->getPath());

        $section = $node->addChild(self::TAG_PAGE_SECTION);
        $section[self::ATTR_PAGE_SRC] = $this->getRelativePath($outputSettings->getSectionPage(), $theme->getPath());

        $article = $node->addChild(self::TAG_PAGE_ARTICLE);
        $article[self::ATTR_PAGE_SRC] = $this->getRelativePath($outputSettings->getArticlePage(), $theme->getPath());

        $error = $node->addChild(self::TAG_PAGE_ERROR);
        $error[self::ATTR_PAGE_SRC] = $this->getRelativePath($outputSettings->getErrorPage(), $theme->getPath());

        $xml->asXML($this->toFullPath($theme, $this->themeConfigFileName));

        // We have to update also the output theme settings in the database if there is one.
        $em = $this->getEntityManager();

        $q = $em->createQueryBuilder();
        $q->select('ost')->from(OutputSettingsTheme::NAME, 'ost');
        $q->leftJoin('ost.themePath', 'rsc');
        $q->andWhere('rsc.path = ?1');
        $q->setParameter(1, $theme->getPath());

        $result = $q->getQuery()->getResult();
        // If there are results than it means that the theme belongs to a publication
        if(count($result) > 0){
            $updated = FALSE;
            foreach ($result as $outTh){
                /* @var $outTh Newscoop\Entity\Output\OutputSettingsTheme */
                if($outTh->getOutput() == $outputSettings->getOutput()){
                    $this->syncOutputSettings($outTh, $outputSettings);
                    $em->persist($outTh);
                    $updated = TRUE;
                    break;
                }
            }
            if(!$updated){
                $pathRsc = new Resource();
                $pathRsc->setName(self::THEME_PATH_RSC_NAME);
                $pathRsc->setPath($themeFolder);
                $pathRsc = $this->getSyncResourceService()->getSynchronized($pathRsc);

                $outTh = new OutputSettingsTheme();
                $outTh->setPublication($result[0]->getPublication());
                $outTh->setThemePath($pathRsc);

                $outTh->setOutput($outputSettings->getOutput());
                $this->syncOutputSettings($outTh, $outputSettings);

                $em->persist($outTh);
            }
        }
    }

    /**
     * Adds new mapping in the theme xml
     *
     * @param $articleTypes an array of mapping new values to old ones
     * 		[ oldTypeName => [
     * 			name : newTypeName,
     * 			ignore : boolean,
     * 			fields' : [ OldFieldName : [ name : new/oldType, parentType : existingSysType, ignore : boolean ], [...] ]
     * 			]
     * 		, [...] ]
     * 	parentType => existingSysType will be used for getting it's other props from db
     *
     * @return string the generated xml
     */
    function assignArticleTypes($articleTypes, Theme $theme)
    {
        Validation::notEmpty($articleTypes, 'articleTypes');
        Validation::notEmpty($theme, 'theme');

        $xml = $this->loadXML( ( $xmlFileName = $this->toFullPath($theme, $this->themeConfigFileName ) ) );
        if($xml == NULL){
            throw new \Exception("Unknown theme path '.$theme->gePath().' to assign to.");
        }

        $artServ = $this->getArticleTypeService();

        $artCache = array();
        /**
         * function purpose: not to make so many calls to db
         * @param string $parentType article type name
         * @param string $fieldName field name doh
         * @return ArticleTypeField|null
         */
        $getFieldByName = function( $parentType, $fieldName ) use( $artServ, &$artCache )
        {
            if( !isset( $artCache[ $parentType.$fieldName ] ) )
            {
                $artType = $artServ->findTypeByName( $parentType );
                if( $artType ) {
                    $artCache[ $parentType.$fieldName ] = $artServ->findFieldByName( $artType, $fieldName );
                }
            }
            return $artCache[ $parentType.$fieldName ];
        };


        // parse the mapping array
        foreach( $articleTypes as $typeName => $type )
        {
            $articleXPath = '/'.self::TAG_ROOT.'/'.self::TAG_ARTICLE_TYPE.'[@'.self::ATTR_ARTICLE_TYPE_NAME.'=(\''.$typeName.'\')]';

            $fieldNodes = $xml->xpath("$articleXPath/*");

            if( count($fieldNodes) )
            {
                foreach( $fieldNodes as $fieldNode )
                {
                    if( !( $updateField = $type['fields'][ (string) $fieldNode[self::ATTR_ARTICLE_TYPE_FILED_NAME] ] )
                    || $updateField['ignore'] == true )
                    continue;

                    $fieldNode[self::ATTR_ARTICLE_TYPE_FILED_NAME] = $updateField['name'];

                    $theField = $getFieldByName( $updateField['parentType'], $updateField['name'] );
                    /* @var $theField ArticleTypeField */
                    if( $theField )
                    {
                        $fieldNode[self::ATTR_ARTICLE_TYPE_FILED_LENGTH] = $theField->getLength();
                        $fieldNode[self::ATTR_ARTICLE_TYPE_FILED_TYPE] = $theField->getType();
                    }
                }
            }

            if( $type['ignore'] ) {
                continue;
            }
            // set new article type node
            $typeNode = $xml->xpath( $articleXPath );
            if( !( $typeNode = current( $typeNode ) ) ) {
                continue;
            }
            /* @var $typeNode SimpleXMLElement */
            $typeNode[self::ATTR_ARTICLE_TYPE_NAME] = $type['name'];
        }

        return $xml->asXML( $xmlFileName );
    }

    /* --------------------------------------------------------------- */

    /**
     * Provides the dictrine entity manager.
     *
     * @return Doctrine\ORM\EntityManager
     * 		The doctrine entity manager.
     */
    protected function getEntityManager()
    {
        if($this->em === NULL){
            $doctrine = \Zend_Registry::get('doctrine');
            $this->em = $doctrine->getEntityManager();
        }
        return $this->em;
    }

    /**
     * Provides the ouput service.
     *
     * @return Newscoop\Service\IOutputService
     *		The service service to be used.
     */
    protected function getOutputService()
    {
        if ($this->outputService === NULL) {
            $this->outputService = $this->getResourceId()->getService(IOutputService::NAME);
        }
        return $this->outputService;
    }

    /**
     * Provides the Output setting issue service.
     *
     * @return Newscoop\Service\IOutputSettingIssueService
     * 		The output setting issue service to be used by this controller.
     */
    protected function getOutputSettingIssueService()
    {
        if ($this->outputSettingIssueService === NULL) {
            $this->outputSettingIssueService = $this->getResourceId()->getService(IOutputSettingIssueService::NAME);
        }
        return $this->outputSettingIssueService;
    }

    /**
     * Provides the sync resource service.
     *
     * @return Newscoop\Service\ISyncResourceService
     *		The sync resource service to be used.
     */
    protected function getSyncResourceService()
    {
        if ($this->syncResourceService === NULL) {
            $this->syncResourceService = $this->getResourceId()->getService(ISyncResourceService::NAME);
        }
        return $this->syncResourceService;
    }

    /**
     * Provides the article type service.
     *
     * @return Newscoop\Service\IArticleTypeService
     *
     */
    public function getArticleTypeService()
    {
        if( $this->articleTypeService === NULL ) {
            $this->articleTypeService = $this->getResourceId()->getService(IArticleTypeService::NAME);
        }
        return $this->articleTypeService;
    }

    /* --------------------------------------------------------------- */

    /**
     * Load all the output settings from the specified path.
     *
     * @param \SimpleXMLElement $node
     * 		The node from which to load, *(not null not empty).
     *
     * @param string $themePath
     * 		The theme path from where to load the output settings, *(not null not empty).
     * @return array
     * 		The array containing all the found output settings, not null.
     */
    protected function loadOutputSettings($themePath)
    {
        $outputs = array();
        $xml = $this->loadXML($this->toFullPath($themePath, $this->themeConfigFileName));
        if($xml != NULL){
            $nodes = $this->getNodes($xml, self::TAG_OUTPUT);
            foreach ($nodes as $node){
                /* @var $node \SimpleXMLElement */
                try{
                    // First we have to search if there is an ouput
                    // registered with the name specifed in the XML.
                    $outputName = $this->readAttribute($node, self::ATTR_OUTPUT_NAME);
                    $output = $this->getOutputService()->findByName($outputName);
                    if($output != NULL){
                        $oset = $this->loadOutputSetting($node, $themePath);
                        $oset->setOutput($output);

                        $outputs[] = $oset;
                    } else {
                        $this->getErrorHandler()->warning(ThemeErrors::OUTPUT_MISSING, $outputName);
                    }
                }catch(XMLMissingAttribueException $e){
                    $this->getErrorHandler()->error(ThemeErrors::XML_MISSING_ATTRIBUTE, self::ATTR_OUTPUT_NAME, self::TAG_OUTPUT);
                }catch(FailedException $e){
                    // Nothing to do.
                }
            }
        }
        return $outputs;
    }

    /**
     * Load the output setting from the provided xml node.
     *
     * @param \SimpleXMLElement $nodeOutput
     * 		The node from which to load, *(not null not empty).
     *
     * @param string $themePath
     * 		The theme path to construct the resource path based on, *(not null not empty).
     * @throws FailedException
     * 		Thrown if the output setting has failed to be obtained, this exception will not contain any message, the resons of failure
     * 		will be looged in the error handler.
     * @return \Newscoop\Entity\OutputSettings
     * 		The loaded output setting, not null.
     */
    protected function loadOutputSetting(\SimpleXMLElement $nodeOutput, $themePath)
    {
        $oset = new OutputSettings();

        $oset->setFrontPage($this->loadOutputResource($nodeOutput, self::TAG_PAGE_FRONT, $themePath));
        $oset->setSectionPage($this->loadOutputResource($nodeOutput, self::TAG_PAGE_SECTION, $themePath));
        $oset->setArticlePage($this->loadOutputResource($nodeOutput, self::TAG_PAGE_ARTICLE, $themePath));
        $oset->setErrorPage($this->loadOutputResource($nodeOutput, self::TAG_PAGE_ERROR, $themePath));

        return $oset;
    }

    /**
     * Reads the resources from an output tag.
     *
     * @param \SimpleXMLElement $parent
     * 		The parent output node to read the resources from, *(not null not empty).
     * @param string $tagName
     * 		The tag name containing the resource, *(not null not empty).
     * @param string $themePath
     * 		The theme path to construct the resource path based on, *(not null not empty).
     * @param string $name
     * 		The name of the created resource based on the found tag, *(not null not empty).
     * @throws FailedException
     * 		Thrown if the resource has failed to be obtained, this exception will not contain any message, the resons of failure
     * 		will be looged in the error handler.
     * @return \Newscoop\Entity\Resource
     * 		The obtained resource, not null.
     */
    protected function loadOutputResource(\SimpleXMLElement $parent, $tagName, $themePath)
    {
        $nodes = $this->getNodes($parent, $tagName);
        if(count($nodes) == 0){
            $this->getErrorHandler()->error(ThemeErrors::XML_MISSING_TAG, $tagName, $parent->getName());
            throw new FailedException();
        }
        if(count($nodes) > 1){
            $this->getErrorHandler()->error(ThemeErrors::XML_TO_MANY_TAGS, $tagName, $parent->getName(), 1);
            throw new FailedException();
        }
        $node = $nodes[0];
        /* @var $node \SimpleXMLElement */
        try{
            $rsc = new Resource();
            $rsc->setName($tagName);
            $rsc->setPath($this->escapePath($themePath.$this->readAttribute($node, self::ATTR_PAGE_SRC)));
            return $rsc;
        }catch(XMLMissingAttribueException $e){
            $this->getErrorHandler()->error(ThemeErrors::XML_MISSING_ATTRIBUTE, $e->getAttributeName(), $tagName);
            throw new FailedException();
        }
    }

    /**
     * Provides the relative path of the resource based on the provided theme path.
     * This method also checks if the resource path is compatible with the theme path
     * meaning that the resource needs to be placed in the theme.
     *
     * @param Resource $rsc
     * 		The resource to extract the relative path from, not null.
     * @param string $themePath
     * 		The theme path, not null.
     * @throws Exception
     * 		In case the resource does not belong to the theme.
     * @return string
     * 		The relative [path in regards with the theme path for the resource.
     */
    protected function getRelativePath(Resource $rsc, $themePath)
    {
        $path = $rsc->getPath();
        $lenght = strlen($themePath);
        if(strncmp($path, $themePath, $lenght) != 0){
            throw new \Exception("The resource path '.$path.' is not for the provided theme path '.$themePath.'.");
        }
        $path = substr($path, $lenght);
        return $path;
    }

    /* --------------------------------------------------------------- */

    /**
     * Filter from the provided configs array all the configs that are located under the same folder.
     * For instance if the config is in a publication folder than this method will return all the configs for that
     * publicatio.
     *
     * @param string $config
     * 		The config path to be searched for, not null.
     * @param array @configs
     * 		The array containing as key the id of the theme config (index) and as a value the relative
     * 		path of the theme configuration XML file for all configurations to be filtered, not null.
     * @return array
     * 		The array containing as key the id of the theme config (index) and as a value the relative
     * 		path of the theme configuration XML file for all configurations that are iun the same folder, not null can be empty.
     */
    protected function filterThemesConfigPathsInSameFolder($config, array $allConfigs)
    {
        // First we extract the relative path for the 'theme.xml' config file
        $rPath = $this->extractRelativePathFrom($config);
        // Now we extract the relative path of the theme folder.
        $rPath = $this->extractRelativePathFrom(substr($rPath, 0, -1));

        $inFolder = array();
        $length = strlen($rPath);
        foreach ($allConfigs as $id => $cnf){
            if(strncmp($cnf, $rPath, $length) == 0){
                $inFolder[$id] = $cnf;
            }
        }

        return $inFolder;
    }

    /* --------------------------------------------------------------- */

    /**
     * Copies from the from output settings to the to output settings all the pages (front, article ...).
     * @param Newscoop\Entity\OutputSettings $to
     * 		The output setting to copy to, *(not null not empty).
     * @param Newscoop\Entity\OutputSettings $from
     * 		The output setting to copy from, *(not null not empty).
     */
    protected function syncOutputSettings(OutputSettings $to, OutputSettings $from)
    {
        $syncRsc = $this->getSyncResourceService();
        $to->setFrontPage($syncRsc->getSynchronized($from->getFrontPage()));
        $to->setSectionPage($syncRsc->getSynchronized($from->getSectionPage()));
        $to->setArticlePage($syncRsc->getSynchronized($from->getArticlePage()));
        $to->setErrorPage($syncRsc->getSynchronized($from->getErrorPage()));
    }

    /* --------------------------------------------------------------- */

    /**
     * Provides a new folder (Automatically created) to place a new theme.
     * @param str $inFolder
     * 		The folder wehere to place the new theme folder, this has to be relative to the
     * 		themes folder.
     * @return str
     * 		The relative theme path.
     */
    protected function getNewThemeFolder($inFolder)
    {
        $number = 1;
        $fullfodler = $this->toFullPath($inFolder);
        $length = strlen(self::FOLDER_THEME_PREFIX);
        if (is_dir($fullfodler)) {
            if ($dh = opendir($fullfodler)) {
                while (($dir = readdir($dh)) !== false) {
                    if ($dir != "." && $dir != ".." && is_dir($fullfodler.$dir)){
                        if(strncmp($dir, self::FOLDER_THEME_PREFIX, $length) == 0){
                            $themeNr = substr($dir, $length);
                            if(is_numeric($themeNr)){
                                $number = ((int)$themeNr) + 1;
                            }
                        }
                    }
                }
                closedir($dh);
            }
        } else {
            mkdir($fullfodler);
        }

        $themeFolder = $inFolder.self::FOLDER_THEME_PREFIX.$number.'/';
        $themeFullFolder = $this->toFullPath($themeFolder);
        @mkdir($themeFullFolder);
        return $themeFolder;
    }

    /**
     * Copies recursivelly the folder content from src to destination.
     *
     * @param string $src
     * 		The source folder, *(not null not empty).
     * @param string $dst
     * 		the destination folder, *(not null not empty).
     */
    protected function copy($src, $dst)
    {
        $dir = opendir($src);
        if(!file_exists($dst))
        mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    $this->copy($src . '/' . $file,$dst . '/' . $file);
                }
                else {
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /**
     * Deletes recursivelly the folder content from dir.
     *
     * @param string $dir
     * 		The floder to be deleted, *(not null not empty).
     */
    protected function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir"){
                        $this->rrmdir($dir."/".$object);
                    }  else {
                        unlink($dir."/".$object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }
}