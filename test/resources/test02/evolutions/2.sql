-- UP

DROP PROCEDURE IF EXISTS `color_get`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `color_get`(
  IN `@id` INT
)
BEGIN

  SELECT
    id,
    description,
    inserted,
    updated
  FROM color
  WHERE id = `@id`;

END;

-- DOWN