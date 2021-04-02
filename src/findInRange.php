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

$data = json_decode(file_get_contents("php://input"));

if (
  empty($data->between) ||
  empty($data->between->start) ||
  empty($data->between->end)
)
{
  http_response_code(400);
  echo json_encode(array("message" => "Invalid request data."));
  return;
}

try
{
  $query = "SELECT * FROM data WHERE timestamp BETWEEN :start AND :end ORDER BY timestamp ASC;";

  $statement = $dbConnection->prepare($query);
  $statement->bindParam(":start", $data->between->start);
  $statement->bindParam(":end", $data->between->end);

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
