<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
// is not defined in these cases.
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/classes/Article.php');
require_once($g_documentRoot.'/classes/ArticleAttachment.php');
require_once($g_documentRoot.'/classes/Template.php');
require_once($g_documentRoot.'/classes/Language.php');
require_once($g_documentRoot.'/template_engine/metaclasses/MetaDbObject.php');


/**
 * @package Campsite
 */
final class MetaArticle extends MetaDbObject {
    private $m_articleData = null;

    private $m_state = null;

    private $m_contentCache = null;


    private function InitProperties()
    {
        if (!is_null($this->m_properties)) {
            return;
        }
        $this->m_properties['name'] = 'Name';
        $this->m_properties['number'] = 'Number';
        $this->m_properties['keywords'] = 'Keywords';
        $this->m_properties['type_name'] = 'Type';
        $this->m_properties['creation_date'] = 'UploadDate';
        $this->m_properties['publish_date'] = 'PublishDate';
        $this->m_properties['url_name'] = 'ShortName';
        $this->m_properties['comments_locked'] = 'comments_locked';
        $this->m_properties['last_update'] = 'time_updated';
    }


    public function __construct($p_languageId = null, $p_articleId = null)
    {
        $articleObj = new Article($p_languageId, $p_articleId);
        $this->m_dbObject = $articleObj;
        $this->InitProperties();

        $this->m_articleData = new ArticleData($articleObj->getType(),
        $articleObj->getArticleNumber(),
        $articleObj->getLanguageId());

        foreach ($this->m_articleData->m_columnNames as $property) {
            if ($property[0] != 'F') {
                continue;
            }
            $property = substr($property, 1);
            $this->m_customProperties[strtolower($property)] = array($property);
        }
        $this->m_customProperties['year'] = 'getCreationYear';
        $this->m_customProperties['mon'] = 'getCreationMonth';
        $this->m_customProperties['wday'] = 'getCreationWeekDay';
        $this->m_customProperties['mday'] = 'getCreationMonthDay';
        $this->m_customProperties['yday'] = 'getCreationYearDay';
        $this->m_customProperties['hour'] = 'getCreationHour';
        $this->m_customProperties['min'] = 'getCreationMinute';
        $this->m_customProperties['sec'] = 'getCreationSecond';
        $this->m_customProperties['mon_name'] = 'getCreationMonthName';
        $this->m_customProperties['wday_name'] = 'getCreationWeekDayName';
        $this->m_customProperties['template'] = 'getTemplate';
        $this->m_customProperties['comments_enabled'] = 'getCommentsEnabled';
        $this->m_customProperties['on_front_page'] = 'getOnFrontPage';
        $this->m_customProperties['on_section_page'] = 'getOnSectionPage';
        $this->m_customProperties['is_published'] = 'getIsPublished';
        $this->m_customProperties['is_public'] = 'getIsPublic';
        $this->m_customProperties['is_indexed'] = 'getIsIndexed';
        $this->m_customProperties['publication'] = 'getPublication';
        $this->m_customProperties['issue'] = 'getIssue';
        $this->m_customProperties['section'] = 'getSection';
        $this->m_customProperties['language'] = 'getLanguage';
        $this->m_customProperties['owner'] = 'getOwner';
        $this->m_customProperties['defined'] = 'defined';
        $this->m_customProperties['has_attachments'] = 'hasAttachments';
        $this->m_customProperties['image_index'] = 'getImageIndex';
        $this->m_customProperties['comment_count'] =  'getCommentCount';
        $this->m_customProperties['content_accessible'] = 'isContentAccessible';
        $this->m_customProperties['image'] = 'getImage';
    } // fn __construct


