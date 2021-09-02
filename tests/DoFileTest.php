<?php

require_once __DIR__."/../src/do_file.php";

use Jaypha\MySQLiExt;
use PHPUnit\Framework\TestCase;

class UploaderTester extends UserUpload {

  function clearOut() { 
    $this->clean();
  }

  function test_read_csv_to_array(string $fileName) {
    $this->csv_to_array($fileName);
  }

  function test_validate_line(array $row) : bool {
    return $this->validate_line($row);
  }

  function test_process_line(array $row) : array {
    return $this->process_line($row);
  }
}

class DoFileTest extends TestCase {

  protected static $uploader;

  public static function setUpBeforeClass(): void {
    self::$uploader = new UploaderTester(
      $GLOBALS["DB_USER"],
      $GLOBALS["DB_PASSWD"],
      $GLOBALS["DB_HOST"],
    );
  }

  function testValidateLine() {
    $row = [ ];
    self::$uploader->clearOut();
    self::$uploader->test_validate_line($row);
    $this->assertEquals(["Name entry missing"], self::$uploader->errors);

    $row["name"] = "x";
    self::$uploader->clearOut();
    self::$uploader->test_validate_line($row);
    $this->assertEquals(["Surname entry missing"], self::$uploader->errors);

    $row["surname"] = "y";
    self::$uploader->clearOut();
    self::$uploader->test_validate_line($row);
    $this->assertEquals(["Email entry missing"], self::$uploader->errors);

    $row["email"] = "edward@jikes@com.au";
    self::$uploader->clearOut();
    self::$uploader->test_validate_line($row);
    $this->assertEquals(["Email address '{$row["email"]}' is invalid"], self::$uploader->errors);

    $row["email"] = "edward@jikes.com.au";
    self::$uploader->clearOut();
    self::$uploader->test_validate_line($row);
    $this->assertEquals([], self::$uploader->errors);
  }


  function testProcessLine() {
    $input = [
      [ "name" => "John", "surname" => "DOE", "email" => "jDoe@gmail.com" ],
      [ "name" => "biLL", "surname" => "van volt", "email" => "bv@gmail.com" ],
      [ "name" => "able", "surname" => "tasman", "email" => "a.t@ABC.COM.AU" ],
    ];

    $expected = [
      [ "name" => "John", "surname" => "Doe", "email" => "jdoe@gmail.com" ],
      [ "name" => "Bill", "surname" => "Van Volt", "email" => "bv@gmail.com" ],
      [ "name" => "Able", "surname" => "Tasman", "email" => "a.t@abc.com.au" ],
    ];

    foreach ($input as $i => $v)
      $this->assertEquals($expected[$i], self::$uploader->test_process_line($v));
  }
}


