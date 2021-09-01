<?php


use Jaypha\MySQLiExt;

require_once __DIR__."/consts.php";

/**
 * Execute the 'file' command.
 *
 * @param string $fileName Name of the input file to process. Must be a CSV file
 * @param string $isDryRun if true, then records are not to be entered into database.
 * @param string $userName Username to access the database
 * @param string $password Password to access the database
 * @param string $hostName THe host used to access the database
 */

function do_file(string $fileName, bool $isDryRun, string $userName, string $password, string $hostName) {
  $response = read_csv_to_array($fileName);

  if (!$isDryRun)
  {
    $database = new MySQLiExt($hostName, $userName, $password);
    if (!$database->tableExists(DATABASE_NAME.".".TABLE_NAME))
      throw new \RuntimeExcpetion("Database table does not exist, please create using --create_table");

    $dbName = $database->real_escape_string(DATABASE_NAME);
    $database->q("USE $dbName");

    $database->insert(TABLE_NAME, [ "name", "surname", "email" ], $response["data"]);
    $database->close();
  }

  foreach ($response["errors"] as $error)
    echo $error, "\n";
}

/**
 * Reads, validates, and processes input from a CSV file.
 */

function read_csv_to_array($fileName) {
  $stream = @fopen($fileName, "r");
  if ($stream === false)
    throw new \RuntimeException(error_get_last()["message"]);

  $headers = array_map("trim", fgetcsv($stream, 0));
  if ($headers === false)
    throw new \RuntimeException("Cannot read headers line");
  
  $output = [];
  $errors = [];
  while (($inputLine = @fgetcsv($stream, 0)) !== false)
  {
    $row = [];
    foreach ($headers as $i => $v)
      $row[$v] = trim($inputLine[$i]);
    $response = validate_line($row);
    if ($response === false)
      $output[] = process_line($row);
    else
      $errors[] = $response;
  }
  if (!feof($stream))
    throw new \RuntimeException(error_get_last()["message"]);

  return [ "data" => $output, "errors" => $errors ];
}

function process_line($row)
{
  // Assumes that the row has been validated.
  return [
    "name" => ucwords(strtolower($row["name"])),
    "surname" => ucwords(strtolower($row["surname"]), " '"),
    "email" => strtolower($row["email"])
  ];
}


/**
 *
 *  @returns mixed A string containing the message describing the invalidity, or false is the row was valid.
 */
function validate_line($row)
{
  if (!isset($row["name"]))
    return "Name entry missing";
  if (!isset($row["surname"]))
    return "Surame entry missing";
  if (!isset($row["email"]))
    return "Email entry missing";

  if (!filter_var($row["email"], FILTER_VALIDATE_EMAIL))
    return "Email address '{$row["email"]}' is invalid";

  return false;
}



