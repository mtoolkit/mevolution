-- UP

DROP PROCEDURE IF EXISTS `color_get_list`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `color_get_list`(
  IN `@item_id` INT
)
  BEGIN

    SELECT
      id,
      description,
      inserted,
      updated
    FROM color
      INNER JOIN item_color ON color.id = item_color.color_id
    WHERE item_color.item_id = `@item_id`;

    END;

DROP PROCEDURE IF EXISTS `color_save`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `color_save`(
  IN `@description` VARCHAR(255)
)
  BEGIN
    REPLACE INTO color (description, update_date)
    VALUES (`@description`, CURRENT_TIMESTAMP);

    SELECT LAST_INSERT_ID() AS id;
    END;

DROP PROCEDURE IF EXISTS `company_get`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `company_get`(
  IN `@id` INT
)
  BEGIN

    SELECT
      id,
      `name`,
      link,
      inserted,
      updated
    FROM company
    WHERE id = `@id`;

    END;

DROP PROCEDURE IF EXISTS `company_save`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `company_save`(
  IN `@name` VARCHAR(100),
  IN `@link` TEXT
)
  BEGIN
    REPLACE INTO company (`name`, `link`, update_date)
    VALUES (`@name`, `@link`, CURRENT_TIMESTAMP);

    SELECT LAST_INSERT_ID() AS id;
    END;

DROP PROCEDURE IF EXISTS `country_get`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `country_get`(
  IN `@id` INT
)
  BEGIN

    SELECT
      id,
      `name`,
      currency,
      currency_symbol,
      inserted,
      updated
    FROM country
    WHERE id = `@id`;

    END;

DROP PROCEDURE IF EXISTS `country_save`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `country_save`(
  IN `@name`            VARCHAR(100),
  IN `@currency`        VARCHAR(100),
  IN `@currency_simbol` VARCHAR(10)
)
  BEGIN
    REPLACE INTO country (`name`, `currency`, `currency_simbol`, update_date)
    VALUES (`@name`, `@currency`, `@currency_simbol`, CURRENT_TIMESTAMP);

    SELECT LAST_INSERT_ID() AS id;
    END;

DROP PROCEDURE IF EXISTS `data_get`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `data_get`(
  IN `@item_id`    INT,
  IN `@country_id` INT
)
  BEGIN

    SELECT
      country_id,
      `price`,
      original_price,
      company_id,
      description,
      link,
      inserted,
      updated
    FROM `data`
    WHERE id = `@id`;

    END;

DROP PROCEDURE IF EXISTS `data_save`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `data_save`(
  IN `@price`          FLOAT,
  IN `@original_price` FLOAT,
  IN `@link`           TEXT,
  IN `@company_id`     INT,
  IN `@country_id`     INT,
  IN `@item_id`        INT
)
  BEGIN
    REPLACE INTO `data` (
      `price`,
      `original_price`,
      `link`,
      `company_id`,
      `country_id`,
      `item_id`,
      `update_date`
    )
    VALUES (
      `@price`,
      `@original_price`,
      `@link`,
      `@company_id`,
      `@country_id`,
      `@item_id`,
      CURRENT_TIMESTAMP
    );

    SELECT LAST_INSERT_ID() AS id;
    END;

DROP PROCEDURE IF EXISTS `item_color_save`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `item_color_save`(
  IN `@item_id`  INT,
  IN `@color_id` INT
)
  BEGIN
    REPLACE INTO item_color (`item_id`, `color_id`)
    VALUES (`@item_id`, `@color_id`);
    END;

DROP PROCEDURE IF EXISTS `item_get`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `item_get`(
  IN `@id` INT
)
  BEGIN

    SELECT
      id,
      `name`,
      brand,
      category,
      collection,
      season,
      inserted,
      updated
    FROM `item`
    WHERE id = `@id`;

    END;

