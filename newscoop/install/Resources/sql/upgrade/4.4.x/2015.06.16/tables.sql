DELETE FROM `SnippetTemplateField` WHERE TemplateId = 1;
INSERT INTO `SnippetTemplateField` (`Id`, `Name`, `Type`, `Scope`, `Required`, `TemplateId`)
VALUES
    (1, 'URL', 'url', 'frontend', 1, 1),
    (2, 'Endpoint', 'text', 'frontend', 0, 1),
    (3, 'maxwidth', 'integer', 'frontend', 0, 1),
    (4, 'provider_url', 'url', 'backend', 0, 1),
    (5, 'description', 'textarea', 'backend', 0, 1),
    (6, 'title', 'text', 'backend', 0, 1),
    (7, 'type', 'text', 'backend', 0, 1),
    (8, 'thumbnail_width', 'integer', 'backend', 0, 1),
    (9, 'height', 'integer', 'backend', 0, 1),
    (10, 'width', 'integer', 'backend', 0, 1),
    (11, 'html', 'textarea', 'backend', 0, 1),
    (12, 'author_name', 'text', 'backend', 0, 1),
    (13, 'version', 'text', 'backend', 0, 1),
    (14, 'provider_name', 'text', 'backend', 0, 1),
    (15, 'thumbnail_url', 'url', 'backend', 0, 1),
    (16, 'thumbnail_height', 'integer', 'backend', 0, 1),
    (17, 'author_url', 'url', 'backend', 0, 1);
