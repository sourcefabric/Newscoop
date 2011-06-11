DELIMITER //
DROP FUNCTION IF EXISTS CheckPolygonPoint //
CREATE FUNCTION CheckPolygonPoint(point GEOMETRY, spacing INT, length INT, polygon VARCHAR(20100))
RETURNS INT DETERMINISTIC
-- RETURNS TEXT DETERMINISTIC
-- the tret return variable can be used for debugging
    ppfound:BEGIN

    DECLARE lat, lon REAL;
    DECLARE plat, plon, nlat, nlon REAL DEFAULT 0;
    DECLARE zlatdiff, zlondiff, platdiff, plondiff, nlatdiff, nlondiff REAL DEFAULT 0;
    DECLARE pind INT DEFAULT 0;
    DECLARE nposlat, nposlon, zposlat, zposlon INT DEFAULT 0;
    DECLARE inside INT DEFAULT 0;
    DECLARE maxinner INT DEFAULT 0;
    DECLARE zq, pq, nq INT DEFAULT 0;
    DECLARE qdiff, qdiffmod INT DEFAULT 0;
    DECLARE dprod REAL DEFAULT 0;

--    DECLARE tret TEXT;
    DECLARE totangle INT DEFAULT 0;
    SET totangle = 0;
--    SET tret = "actions: ";

    SET lat = X(point);
    SET lon = Y(point);

    SET nposlat = (2 * spacing) + 1;
    SET nposlon = (3 * spacing) + 1;
    SET maxinner = length - 1;
    SET inside = 0;

    SET zposlat = 1;
    SET zposlon = 1 + spacing;

    SET plat = 0 + SUBSTRING(polygon, zposlat, spacing);
    SET plon = 0 + SUBSTRING(polygon, zposlon, spacing);
    SET platdiff = plat - lat;
    SET plondiff = plon - lon;
    SET zlatdiff = platdiff;
    SET zlondiff = plondiff;

    SET zq = 0;
    IF 0 > zlatdiff THEN
        IF 0 > zlondiff THEN
            SET zq = 5;
        ELSEIF 0 < zlondiff THEN
            SET zq = 7;
        ELSE
            SET zq = 6;
        END IF;
    ELSEIF 0 < zlatdiff THEN
        IF 0 > zlondiff THEN
            SET zq = 3;
        ELSEIF 0 < zlondiff THEN
            SET zq = 1;
        ELSE
            SET zq = 2;
        END IF;
    ELSE
        IF 0 > zlondiff THEN
            SET zq = 4;
        ELSEIF 0 < zlondiff THEN
            SET zq = 0;
        ELSE
            SET inside = 1;
            RETURN inside;
            LEAVE ppfound;
        END IF;
    END IF;

    SET pq = zq;

    SET inside = 0;
    SET pind = 0;
    polygrun: LOOP
        IF pind < maxinner THEN 
            SET nlat = 0 + SUBSTRING(polygon, nposlat, spacing);
            SET nlon = 0 + SUBSTRING(polygon, nposlon, spacing);
            SET nlatdiff = nlat - lat;
            SET nlondiff = nlon - lon;

            SET nq = 0;
            IF 0 > nlatdiff THEN
                IF 0 > nlondiff THEN
                    SET nq = 5;
                ELSEIF 0 < nlondiff THEN
                    SET nq = 7;
                ELSE
                    SET nq = 6;
                END IF;
            ELSEIF 0 < nlatdiff THEN
                IF 0 > nlondiff THEN
                    SET nq = 3;
                ELSEIF 0 < nlondiff THEN
                    SET nq = 1;
                ELSE
                    SET nq = 2;
                END IF;
            ELSE
                IF 0 > nlondiff THEN
                    SET nq = 4;
                ELSEIF 0 < nlondiff THEN
                    SET nq = 0;
                ELSE
                    SET inside = 1;
                    RETURN inside;
                    LEAVE ppfound;
                END IF;
            END IF;
        ELSE
            SET nlatdiff = zlatdiff;
            SET nlondiff = zlondiff;
            SET nq = zq;
        END IF;


        SET qdiff = nq - pq;
        SET qdiffmod = qdiff;
        IF 8 <= qdiffmod THEN
            SET qdiffmod = qdiffmod - 8;
        END IF;
        IF 0 > qdiffmod THEN
            SET qdiffmod = qdiffmod + 8;
        END IF;

        IF 4 != qdiffmod THEN
            IF -4 > qdiff THEN
                SET qdiff = qdiff + 8;
            END IF;
            IF 4 < qdiff THEN
                SET qdiff = qdiff - 8;
            END IF;
            SET totangle = totangle + qdiff;
--            SET tret = CONCAT(tret, ", std:", qdiffmod, qdiff);

        ELSE

            IF NOT (pq % 2) THEN
                SET inside = 1;
                RETURN inside;
                LEAVE ppfound;
            END IF;

            SET dprod = (plondiff * nlatdiff) - (platdiff * nlondiff);
            IF (0 = dprod) THEN
                SET inside = 1;
                RETURN inside;
                LEAVE ppfound;
            END IF;

--            SET tret = CONCAT(tret, ", ", dprod);
            IF 0 < dprod THEN
                SET totangle = totangle + 4;
--                SET tret = CONCAT(tret, ", ", 4);

            ELSE
                SET totangle = totangle - 4;
--                SET tret = CONCAT(tret, ", ", -4);

            END IF;

        END IF;

        SET pind = pind + 1;
        SET nposlat = nposlat + (2 * spacing);
        SET nposlon = nposlon + (2 * spacing);
        SET platdiff = nlatdiff;
        SET plondiff = nlondiff;
        SET pq = nq;

        IF pind >= length THEN
            LEAVE polygrun;
        END IF;
    END LOOP;

    IF 0 != totangle THEN
        SET inside = 1;
    END IF;

--    RETURN tret;
    RETURN inside;

    END //
DELIMITER ;


