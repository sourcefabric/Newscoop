DROP TRIGGER IF EXISTS author_update;
DROP TRIGGER IF EXISTS comment_insert;
DROP PROCEDURE IF EXISTS set_user_points;
DROP PROCEDURE IF EXISTS set_user_points_all;

DELIMITER $
CREATE PROCEDURE set_user_points(IN user INT)
BEGIN
	DECLARE author INT;
	DECLARE posts INT DEFAULT 0;
	DECLARE comments INT DEFAULT 0;
	DECLARE total_points INT DEFAULT 0;

   	SELECT author_id INTO author FROM `liveuser_users` WHERE Id = user;
	IF author > 0 THEN
        SELECT COUNT(fk_article_number) INTO posts FROM ArticleAuthors aa INNER JOIN Articles a ON aa.fk_article_number = a.Number WHERE fk_author_id = author AND a.Type IN ('news', 'blog');
    END IF;
    
    SELECT COUNT(cc.id) INTO comments FROM comment_commenter cc INNER JOIN comment c ON cc.id = c.fk_comment_commenter_id WHERE cc.fk_user_id = user;
    
    SET total_points = comments + posts;
    UPDATE `liveuser_users` SET `points` = total_points WHERE `Id` = user;
END;
$

CREATE PROCEDURE set_user_points_all()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE user INT;
    DECLARE users CURSOR FOR SELECT Id FROM liveuser_users;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN users;
    
    update_loop: LOOP
        FETCH users INTO user;
        IF done THEN
            LEAVE update_loop;
        END IF;
        CALL set_user_points(user);
    END LOOP;
    
    CLOSE users;
END;
$

CREATE TRIGGER author_update AFTER INSERT ON ArticleAuthors
FOR EACH ROW
BEGIN
    DECLARE user INT;
    SELECT Id INTO user FROM liveuser_users WHERE `author_id` = NEW.fk_author_id;
    IF user > 0 THEN
        CALL set_user_points(user);
    END IF;
END;
$

CREATE TRIGGER comment_insert AFTER INSERT ON comment
FOR EACH ROW
BEGIN
    DECLARE user INT;
    SELECT fk_user_id INTO user FROM comment_commenter WHERE id = NEW.fk_comment_commenter_id;
    IF user > 0 THEN
        CALL set_user_points(user);
    END IF;
END;
$

DELIMITER ;
