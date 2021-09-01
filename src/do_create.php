<?php

use Jaypha\MySQLiExt;

require_once __DIR__."/consts.php";

/**
 * Execute the 'create_table' command.
 *
 * @param string $userName Username to access the database
 * @param string $password Password to access the database
 * @param string $hostName THe host used to access the database
 */
function do_create(string $userName, string $password, string $hostName) {
  $database = new MySQLiExt($hostName, $userName, $password);

  $dbName = $database->real_escape_string(DATABASE_NAME);
  $tableName = $database->real_escape_string(TABLE_NAME);

  $database->q("CREATE DATABASE IF NOT EXISTS $dbName");
  $database->q("USE $dbName");
  $database->q("DROP TABLE IF EXISTS $tableName");
  $database->q(file_get_contents(__DIR__."/create_table_sql.txt"));

  $database->close();
}
