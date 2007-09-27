<?php
class poll_linker
{
    function getSelected($type, $IdLanguage, $IdPublication=null, $NrIssue=null, $NrSection=null, $NrArticle=null)
    {
        global $DB;
        $NrPolls = array();
        
        switch ($type) {
            case 'article':
                $query = "SELECT NrPoll
                          FROM   mod_poll_article
                          WHERE  NrArticle  = $NrArticle AND
                                 IdLanguage = $IdLanguage";
            break; 
            
            case 'section':
                $query = "SELECT NrPoll
                          FROM   mod_poll_section
                          WHERE  NrSection      = $NrSection        AND
                                 IdPublication  = $IdPublication    AND
                                 NrIssue        = $NrIssue          AND
                                 IdLanguage     = $IdLanguage";
            break;   
            
            case 'issue':
                $query = "SELECT NrPoll
                          FROM   mod_poll_issue
                          WHERE  IdPublication  = $IdPublication    AND
                                 NrIssue        = $NrIssue          AND
                                 IdLanguage     = $IdLanguage";
            break;
            
            case 'publication':
                $query = "SELECT NrPoll
                          FROM   mod_poll_publication
                          WHERE  IdPublication  = $IdPublication    AND
                                 IdLanguage     = $IdLanguage";
            break;
            
            default:
                return array();
            break;
        }
        $res    = sqlQuery($DB['modules'], $query);  
        while ($row = mysql_fetch_array($res)) {
            $NrPolls[$row['NrPoll']] = true; 
        }
        return $NrPolls;            
    }
    
    function selectPoll($type, $IdLanguage, $IdPublication=null, $NrIssue=null, $NrSection=null, $NrArticle=null)
    {
        global $DB;
        $selected = $this->getSelected($type, $IdLanguage, $IdPublication, $NrIssue, $NrSection, $NrArticle);
        $selector = '<select name="NrPolls[]" size="7" multiple>';
        $query = "SELECT m.Number, q.Title
                  FROM   mod_poll_main AS m, 
                         mod_poll_questions AS q
                  WHERE  m.Number      = q.NrPoll   AND 
                         q.IdLanguage  = $IdLanguage";
        $polls = sqlQuery($DB['modules'], $query);
        
        while ($poll = mysql_fetch_array($polls)) {
            $selector .= "<option value='{$poll['Number']}'";
            if ($selected[$poll['Number']]) $selector .= " selected";
            reset($selected);
            $selector .= ">{$poll['Title']}</option>";
        }
        
        $selector .= '</select>';        
        return $selector;
    }


    function LinkPoll($NrPolls, $type, $IdLanguage, $IdPublication=null, $NrIssue=null, $NrSection=null, $NrArticle=null)
    {
        global $DB;
        switch ($type) {
            case 'article':
                $query[] = "DELETE
                            FROM   mod_poll_article
                            WHERE  NrArticle  = $NrArticle    AND
                                   IdLanguage = $IdLanguage";
            break; 
            
            case 'section':
                $query[] = "DELETE
                            FROM   mod_poll_section
                            WHERE  NrSection      = $NrSection        AND
                                   IdPublication  = $IdPublication    AND
                                   NrIssue        = $NrIssue          AND
                                   IdLanguage     = $IdLanguage";
            break;   
            
            case 'issue':
                $query[] = "DELETE
                            FROM   mod_poll_issue
                            WHERE  IdPublication  = $IdPublication    AND
                                   NrIssue        = $NrIssue          AND
                                   IdLanguage     = $IdLanguage";
            break;
            
            case 'publication':
                $query[] = "DELETE
                            FROM   mod_poll_publication
                            WHERE  IdPublication  = $IdPublication    AND
                                   IdLanguage     = $IdLanguage";
            break;   
        }

        if (is_array($NrPolls)) {
            while (list($key, $NrPoll) = each($NrPolls)) {
                switch ($type) {
                case 'article':
                    $query[] = "INSERT
                                INTO   mod_poll_article
                                SET    NrPoll     = $NrPoll,
                                       IdLanguage = $IdLanguage,
                                       NrArticle  = $NrArticle";
                break; 
                
                case 'section':
                    $query[] = "INSERT
                                INTO   mod_poll_section
                                SET    NrPoll         = $NrPoll,
                                       IdLanguage     = $IdLanguage,
                                       NrSection      = $NrSection,
                                       IdPublication  = $IdPublication,
                                       NrIssue        = $NrIssue";
                break;   
                
                case 'issue':
                    $query[] = "INSERT
                                INTO   mod_poll_issue
                                SET    NrPoll         = $NrPoll,
                                       IdLanguage     = $IdLanguage,
                                       IdPublication  = $IdPublication,
                                       NrIssue        = $NrIssue";  
                break;
                
                case 'publication':
                    $query[] = "INSERT
                                INTO   mod_poll_publication
                                SET    NrPoll         = $NrPoll,
                                       IdLanguage     = $IdLanguage,
                                       IdPublication  = $IdPublication";
                break;
                }
            }
        }
        sqlQuery($DB['modules'], $query);
    }
}
?>