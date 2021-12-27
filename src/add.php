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

if (!isset($data->status)) $data->status = NULL;
if (!isset($data->flowtemp)) $data->flowtemp = NULL;
if (!isset($data->refluxtemp)) $data->refluxtemp = NULL;
if (!isset($data->tank1)) $data->tank1 = NULL;
if (!isset($data->tank2)) $data->tank2 = NULL;
if (!isset($data->hflowtemp)) $data->hflowtemp = NULL;
if (!isset($data->houtsidetemp)) $data->houtsidetemp = NULL;
if (!isset($data->hofficetemp)) $data->hofficetemp = NULL;
if (!isset($data->glasshousetemp)) $data->glasshousetemp = NULL;
if (!isset($data->timestamp)) $data->timestamp = NULL;

try {
  $query = "INSERT INTO data(status, flowtemp, refluxtemp, tank1, tank2, hflowtemp, houtsidetemp, hofficetemp, glasshousetemp, timestamp) VALUES(:status, :flowtemp, :refluxtemp, :tank1, :tank2, :hflowtemp, :houtsidetemp, :hofficetemp, :glasshousetemp, :timestamp);";

  $statement = $dbConnection->prepare($query);
  $statement->bindParam(":status", $data->status);
  $statement->bindParam(":flowtemp", $data->flowtemp);
  $statement->bindParam(":refluxtemp", $data->refluxtemp);
  $statement->bindParam(":tank1", $data->tank1);
  $statement->bindParam(":tank2", $data->tank2);
  $statement->bindParam(":hflowtemp", $data->hflowtemp);
  $statement->bindParam(":houtsidetemp", $data->houtsidetemp);
  $statement->bindParam(":hofficetemp", $data->hofficetemp);
  $statement->bindParam(":glasshousetemp", $data->glasshousetemp);
  $statement->bindParam(":timestamp", $data->timestamp);

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
