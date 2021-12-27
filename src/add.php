<?php
require "./vendor/autoload.php";
// Require dependencies
use Dotenv\Dotenv;
require "./utils/DatabaseConnector.php";

// Set response headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Read values from .env
$dotenv = new DotEnv(__DIR__);
$dotenv->load();

// Initialize Database connection
$dbConnection = (new DatabaseConnector())->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (
  !isset($data->token) ||
  strcmp($data->token, getenv("TOKEN")) != 0
) {
  http_response_code(401);
  echo json_encode(array("message" => "Invalid token."));
  return;
}

if (
  !isset($data->status) &&
  !isset($data->flowtemp) &&
  !isset($data->refluxtemp) &&
  !isset($data->tank1) &&
  !isset($data->tank2) &&
  !isset($data->hflowtemp) &&
  !isset($data->houtsidetemp) &&
  !isset($data->hofficetemp) &&
  !isset($data->glasshousetemp)
) {
  http_response_code(400);
  echo json_encode(array("message" => "Invalid request data."));
  return;
}

try {
  $query = "INSERT INTO data(status, flowtemp, refluxtemp, tank1, tank2, hflowtemp, houtsidetemp, hofficetemp, glasshousetemp, timestamp) VALUES(:status, :flowtemp, :refluxtemp, :tank1, :tank2, :hflowtemp, :houtsidetemp, :hofficetemp, :glasshousetemp, :timestamp);";

  $statement = $dbConnection->prepare($query);
  $statement->bindParam(":status", $data->status ?? NULL);
  $statement->bindParam(":flowtemp", $data->flowtemp ?? NULL);
  $statement->bindParam(":refluxtemp", $data->refluxtemp ?? NULL);
  $statement->bindParam(":tank1", $data->tank1 ?? NULL);
  $statement->bindParam(":tank2", $data->tank2 ?? NULL);
  $statement->bindParam(":hflowtemp", $data->hflowtemp ?? NULL);
  $statement->bindParam(":houtsidetemp", $data->houtsidetemp ?? NULL);
  $statement->bindParam(":hofficetemp", $data->hofficetemp ?? NULL);
  $statement->bindParam(":glasshousetemp", $data->glasshousetemp ?? NULL);
  $statement->bindParam(":timestamp", $data->timestamp ?? NULL);

  $success = $statement->execute();

  if ($success) {
    http_response_code(201);
    echo json_encode(array("message" => "Success."));
  } else {
    http_response_code(403);
    echo json_encode(array("message" => "Internal error."));
  }
} catch (PDOException $e) {
  http_response_code(400);
  echo json_encode(array("message" => $e->getmessage()));
}
