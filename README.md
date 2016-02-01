# MToolkit - Evolution
Agile way to manage your database releases.

## Why
In the agile world you have to deploy quickly the new implementations of the software.
Tracking and managing the changes of the database is difficult and boring.
MToolkit - Evolution tries to make this task easy.
We have been inspired by Play Framework: https://www.playframework.com/documentation/2.0/Evolutions
You can track the database evolutions in your project (in a dedicated folder) and you can apply them for the release.

## How to use
In your project, create one or more (one per each environment) .ini file like this:
```
username = root
password = passwd
host = 127.0.0.1
type = mysql
dbname = mevolution_test
```
This file will be used to connect to the database where you apply or revert the evolutions.

In a folder put the evolutions files. The names of the files must be 1.sql, 2.sql, etc.
The content of the files must be like this:
```sql
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
```

**UP and DOWN placeholders are mandatory.**
The UP queries will execute when you apply the evolutions.
The DOWN queries will execute when you revert the evolutions.


### Init
To initializate the database and before applying new evolutions, run the command:
```bash
php mevolution.phar init -s=path_to_the_ini_file -e=path_of_the_evolutions_folder
```

### Apply evolutions
To apply the evolutions run:
```bash
php mevolution.phar apply -s=path_to_the_ini_file -e=path_of_the_evolutions_folder
```

### Revert evolutions
To revert the evolutions run:
```bash
php mevolution.phar revert -s=path_to_the_ini_file -e=path_of_the_evolutions_folder
```

To revert to a specific evolution use "-to=" argument:
```bash
php mevolution.phar revert -s=path_to_the_ini_file -e=path_of_the_evolutions_folder -to=1
```
The "down" query "1" will be executed.

## Create PHAR executable
MToolkit Evolution uses [box-project](http://box-project.github.io/box2/) to create the PHAR file.

To create the PHAR executable, run the following command in the root of the project:
```bash
php box.phar build -c manifest.json 
```
## Not supported
- Using DELIMITER in the evolutions file.