DROP PROCEDURE IF EXISTS `item_save`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `item_save`(
  IN `@name`       FLOAT,
  IN `@brand`      FLOAT,
  IN `@category`   TEXT,
  IN `@season`     INT,
  IN `@collection` INT
)
  BEGIN
    REPLACE INTO item (
      `name`,
      `brand`,
      `category`,
      `season`,
      `collection`,
      `update_date`
    )
    VALUES (
      `@name`,
      `@brand`,
      `@category`,
      `@season`,
      `@collection`,
      CURRENT_TIMESTAMP
    );

    SELECT LAST_INSERT_ID() AS id;
    END;

DROP PROCEDURE IF EXISTS `item_size_save`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `item_size_save`(
  IN `@item_id` INT,
  IN `@size_id` INT
)
  BEGIN
    REPLACE INTO item_size (`item_id`, `size_id`)
    VALUES (`@item_id`, `@size_id`);
    END;

DROP PROCEDURE IF EXISTS `item_tag_save`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `item_tag_save`(
  IN `@item_id` INT,
  IN `@tag_id`  INT
)
  BEGIN
    REPLACE INTO item_tag (`item_id`, `tag_id`)
    VALUES (`@item_id`, `@tag_id`);
    END;

DROP PROCEDURE IF EXISTS `search_get_item_list`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `search_get_item_list`(
  IN `@country_id` INT,
  IN `@search` VARCHAR(500),
  IN `@page_number` INT,
  IN `@page_size` INT
)
  BEGIN

    DECLARE `@pattern` VARCHAR(500);

    SET `@pattern`=REPLACE( `@search`, ' ', '|' );

    DROP TABLE IF EXISTS strings;

    CREATE TEMPORARY TABLE IF NOT EXISTS counted_item
    (
      counted_item_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
      `item_id` INT NOT NULL,
      `rank` FLOAT NOT NULL
    );

    # Salva gli eventi selezionati nella tabella temporanea
    INSERT INTO counted_item
    (
      item_id,
      rank
    )
      SELECT
        item.id
        , (
            occurrences_count(`@search`, item.name) * 0.7
            + occurrences_count(`@search`, item.season) * 0.3
            + occurrences_count(`@search`, item.category) * 0.5
            + occurrences_count(`@search`, item.collection) * 0.4
            + occurrences_count(`@search`, item.brand) * 0.5

            + occurrences_count(`@search`, color.description) * 0.5

            + occurrences_count(`@search`, `size`.eur) * 0.35
            + occurrences_count(`@search`, `size`.uk) * 0.35
            + occurrences_count(`@search`, `size`.us) * 0.35

            + occurrences_count(`@search`, tag.description) * 0.5

            + occurrences_count(`@search`, `data`.description) * 0.2
          ) AS rank
      FROM item
        LEFT OUTER JOIN item_tag ON item.id=item_tag.item_id
        LEFT OUTER JOIN tag ON tag.id=item_tag.tag_id

        LEFT OUTER JOIN item_color ON item.id=item_color.item_id
        LEFT OUTER JOIN color ON color.id=item_color.color_id

        LEFT OUTER JOIN item_size ON item.id=item_size.item_id
        LEFT OUTER JOIN `size` ON `size`.id=item_size.size_id

        LEFT OUTER JOIN `data` ON item.id=`data`.item_id

        LEFT OUTER JOIN country ON country.id=`data`.country_id
      WHERE country.id=`@country_id`
      ORDER BY rank, item.updated, `data`.updated DESC
    ;

    # selects the paged items
    SELECT *
    FROM counted_item
    WHERE counted_item.item_id>(`@page_number`*`@page_size`)
          AND counted_item.item_id<=(`@page_number`*`@page_size`)+`@page_size`;

    END;

DROP PROCEDURE IF EXISTS `size_get`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `size_get`(
  IN `@id` INT
)
  BEGIN

    SELECT
      id,
      eur,
      uk,
      us,
      inserted,
      updated
    FROM size
    WHERE id = `@id`;

    END;

