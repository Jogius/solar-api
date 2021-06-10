<?php
require "./vendor/autoload.php";
// Require dependencies
use Dotenv\Dotenv;
require "./utils/DatabaseConnector.php";

// // Set response headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Read values from .env
$dotenv = new DotEnv(__DIR__);
$dotenv->load();

// Initialize Database connection
$dbConnection = (new DatabaseConnector())->getConnection();

// Get limit from URL
$params = array();
parse_str($_SERVER["QUERY_STRING"], $params);
if ($params["limit"])
{
  $limit = $params["limit"];
}

try
{
  if (isset($limit))
  {
    $query = "SELECT * FROM data ORDER BY timestamp DESC LIMIT :limit;";

     $statement = $dbConnection->prepare($query);
     $statement->bindParam(":limit", $limit, PDO::PARAM_INT);
  }
  else
  {
    $query = "SELECT * FROM data ORDER BY timestamp ASC;";

    $statement = $dbConnection->query($query);
  }
  
  $success = $statement->execute();
  
  if ($success)
  {
    $data = $statement->fetchAll();
  
    echo json_encode($data);
  }
  else
  {
    http_response_code(403);
    echo json_encode(array("message" => "Internal error."));
  }
}
catch (PDOException $e)
{
  http_response_code(400);
  echo json_encode(array("message" => $e->getMessage()));
}
