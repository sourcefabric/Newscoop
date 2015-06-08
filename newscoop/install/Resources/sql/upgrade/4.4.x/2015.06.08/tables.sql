UPDATE `Articles` AS a LEFT JOIN Sections AS s ON a.`NrSection` = s.Number AND a.issue_id = s.fk_issue_id SET `section_id` = s.id;
