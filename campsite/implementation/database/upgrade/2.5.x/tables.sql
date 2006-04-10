-- For new Article type features - ability
-- to rename, translate, reorder, and hide them.
CREATE TABLE data_type_fields (
    table_name VARCHAR(255),
    field_name VARCHAR(255),
    weight INT,
    is_hidden INT,
    fk_phrase_id INT UNSIGNED
);


