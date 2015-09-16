INSERT INTO output_publication (fk_output_id, fk_publication_id, fk_language_id, fk_theme_path_id) SELECT DISTINCT 1, IdPublication, IdLanguage, NULL FROM Issues;
