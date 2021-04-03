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
  !isset($data->status) ||
  !isset($data->flowtemp) ||
  !isset($data->refluxtemp) ||
  !isset($data->timestamp)
)
{
  http_response_code(400);
  echo json_encode(array("message" => "Invalid request data."));
  return;
}

try
{
  $query = "INSERT INTO data(status, flowtemp, refluxtemp, timestamp) VALUES(:status, :flowtemp, :refluxtemp, :timestamp);";

  $statement = $dbConnection->prepare($query);
  $statement->bindParam(":status", $data->status);
  $statement->bindParam(":flowtemp", $data->flowtemp);
  $statement->bindParam(":refluxtemp", $data->refluxtemp);
  $statement->bindParam(":timestamp", $data->timestamp);

  $success = $statement->execute();
  
  if ($success)
  {
    http_response_code(201);
    echo json_encode(array("message" => "Success."));
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
  echo json_encode(array("message" => $e->getmessage()));
}


