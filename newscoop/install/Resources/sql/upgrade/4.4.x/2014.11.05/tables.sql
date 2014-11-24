ALTER TABLE Publications DROP INDEX Alias, ADD INDEX IDX_2A49E10CAB83D3A4 (IdDefaultAlias);
DROP INDEX Name ON Publications;
ALTER TABLE
Publications ADD meta_title VARCHAR(255) DEFAULT NULL,
ADD meta_keywords VARCHAR(255) DEFAULT NULL,
ADD meta_description VARCHAR(255) DEFAULT NULL,
DROP TimeUnit, DROP UnitCost,
DROP UnitCostAllLang, DROP Currency,
DROP TrialTime, DROP PaidTime,
CHANGE Id Id INT AUTO_INCREMENT NOT NULL,
CHANGE Name Name VARCHAR(255) NOT NULL,
CHANGE IdDefaultLanguage IdDefaultLanguage int(10),
CHANGE IdDefaultAlias IdDefaultAlias INT DEFAULT NULL,
CHANGE IdURLType IdURLType INT DEFAULT NULL,
CHANGE comments_enabled comments_enabled TINYINT(1) DEFAULT NULL,
CHANGE comments_article_default_enabled comments_article_default_enabled TINYINT(1) DEFAULT NULL,
CHANGE comments_subscribers_moderated comments_subscribers_moderated TINYINT(1) DEFAULT NULL,
CHANGE comments_public_moderated comments_public_moderated TINYINT(1) DEFAULT NULL,
CHANGE comments_public_enabled comments_public_enabled VARCHAR(255) DEFAULT NULL,
CHANGE comments_captcha_enabled comments_captcha_enabled TINYINT(1) DEFAULT NULL,
CHANGE comments_spam_blocking_enabled comments_spam_blocking_enabled TINYINT(1) DEFAULT NULL,
CHANGE comments_moderator_to comments_moderator_to VARCHAR(255) DEFAULT NULL,
CHANGE comments_moderator_from comments_moderator_from VARCHAR(255) DEFAULT NULL,
CHANGE url_error_tpl_id url_error_tpl_id INT DEFAULT NULL,
CHANGE seo seo VARCHAR(255) DEFAULT NULL;

CREATE INDEX IDX_2A49E10CEC194F36 ON Publications (IdDefaultLanguage);
CREATE INDEX Name ON Publications (Name);