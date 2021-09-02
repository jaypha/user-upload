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

function do_file(string $fileName, bool $isDryRun, string $userName, string $password, string $hostName) : void {

  $uploader = new UserUpload($userName, $password, $hostName);
  $uploader->process($fileName, $isDryRun);

  foreach ($uploader->errors as $error)
    echo $error,"\n";
}

/**
 * Manages the process of reading and porcessing the input and updating the database.
 */
class UserUpload {

  private $database = null;
  private $hostName;
  private $password;
  private $userName;

  private $processedLines;

  private $errors;

  private $processedEmails;

  /**
   * Creates the object, but takes no furhter action.
   *
   * @param string $userName Username to access the database
   * @param string $password Password to access the database
   * @param string $hostName THe host used to access the database
   */
  function __construct(string $userName, string $password, string $hostName) {
    $this->hostName = $hostName;
    $this->password = $password;
    $this->userName = $userName;
  }

  protected function clean() : void { 
    $this->processedLines = [];
    $this->errors = [];
    $this->processedEmails = [];
  }

  /**
   * Read in an inputfile and store the details into the database.
   *
   * @param string $fileName Name of the input file to process. Must be a CSV file
   * @param string $isDryRun if true, then records are not to be entered into database.
   */
  function process(string $fileName, bool $isDryRun) : void {

    $this->database = new MySQLiExt($this->hostName, $this->userName, $this->password);
    if (!$this->database->tableExists(DATABASE_NAME.".".TABLE_NAME))
      throw new \RuntimeExcpetion("Database table does not exist, please create using --create_table");

    $dbName = $this->database->real_escape_string(DATABASE_NAME);
    $this->tableName = $this->database->real_escape_string(TABLE_NAME);
    $this->database->q("USE $dbName");

    $this->clean();

    $this->read_csv_to_array($fileName);

    if (!$isDryRun && count($this->processedLines) > 0)
      $this->database->insert(TABLE_NAME, [ "name", "surname", "email" ], $this->processedLines);
    $this->database->close();
    $this->database = null;
  }


  // Reads, validates, and processes input from a CSV file.

  protected function read_csv_to_array(string $fileName) : void {
    $stream = @fopen($fileName, "r");
    if ($stream === false)
      throw new \RuntimeException(error_get_last()["message"]);

    $headers = array_map("trim", fgetcsv($stream, 0));
    if ($headers === false)
      throw new \RuntimeException("Cannot read headers line");
    
    while (($inputLine = @fgetcsv($stream, 0)) !== false)
    {
      $row = [];
      foreach ($headers as $i => $v)
        $row[$v] = trim($inputLine[$i]);
      if ($this->validate_line($row))
      {
        $processedLine = $this->process_line($row);
        if ($this->check_duplicate($processedLine))
        {
          $this->processedLines[] = $processedLine;
          $this->processedEmails[] = $row["email"];
        }
      }

    }
    if (!feof($stream))
      throw new \RuntimeException(error_get_last()["message"]);
  }


  // Validates a line of input, testing for existance and syntax correctness.

  protected function validate_line(array $row) : bool
  {
    if (!isset($row["name"]))
    {
      $this->errors[] = "Name entry missing";
      return false;
    }

    if (!isset($row["surname"]))
    {
      $this->errors[] = "Surname entry missing";
      return false;
    }
    if (!isset($row["email"]))
    {
      $this->errors[] = "Email entry missing";
      return false;
    }

    if (!filter_var($row["email"], FILTER_VALIDATE_EMAIL))
    {
      $this->errors[] = "Email address '{$row["email"]}' is invalid";
      return false;
    }

    return true;
  }


  // Check for duplicates both already in the database, and in the current input.

  protected function check_duplicate(array $row) : bool {
    if (in_array($row["email"], $this->processedEmails))
    {
      $this->errors[] = "Email address '{$row["email"]}' already processed";
      return false;
    }

    if (
      $this->database &&
      $this->database->queryValue(
        "select count(*) from $this->tableName where email = ".
        $this->database->quote($row["email"])
      )
    )
    {
      $this->errors[] = "Email address '{$row["email"]}' exists in database";
      return false;
    }

    return true;
  }

  // Processes a single line of input, performing required transformions.

  protected function process_line(array $row) : array 
  {
    // Assumes that the row has been validated.
    return [
      "name" => ucwords(strtolower($row["name"])),
      "surname" => ucwords(strtolower($row["surname"]), " '"),
      "email" => strtolower($row["email"])
    ];
  }


  function __get(string $p)
  {
    switch ($p)
    {
      case "errors":
        return $this->errors;
      default:
        throw new \LogicException("Property '$p' not suppoerted in UserUpload");
    }
  }
}