    final public function __get($p_property)
    {
        $property = $this->translateProperty($p_property);
        if ($this->m_state == 'type_name_error') {
            $this->m_state = null;
            return null;
        }

        if ($property == 'type' && $this->m_state == null) {
            $this->m_state = 'type';
            return $this;
        }

        if ($this->m_state == 'type') {
            if (strcasecmp($this->m_dbObject->getType(), $property) != 0) {
                $this->m_state = 'type_name_error';
            } else {
                $this->m_state = null;
            }
            return $this;
        }

        if (is_null($this->m_state) && $property != 'image_index'
        && strncasecmp('image', $property, 5) == 0 && strlen($property) > 5) {
            $imageNo = substr($property, 5);
            $articleImage = new ArticleImage($this->m_dbObject->getArticleNumber(), null, $imageNo);
            if (!$articleImage->exists()) {
                $this->trigger_invalid_property_error($p_property);
                return new MetaImage();
            }
            return new MetaImage($articleImage->getImageId());
        }

        try {
            if (array_search($property, $this->m_properties)) {
                $methodName = $this->m_getPropertyMethod;
                return $this->m_dbObject->$methodName($property);
            } elseif (array_key_exists($property, $this->m_customProperties)) {
                return $this->getCustomProperty($property);
            } else {
                $this->trigger_invalid_property_error($p_property);
                return null;
            }
        } catch (InvalidPropertyException $e) {
            $this->trigger_invalid_property_error($p_property);
            return null;
        }
    } // fn __get


    public function subtitle_url_id($p_fieldName) {
        $property = $this->translateProperty($p_fieldName);
        return 'st-'.$property;
    }


    public function current_subtitle_no($p_fieldName) {
        $property = $this->translateProperty($p_fieldName);
        if (isset($this->m_customProperties[$property])
        && is_array($this->m_customProperties[$property])) {
            $dbProperty = $this->m_customProperties[$property][0];
            $articleFieldType = new ArticleTypeField($this->type_name, $dbProperty);
            if ($articleFieldType->getType() == 'mediumblob') {
                $subtitleId = $this->subtitle_url_id($p_fieldName);
                return CampTemplate::singleton()->context()->default_url->get_parameter($subtitleId);
            }
        }
        return null;
    }


    protected function getCustomProperty($p_property)
    {
        $property = $this->translateProperty($p_property);
        if (isset($this->m_customProperties[$property])
        && is_array($this->m_customProperties[$property])) {
            try {
                $dbProperty = $this->m_customProperties[$property][0];
                $fieldValue = $this->m_articleData->getProperty('F'.$dbProperty);
                $articleFieldType = new ArticleTypeField($this->type_name, $dbProperty);
                if ($articleFieldType->getType() == 'mediumblob') {
                    if (is_null($this->getContentCache($property))) {
                        $subtitleId = $this->subtitle_url_id($property);
                        $subtitleNo = CampTemplate::singleton()->context()->default_url->get_parameter($subtitleId);
                        if (is_null($subtitleNo)) {
                            $subtitleNo = 0;
                        } elseif ($subtitleNo == 'all') {
                            $subtitleNo = null;
                        }
                        $bodyField = new MetaArticleBodyField($fieldValue,
                                         $articleFieldType->getPrintName(), $this->name, $subtitleNo);
                        $this->setContentCache($property, $bodyField);
                    }
                    $fieldValue = $this->getContentCache($property);
                }
                return $fieldValue;
            } catch (InvalidPropertyException $e) {
                // do nothing; will throw another exception with original property field name
            }
            throw new InvalidPropertyException(get_class($this->m_dbObject), $p_property);
        }
        return parent::getCustomProperty($p_property);
    }


    private function getContentCache($p_property)
    {
        $p_property = $this->translateProperty($p_property);
        if (is_null($this->m_contentCache)
        || !isset($this->m_contentCache[$p_property])) {
            return null;
        }
        return $this->m_contentCache[$p_property];
    }


    private function setContentCache($p_property, $p_value)
    {
        $p_property = $this->translateProperty($p_property);
        $this->m_contentCache[$p_property] = $p_value;
    }


    protected function getCreationYear()
    {
        $creation_timestamp = strtotime($this->m_dbObject->getProperty('UploadDate'));
        $creation_date_time = getdate($creation_timestamp);
        return $creation_date_time['year'];
    }


    protected function getCreationMonth()
    {
        $creation_timestamp = strtotime($this->m_dbObject->getProperty('UploadDate'));
        $creation_date_time = getdate($creation_timestamp);
        return $creation_date_time['mon'];
    }


