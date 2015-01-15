<?php

require_once 'ListObject.php';
require_once($GLOBALS['g_campsiteDir'] . '/classes/CampCache.php');
require_once($GLOBALS['g_campsiteDir'] . '/classes/Article.php');

/**
 * ArticlesList class
 *
 */
class ArticlesList extends ListObject
{
    private static $s_parameters = array('number'=>array('field'=>'Number', 'type'=>'integer'),
                                         'name'=>array('field'=>'Name', 'type'=>'string'),
                                         'keyword'=>array('field'=>null, 'type'=>'string'),
                                         'onfrontpage'=>array('field'=>'OnFrontPage',
                                                              'type'=>'switch'),
                                         'onsection'=>array('field'=>'OnSection',
                                                            'type'=>'switch'),
                                         'upload_date'=>array('field'=>'UploadDate',
                                                              'type'=>'date'),
                                         'publish_date'=>array('field'=>'PublishDate',
                                                              'type'=>'date'),
                                         'publish_datetime' => array(
                                             'field' => 'PublishDate',
                                             'type' => 'datetime',
                                         ),
                                         'public'=>array('field'=>'Public',
                                                         'type'=>'switch'),
                                         'type'=>array('field'=>'Type',
                                                       'type'=>'string'),
                                         'matchalltopics'=>array('field'=>null,
                                                                 'type'=>'void'),
                                         'matchanytopic'=>array('field'=>null,
                                                                'type'=>'void'),
                                         'topic'=>array('field'=>null,
                                                        'type'=>'topic'),
                                         'topic_strict' => array('field' => null,
                                                                 'type' => 'topic'),
                                         'reads'=>array('field'=>null, 'type'=>'integer'),
                                         'author'=>array('field'=>null, 'type'=>'string'),
                                         'section'=>array('field'=>'NrSection', 'type'=>'integer'),
                                         'issue'=>array('field'=>'NrIssue', 'type'=>'integer'),
                                         'insection' => array(
                                             'field' => 'insection',
                                             'type' => 'string',
                                         ),
                                   );

    private static $s_orderFields = array(
                                          'bynumber',
                                          'byname',
                                          'bydate',
                                          'bycreationdate',
                                          'bypublishdate',
                                          'bylastupdate',
                                          'bypopularity',
                                          'bypublication',
                                          'byissue',
                                          'bysection',
                                          'bylanguage',
                                          'bysectionorder',
                                          'bycomments',
                                          'bylastcomment',
                                          'bykeywords',
                                    );

    private static $s_articleTypes = null;

    private static $s_dynamicFields = null;

    const CONSTRAINT_ATTRIBUTE_NAME = 1;
    const CONSTRAINT_DYNAMIC_FIELD = 4;
    const CONSTRAINT_OPERATOR = 2;
    const CONSTRAINT_VALUE = 3;

    private $m_ignoreIssue = false;

    private $m_ignoreSection = false;

  /**
     * Creates the list of objects. Sets the parameter $p_hasNextElements to
     * true if this list is limited and elements still exist in the original
     * list (from which this was truncated) after the last element of this
     * list.
     *
     * @param  int   $p_start
     * @param  int   $p_limit
     * @param  array $p_parameters
     * @param  int   &$p_count
     * @return array
     */
    protected function CreateList($p_start = 0, $p_limit = 0, array $p_parameters, &$p_count)
    {
        $articlesList = Article::GetList($this->m_constraints, $this->m_order, $p_start, $p_limit, $p_count, false, false);
        $metaArticlesList = array();
        foreach ($articlesList as $article) {
            $metaArticlesList[] = new MetaArticle($article['language_id'], $article['number']);
        }

        return $metaArticlesList;
    }

