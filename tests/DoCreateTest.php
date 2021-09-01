<?php

require_once __DIR__."/../src/do_create.php";

use Jaypha\MySQLiExt;
use PHPUnit\Framework\TestCase;

class DoCreateTest extends TestCase {

  function testCreate() {
    global $DB_HOST, $DB_USER, $DB_PASSWD;

    $database = new MySQLiExt($DB_HOST, $DB_USER, $DB_PASSWD);

    $dbName = $database->real_escape_string(DATABASE_NAME);

    $database->q("DROP DATABASE IF EXISTS $dbName");
    $database->close();

    // Run with no dataase or table.
    do_create($DB_USER, $DB_PASSWD, $DB_HOST);

    $database = new MySQLiExt($DB_HOST, $DB_USER, $DB_PASSWD);

    $this->assertEquals(DATABASE_NAME,$database->queryValue("show databases like '$dbName'"));
    $this->assertTrue($database->tableExists(DATABASE_NAME.".".TABLE_NAME));

    $database->close();

    // This is run with a created database and table. Make sure it doesn't crash.
    do_create($DB_USER, $DB_PASSWD, $DB_HOST);
  }
}