    protected function getCreationWeekDay()
    {
        $creation_timestamp = strtotime($this->m_dbObject->getProperty('UploadDate'));
        $creation_date_time = getdate($creation_timestamp);
        return $creation_date_time['wday'];
    }


    protected function getCreationMonthDay()
    {
        $creation_timestamp = strtotime($this->m_dbObject->getProperty('UploadDate'));
        $creation_date_time = getdate($creation_timestamp);
        return $creation_date_time['mday'];
    }


    protected function getCreationYearDay()
    {
        $creation_timestamp = strtotime($this->m_dbObject->getProperty('UploadDate'));
        $creation_date_time = getdate($creation_timestamp);
        return $creation_date_time['yday'];
    }


    protected function getCreationHour()
    {
        $creation_timestamp = strtotime($this->m_dbObject->getProperty('UploadDate'));
        $creation_date_time = getdate($creation_timestamp);
        return $creation_date_time['hours'];
    }


    protected function getCreationMinute()
    {
        $creation_timestamp = strtotime($this->m_dbObject->getProperty('UploadDate'));
        $creation_date_time = getdate($creation_timestamp);
        return $creation_date_time['minutes'];
    }


    protected function getCreationSecond()
    {
        $creation_timestamp = strtotime($this->m_dbObject->getProperty('UploadDate'));
        $creation_date_time = getdate($creation_timestamp);
        return $creation_date_time['seconds'];
    }


    protected function getCreationMonthName() {
        $dateTime = new MetaDateTime($this->m_dbObject->getProperty('UploadDate'));
        return $dateTime->getMonthName();
    }


    protected function getCreationWeekDayName() {
        $dateTime = new MetaDateTime($this->m_dbObject->getProperty('UploadDate'));
        return $dateTime->getWeekDayName();
    }


    protected function getOnFrontPage()
    {
        return (int)($this->m_dbObject->getProperty('OnFrontPage') == 'Y');
    }


    protected function getOnSectionPage()
    {
        return (int)($this->m_dbObject->getProperty('OnSection') == 'Y');
    }


    protected function getIsPublished()
    {
        return (int)($this->m_dbObject->getProperty('Published') == 'Y');
    }


    protected function getIsPublic()
    {
        return (int)($this->m_dbObject->getProperty('Public') == 'Y');
    }


    protected function getIsIndexed()
    {
        return (int)($this->m_dbObject->getProperty('IsIndexed') == 'Y');
    }


    protected function getPublication()
    {
        return new MetaPublication($this->m_dbObject->getProperty('IdPublication'));
    }


    protected function getIssue()
    {
        return new MetaIssue($this->m_dbObject->getProperty('IdPublication'),
        $this->m_dbObject->getProperty('IdLanguage'),
        $this->m_dbObject->getProperty('NrIssue'));
    }


    protected function getSection()
    {
        return new MetaSection($this->m_dbObject->getProperty('IdPublication'),
        $this->m_dbObject->getProperty('NrIssue'),
        $this->m_dbObject->getProperty('IdLanguage'),
        $this->m_dbObject->getProperty('NrSection'));
    }


    protected function getLanguage()
    {
        return new MetaLanguage($this->m_dbObject->getProperty('IdLanguage'));
    }


    protected function getOwner()
    {
        return new MetaUser($this->m_dbObject->getProperty('IdUser'));
    }


    protected function getTemplate()
    {
        $articleSection = new Section($this->m_dbObject->getProperty('IdPublication'),
        $this->m_dbObject->getProperty('NrIssue'),
        $this->m_dbObject->getProperty('IdLanguage'),
        $this->m_dbObject->getProperty('NrSection'));
        if ($articleSection->getArticleTemplateId() > 0) {
            return new MetaTemplate($articleSection->getArticleTemplateId());
        }
        $articleIssue = new Issue($this->m_dbObject->getProperty('IdPublication'),
        $this->m_dbObject->getProperty('IdLanguage'),
        $this->m_dbObject->getProperty('NrIssue'));
        return new MetaTemplate($articleIssue->getArticleTemplateId());
    }


    protected function hasAttachments()
    {
        $attachments = ArticleAttachment::GetAttachmentsByArticleNumber($this->m_dbObject->getProperty('Number'));
        return (int)(sizeof($attachments) > 0);
    }


