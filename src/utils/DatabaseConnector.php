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
          status INT NOT NULL,
          flowtemp DOUBLE(4, 2) NOT NULL,
          refluxtemp DOUBLE(4, 2) NOT NULL,
          timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (id)
        ) ENGINE=INNODB;
      ";

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