DROP PROCEDURE IF EXISTS `size_get_list`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `size_get_list`(
  IN `@item_id` INT
)
  BEGIN

    SELECT
      id,
      eur,
      uk,
      us,
      inserted,
      updated
    FROM size
      INNER JOIN item_size ON size.id = item_size.color_id
    WHERE item_size.item_id = `@item_id`;

    END;

DROP PROCEDURE IF EXISTS `size_save`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `size_save`(
  IN `@eur` VARCHAR(255),
  IN `@uk`  VARCHAR(255),
  IN `@us`  VARCHAR(255)
)
  BEGIN
    REPLACE INTO size (`eur`, `uk`, `us`, update_date)
    VALUES (`@eur`, `@uk`, `@us`, CURRENT_TIMESTAMP);

    SELECT LAST_INSERT_ID() AS id;
    END;

DROP PROCEDURE IF EXISTS `tag_get`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `tag_get`(
  IN `@id` INT
)
  BEGIN

    SELECT
      id,
      description,
      inserted,
      updated
    FROM size
    WHERE id = `@id`;

    END;

DROP PROCEDURE IF EXISTS `tag_get_list`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `tag_get_list`(
  IN `@item_id` INT
)
  BEGIN

    SELECT
      id,
      description,
      inserted,
      updated
    FROM tag
      INNER JOIN item_tag ON tag.id = item_tag.color_id
    WHERE item_tag.item_id = `@item_id`;

    END;

DROP PROCEDURE IF EXISTS `tag_save`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `tag_save`(
  IN `@description` VARCHAR(255)
)
  BEGIN
    REPLACE INTO tag (description, update_date)
    VALUES (`@description`, CURRENT_TIMESTAMP);

    SELECT LAST_INSERT_ID() AS id;
    END;

--
-- Functions
--
DROP FUNCTION IF EXISTS `occurrences_count`;
CREATE DEFINER=`root`@`localhost` FUNCTION `occurrences_count`(
  `@search` VARCHAR(8000),
  `@text` VARCHAR(8000)
) RETURNS float
  BEGIN

    DECLARE _occurrences_to_returns float;
    DECLARE _occurrences INT;
    DECLARE _i INT;
    DECLARE `_substring` VARCHAR(500);
    DECLARE _separator VARCHAR(10);
    DECLARE _correction INT;

    SET _separator=" ";

    -- Counts the occurences of the delimiter
    SET _occurrences= LENGTH(`@search`) - LENGTH(REPLACE(`@search`, _separator, ''))+1;

    -- Prepares the table of the results
    CREATE TEMPORARY TABLE IF NOT EXISTS Strings (
        pos INT
      , `value` VARCHAR(500) NOT NULL
      , `position` INT NOT NULL
      , `length` INT NOT NULL
      , PRIMARY KEY ( `value`, `position` ) );
    DELETE FROM Strings;

    -- Iterate for every splitter words found
    SET _i=0;
    ForIteration: LOOP
      SET _i = _i + 1;
      IF _i <= _occurrences THEN

        SET `_substring` = SUBSTRING(SUBSTRING_INDEX(`@search`, _separator, _i),
                                     LENGTH(SUBSTRING_INDEX(`@search`, _separator, _i -1)) + 1);

        IF LENGTH(`_substring`)>0 THEN

          SET _correction= LENGTH( `_substring`) - LENGTH(REPLACE(`_substring`, ' ', ''));

          INSERT INTO Strings(pos, value, position, length)
          VALUES(
            _i,
            REPLACE(`_substring`, " ", ""),
            LENGTH(SUBSTRING_INDEX(`@search`, _separator, _i -1)) +1+_correction,
            LENGTH( REPLACE(`_substring`, " ", "") )
          );

        END IF;

        ITERATE ForIteration;
      END IF;
      LEAVE ForIteration;
    END LOOP ForIteration;

    SELECT SUM( (`@text` REGEXP value)*(0.5*1/pos) ) INTO _occurrences_to_returns
    FROM Strings;

    Return IFNULL( _occurrences_to_returns, 0);

    END;

-- DOWN