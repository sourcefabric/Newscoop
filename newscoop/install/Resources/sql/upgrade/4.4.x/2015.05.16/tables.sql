UPDATE `Articles` AS a LEFT JOIN Issues AS i ON a.`NrIssue`= i.Number  SET `issue_id`= i.id;
UPDATE `Articles` AS a LEFT JOIN Sections AS s ON a.`NrSection` = s.Number SET `section_id` = s.id;