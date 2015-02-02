<?php

require_once 'ListObject.php';

/**
 * ArticleTopicsList class
 *
 */
class ArticleTopicsList extends ListObject
{
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
        $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
        $cacheKey = $cacheService->getCacheKey(array('ArticleTopicsList', implode('-', $this->m_constraints), implode('-', $this->m_order), $p_start, $p_limit, $p_count), 'article');
        if ($cacheService->contains($cacheKey)) {
            $metaTopicsList = $cacheService->fetch($cacheKey);
        } else {
            $articleTopicsList = ArticleTopic::GetList($this->m_constraints, $this->m_order, $p_start, $p_limit, $p_count);
            $metaTopicsList = array();
            foreach ($articleTopicsList as $topic) {
                $metaTopicsList[] = new MetaTopic($topic);
            }

            $cacheService->save($cacheKey, $metaTopicsList);
        }

        return $metaTopicsList;
    }

    /**
     * Processes list constraints passed in an array.
     *
     * @param  array $p_constraints
     * @return array
     */
    protected function ProcessConstraints(array $p_constraints)
    {
        return array();
    }

    /**
     * Processes order constraints passed in an array.
     *
     * @param  array $p_order
     * @return array
     */
    protected function ProcessOrder(array $p_order)
    {
        return array();
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
        $parameters = array();
        foreach ($p_parameters as $parameter=>$value) {
            $parameter = strtolower($parameter);
            switch ($parameter) {
                case 'length':
                case 'columns':
                case 'name':
                    if ($parameter == 'length' || $parameter == 'columns') {
                        $intValue = (int) $value;
                        if ("$intValue" != $value || $intValue < 0) {
                            CampTemplate::singleton()->trigger_error("invalid value $value of parameter $parameter in statement list_article_topics");
                        }
                        $parameters[$parameter] = (int) $value;
                    } else {
                        $parameters[$parameter] = $value;
                    }
                    break;
                case 'root':
                    $arrayTopics = explode(';',(string) $value);
                    for ($i = 0, $count = count($arrayTopics); $i < $count; $i++) {
                        $metaTopic = new MetaTopic($arrayTopics[$i]);
                        if (!$metaTopic->defined) {
                            CampTemplate::singleton()->trigger_error("invalid value " . $arrayTopics[$i] . " of parameter $parameter in statement list_article_topics");

                            return array();
                        }
                        $operator = new Operator('is', 'integer');
                        $this->m_constraints[] = new ComparisonOperation('roottopic', $operator, $metaTopic->identifier);
                    }
                    break;
                default:
                    CampTemplate::singleton()->trigger_error("invalid parameter $parameter in list_article_topics", $p_smarty);
            }
        }

        $operator = new Operator('is', 'integer');
        $context = CampTemplate::singleton()->context();
        if (!$context->article->defined) {
            CampTemplate::singleton()->trigger_error("undefined environment attribute 'Article' in statement list_article_topics");

            return array();
        }
        $this->m_constraints[] = new ComparisonOperation('nrarticle', $operator,
                                                         $context->article->number);

        return $parameters;
    }
}