    /**
     * Processes list constraints passed in an array.
     *
     * @param  array $p_constraints
     * @return array
     */
    protected function ProcessConstraints(array $p_constraints)
    {
        $parameters = array();
        $state = 1;
        $attribute = null;
        $articleTypeName = null;
        $operator = null;
        $value = null;
        $switchTypeHint = false;
        $context = CampTemplate::singleton()->context();
        for ($index = 0; $index < count($p_constraints); $index++) {
            $word = $p_constraints[$index];

            switch ($state) {
                case self::CONSTRAINT_ATTRIBUTE_NAME: // reading the parameter name
                    $attribute = strtolower($word);
                    if (!array_key_exists($attribute, ArticlesList::$s_parameters)) {
                        // not a static field; is it a article type name?
                        self::ReadArticleTypes();
                        if (array_key_exists($attribute, self::$s_articleTypes)) {
                            $articleTypeName = self::$s_articleTypes[$attribute];
                            $state = self::CONSTRAINT_DYNAMIC_FIELD;
                            break;
                        }

                        // not an article type name; is it a dynamic field name?
                        $dynamicFields = self::GetDynamicFields($articleTypeName, $attribute);
                        if (count($dynamicFields) > 0) {
                            if (count($dynamicFields) == 1) {
                                $type = $dynamicFields[0]->getGenericType();
                            } else {
                                $type = null;
                                foreach ($dynamicFields as $field) {
                                    $switchTypeHint = $switchTypeHint || $field->getType() == ArticleTypeField::TYPE_SWITCH;
                                    if (is_null($type)) {
                                        $type = $field->getGenericType();
                                    } elseif ($type != $field->getGenericType()) {
                                        $type = 'string';
                                    }
                                }
                            }
                            $state = self::CONSTRAINT_OPERATOR;
                            break;
                        }

                        // unknown attribute
                        CampTemplate::singleton()->trigger_error("invalid attribute $word in statement list_articles, constraints parameter");

                        return false;
                    } else {
                        $type = ArticlesList::$s_parameters[$attribute]['type'];
                    }
                    if ($attribute == 'keyword') {
                        $operator = new Operator('is', 'string');
                        $state = self::CONSTRAINT_VALUE;
                    } elseif ($attribute == 'matchalltopics' || $attribute == 'matchanytopic') {
                        if ($attribute == 'matchalltopics') {
                            $operator = new Operator('is', 'boolean');
                            $comparisonOperation = new ComparisonOperation($attribute, $operator, 'true');
                            $parameters[] = $comparisonOperation;
                        }
                        $state = self::CONSTRAINT_ATTRIBUTE_NAME;
                    } else {
                        $state = self::CONSTRAINT_OPERATOR;
                    }
                    $this->m_ignoreIssue = $this->m_ignoreIssue || $attribute == 'issue';
                    $this->m_ignoreSection = $this->m_ignoreSection || $attribute == 'section';

                    if ($attribute == 'onfrontpage' || $attribute == 'onsection') {
                        if (($index + 1) < count($p_constraints)) {
                            try {
                                $operator = new Operator($p_constraints[$index+1], 'switch');
                            } catch (InvalidOperatorException $e) {
                                $operator = new Operator('is', 'switch');
                                $comparisonOperation = new ComparisonOperation($attribute, $operator, 'on');
                                $parameters[] = $comparisonOperation;
                                $state = self::CONSTRAINT_ATTRIBUTE_NAME;
                            }
                        } else {
                            $operator = new Operator('is', 'switch');
                            $comparisonOperation = new ComparisonOperation($attribute, $operator, 'on');
                            $parameters[] = $comparisonOperation;
                            $state = self::CONSTRAINT_ATTRIBUTE_NAME;
                        }
                    }
                    break;
                case self::CONSTRAINT_DYNAMIC_FIELD:
                    $attribute = strtolower($word);
                    $dynamicFields = self::GetDynamicFields($articleTypeName, $attribute);
                    if (count($dynamicFields) > 0) {
                        $type = $dynamicFields[0]->getGenericType();
                        $state = self::CONSTRAINT_OPERATOR;
                        break;
                    }
                    CampTemplate::singleton()->trigger_error("invalid dynamic field $word in statement list_articles, constraints parameter");

                    return false;
                case self::CONSTRAINT_OPERATOR: // reading the operator
                    try {
                        $operator = new Operator($word, $type);
                    } catch (InvalidOperatorException $e) {
                        CampTemplate::singleton()->trigger_error("invalid operator $word of parameter constraints.$attribute in statement list_articles");

                        return false;
                    }
                    $state = self::CONSTRAINT_VALUE;
                    break;
                case self::CONSTRAINT_VALUE: // reading the value to compare against
                    if ($attribute == 'publish_datetime' && $index + 1 < count($p_constraints)) { // add time to date
                        if (preg_match('/^[0-2][0-9]:[0-5][0-9](:[0-5][0-9])?$/', $p_constraints[$index + 1])) {
                            $word .= ' ' . $p_constraints[$index + 1];
                            $index++; // skip next value
                        }
                    }

                    $metaClassName = 'Meta'.ucfirst($type);
                    try {
                        $valueObj = new $metaClassName($word);
                    } catch (InvalidValueException $e) {
                        CampTemplate::singleton()->trigger_error("invalid value $word of parameter constraints.$attribute in statement list_articles");

                        return false;
                    }
                       if ($attribute == 'type') {
                        $word = trim($word);
                           $articleType = new ArticleType($word);
                           if (!$articleType->exists()) {
                            CampTemplate::singleton()->trigger_error("invalid value $word of parameter constraints.$attribute in statement list_articles");

                            return false;
                        }
                        $value = $word;
                    } elseif ($attribute == 'topic') {
                        $em = \Zend_Registry::get('container')->getService('em');
                        $repository = $em->getRepository('Newscoop\NewscoopBundle\Entity\Topic');
                        $context = CampTemplate::singleton()->context();
                        $locale = $context->language->code;
                        $topicObj = $repository->getTopicByIdOrName($word, $locale)->getOneOrNullResult();
                        if (!$topicObj) {
                            CampTemplate::singleton()->trigger_error("invalid value $word of parameter constraints.$attribute in statement list_articles");

                            return false;
                        } else {
                            $value = $topicObj->getTopicId();
                        }
                    } elseif ($attribute == 'author') {
                        if (strtolower($word) == '__current') {
                            $value = $context->article->author->name;
                        } else {
                            $value = $word;
                        }
                       } elseif ($type == 'switch') {
                           $value = (int) (strtolower($word) == 'on');
                       } else {
                           $wordLower = strtolower($word);
                           if ($switchTypeHint && ($wordLower == 'on' || $wordLower == 'off')) {
                               $value = (int) (strtolower($word) == 'on');
                           } else {
                               $value = $word;
                           }
                       }
                       if (!is_null($articleTypeName)) {
                           $attribute = "$articleTypeName.$attribute";
                       }
                       $comparisonOperation = new ComparisonOperation($attribute, $operator, $value);
                       $parameters[] = $comparisonOperation;
                       $state = self::CONSTRAINT_ATTRIBUTE_NAME;
                    $attribute = null;
                    $articleTypeName = null;
                    $type = null;
                    $value = null;
                    break;
            }
        }
        if ($state != self::CONSTRAINT_ATTRIBUTE_NAME) {
            CampTemplate::singleton()->trigger_error("unexpected end of constraints parameter in list_articles");

            return false;
        }

        if (!$this->m_ignoreIssue && $context->issue->defined) {
            $this->m_constraints[] = new ComparisonOperation('NrIssue', new Operator('is', 'integer'),
                                                             $context->issue->number);
        }
        if (!$this->m_ignoreSection && $context->section->defined) {
            $this->m_constraints[] = new ComparisonOperation('NrSection', new Operator('is', 'integer'),
                                                             $context->section->number);
        }

        return $parameters;
    }

