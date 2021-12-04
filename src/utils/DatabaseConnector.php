<?php
use Dotenv\Dotenv;

class DatabaseConnector
{
  private $dbConnection = null;

  public function __construct()
  {
    $host = getenv("DB_HOST");
    $port = getenv("DB_PORT");
    $db   = getenv("DB_DATABASE");
    $username = getenv("DB_USERNAME");
    $password = getenv("DB_PASSWORD");

    try
    {
      $this->dbConnection = new PDO(
        "mysql:host=$host;port=$port;charset=utf8mb4;dbname=$db",
          $username,
          $password
      );
      $this->dbConnection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

      $statement = "
        CREATE TABLE IF NOT EXISTS data (
          id INT NOT NULL AUTO_INCREMENT,
          status VARCHAR(10) NULL DEFAULT NULL,
          flowtemp DOUBLE(4, 2) NULL DEFAULT NULL,
          refluxtemp DOUBLE(4, 2) NULL DEFAULT NULL,
          tank1 DOUBLE(4, 2) NULL DEFAULT NULL,
          tank2 DOUBLE(4, 2) NULL DEFAULT NULL,
          hflowtemp DOUBLE(4, 2) NULL DEFAULT NULL,
          houtsidetemp DOUBLE(4, 2) NULL DEFAULT NULL,
          hofficetemp DOUBLE(4, 2) NULL DEFAULT NULL,
          timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (id)
        ) ENGINE=INNODB;
      ";

      // $statement = "
      //   IF ((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE (table_name = 'data') AND (table_schema = 'dbs1732604') AND (column_name = 'tank1')) > 0)
      //   BEGIN
      //     ALTER TABLE data
      //     ADD tank1 DOUBLE(4, 2) DEFAULT NULL, tank2 DOUBLE(4, 2) DEFAULT NULL;
      //   END;
      // ";

      $this->dbConnection->exec($statement);
    }
    catch (PDOException $e)
    {
      exit($e->getMessage());
    }
  }

  public function getConnection()
  {
    return $this->dbConnection;
  }
}