    protected function getCommentsEnabled()
    {
        $publicationObj = new Publication($this->m_dbObject->getProperty('IdPublication'));
        $articleTypeObj = new ArticleType($this->m_dbObject->getProperty('Type'));
        return $publicationObj->commentsEnabled()
        && $articleTypeObj->commentsEnabled()
        && $this->m_dbObject->commentsEnabled();
    }


    /**
     * Returns the index of the current image inside the article.
     * If the image doesn't belong to the article returns null.
     *
     * @return int
     */
    protected function getImageIndex() {
        $image = CampTemplate::singleton()->context()->image;
        if (!$image->defined) {
            return null;
        }
        $articleImage = new ArticleImage($this->m_dbObject->getArticleNumber(),
        $image->number);
        if (!$articleImage->exists()) {
            return null;
        }
        return $articleImage->getImageArticleIndex();
    }


    protected function getCommentCount() {
        return ArticleComment::GetArticleComments($this->m_dbObject->getArticleNumber(),
        $this->m_dbObject->getLanguageId(), 'approved', true);
    }


    protected function isContentAccessible() {
        if ($this->m_dbObject->isPublic()) {
            return (int)true;
        }
        $user = CampTemplate::singleton()->context()->user;
        return (int)($user->defined && $user->subscription->is_valid
        && $user->subscription->has_section(CampTemplate::singleton()->context()->section->number));
    }


    protected function getImage() {
        $context = CampTemplate::singleton()->context();
        if ($context->image->defined) {
            return $context->image;
        }
        $images = ArticleImage::GetImagesByArticleNumber($this->m_dbObject->getArticleNumber());
        if (count($images) == 0) {
            return new MetaImage();
        }
        return new MetaImage($images[0]->getImageId());
    }


    /**
     * Returns true if the article was translated in to the language
     * identified by the given language code
     *
     * @param string $p_language - language code
     * @return bool
     */
    public function translated_to($p_language)
    {
        if (is_string($p_language)) {
            $languages = Language::GetLanguages(null, $p_language);
            if (sizeof($languages) == 0) {
                return (int)false;
            }
            $language = $languages[0];
        } else {
            $language = $p_language;
        }
        $article = new Article($language->getLanguageId(),
        $this->m_dbObject->getArticleNumber());
        return (int)$article->exists();
    }


    /**
     * Returns true if the article keywords field had the given keyword.
     *
     * @param string $p_keyword
     * @return bool
     */
    public function has_keyword($p_keyword) {
        $keywords = $this->m_dbObject->getKeywords();
        return (int)(stristr($keywords, $p_keyword) !== false);
    }


    /**
     * Returns the number of the subtitles of the given article field.
     * Returns null if the field name was invalid or it name a non body field.
     *
     * @param string $p_property - article field name
     * @return int
     */
    public function subtitles_count($p_property) {
        try {
            $propertyValue = $this->getCustomProperty($p_property);
            if (!is_a($propertyValue, 'MetaArticleBodyField')) {
                return null;
            }
            return $propertyValue->subtitles_count;
        } catch (InvalidPropertyException $e) {
            // do nothing, return null
        }
        return null;
    }


    /**
     * Returns true if the article had an attached image identified
     * by the given article internal index.
     *
     * @param int $p_imageIndex - the index of the image attachment in the article
     * @return bool
     */
    public function has_image($p_imageIndex) {
        $articleImage = new ArticleImage($this->m_dbObject->getArticleNumber(),
        null, $p_imageIndex);
        return (int)$articleImage->exists();
    }


    /**
     * Returns an image meta object corresponding to the given index
     * of the image attachment inside the article. Returns an empty
     * meta image object if the image did not exist.
     *
     * @param int $p_imageIndex - the index of the image attachment in the article
     * @return MetaImage
     */
    public function image($p_imageIndex) {
        $articleImage = new ArticleImage($this->m_dbObject->getArticleNumber(),
        null, $p_imageIndex);
        if (!$articleImage->exists()) {
            return new MetaImage();
        }
        return new MetaImage($articleImage->getImageId());
    }
} // class MetaArticle

?>