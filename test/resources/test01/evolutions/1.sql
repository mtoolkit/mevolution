-- UP

CREATE TABLE test01
(
  id          INT,
  field01     VARCHAR(255),
  field02     VARCHAR(255),
  start_date  DATE,
  end_date    DATE,

  PRIMARY KEY
  (
    id,
    field01,
    start_date
  )
);

-- DOWN
DROP TABLE test01;