    /**
     * Processes order constraints passed in an array.
     *
     * @param  array $p_order
     * @return array
     */
    protected function ProcessOrder(array $p_order)
    {
        $order = array();
        $state = 1;
        foreach ($p_order as $word) {
            switch ($state) {
                case 1: // reading the order field
                    if (array_search(strtolower($word), ArticlesList::$s_orderFields) === false) {
                        $checked_info = Article::CheckCustomOrder($word);
                        if (!$checked_info['status']) {
                            CampTemplate::singleton()->trigger_error("invalid order field $word in list_articles, order parameter");
                        }
                        // ordering by custom fields; runs the same way as other order specs
                        $orderField = $word;
                        $state = 2;
                    } else {
                        $orderField = $word;
                        $state = 2;
                    }
                    break;
                case 2: // reading the order direction
                    if (MetaOrder::IsValid($word)) {
                        $order[] = array('field'=>$orderField, 'dir'=>$word);
                    } else {
                        CampTemplate::singleton()->trigger_error("invalid order $word of attribute $orderField in list_articles, order parameter");
                    }
                    $state = 1;
                    break;
            }
        }
        if ($state != 1) {
            CampTemplate::singleton()->trigger_error("unexpected end of order parameter in list_articles");
        }

        return $order;
    }

    /**
     * Processes the input parameters passed in an array; drops the invalid
     * parameters and parameters with invalid values. Returns an array of
     * valid parameters.
     *
     * @param  array $p_parameters
     * @return array
     */
    protected function ProcessParameters(array $p_parameters)
    {
        self::ReadArticleTypes();
        $parameters = array();
        $parameters['ignore_publication'] = false;
        $parameters['ignore_issue'] = false;
        $parameters['ignore_section'] = false;
        $parameters['ignore_language'] = false;
        foreach ($p_parameters as $parameter=>$value) {
            $parameter = strtolower($parameter);
            switch ($parameter) {
                case 'length':
                case 'columns':
                case 'name':
                case 'constraints':
                case 'order':
                    if ($parameter == 'length' || $parameter == 'columns') {
                        $intValue = (int) $value;
                        if ("$intValue" != $value || $intValue < 0) {
                            CampTemplate::singleton()->trigger_error("invalid value $value of parameter $parameter in statement list_articles");
                        }
                        $parameters[$parameter] = (int) $value;
                    } else {
                        $parameters[$parameter] = $value;
                    }
                    break;
                case 'ignore_publication':
                case 'ignore_issue':
                case 'ignore_section':
                case 'ignore_language':
                    $value = isset($value) && (strtolower($value) != 'false');
                    $parameters[$parameter] = $value;
                    break;
                case 'location':
                    $num = '[-+]?[0-9]+(?:\.[0-9]+)?';
                    if (preg_match("/$num $num, $num $num/", trim($value))) {
                        $parameters[$parameter] = $value;
                    } else {
                        CampTemplate::singleton()->trigger_error("invalid value $value of parameter $parameter in statement list_articles");
                    }
                    break;
                default:
                    $foundField = false;
                    foreach (self::$s_articleTypes as $atype) {
                        if (count($dynaBlaster = self::GetDynamicFields($atype, $parameter))) {
                            $foundField = current($dynaBlaster)->getType();
                            break;
                        }
                    }
                    if (!$foundField) {
                        CampTemplate::singleton()->trigger_error("invalid parameter $parameter in list_articles");
                        break;
                    }
                    switch ($foundField) {
                        case 'complex_date' :
                            $this->m_constraints[] = new ComparisonOperation('complex_date', new Operator('is', 'string'), array($parameter=>$value));
                        break;
                        default: break;
                    }
                    CampTemplate::singleton()->trigger_error("invalid parameter $parameter in list_articles");
            }
        }

        $operator = new Operator('is', 'integer');
        $context = CampTemplate::singleton()->context();
        if ($context->publication->defined && !$parameters['ignore_publication']) {
            $this->m_constraints[] = new ComparisonOperation('IdPublication', $operator,
                                                             $context->publication->identifier);
        }
        if ($context->language->defined && !$parameters['ignore_language']) {
            $this->m_constraints[] = new ComparisonOperation('IdLanguage', $operator,
                                                             $context->language->number);
        }
        $this->m_ignoreIssue = $parameters['ignore_issue'];
        $this->m_ignoreSection = $parameters['ignore_section'];
        if ($context->topic->defined) {
            $this->m_constraints[] = new ComparisonOperation('topic', $operator,
                                                             $context->topic->identifier);
        }
        if (!$context->preview) {
            $this->m_constraints[] = new ComparisonOperation('published', $operator, 'true');
        }

        if (!empty($parameters['location'])) {
            $this->m_constraints[] = new ComparisonOperation('location',
                new Operator('is', 'string'), $parameters['location']);
        }

        return $parameters;
    }

    private static function ReadArticleTypes()
    {
        if (is_null(self::$s_articleTypes)) {
            require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleType.php');
            $articleTypes = ArticleType::GetArticleTypes(true);
            self::$s_articleTypes = array();
            foreach ($articleTypes as $articleType) {
                self::$s_articleTypes[strtolower($articleType)] = $articleType;
            }
        }
    }

    private static function ReadDynamicFields()
    {
        if (is_null(self::$s_dynamicFields)) {
            require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleTypeField.php');
            self::$s_dynamicFields = ArticleTypeField::FetchFields();
        }
    }

    private static function GetDynamicFields($p_articleTypeName = null, $p_fieldName)
    {
        $result = array();
        self::ReadDynamicFields();
        foreach (self::$s_dynamicFields as $dynamicField) {
            if (strtolower($dynamicField->getPrintName()) == strtolower($p_fieldName)
            && (is_null($p_articleTypeName)
            || strtolower($dynamicField->getArticleType()) == strtolower($p_articleTypeName))) {
                $result[] = $dynamicField;
            }
        }

        return $result;
    }
}
