<?php

require_once __DIR__."/../src/do_file.php";

use Jaypha\MySQLiExt;
use PHPUnit\Framework\TestCase;

class DoFileTest extends TestCase {

  const INPUT_FILE = __DIR__."/users.csv";
  const EXPECTED_PROCESSED_FILE = [
    [ "name" => "John", "surname" => "Smith", "email" => "jsmith@gmail.com" ],
    [ "name" => "Hamish", "surname" => "Jones", "email" => "ham@seek.com" ],
    [ "name" => "Phil", "surname" => "Carry", "email" => "phil@open.edu.au" ],
    [ "name" => "Johnny", "surname" => "O'Hare", "email" => "john@yahoo.com.au" ],
    [ "name" => "Mike", "surname" => "O'Connor", "email" => "mo'connor@cat.net.nz" ],
    [ "name" => "William", "surname" => "Smythe", "email" => "happy@ent.com.au" ],
    [ "name" => "Hamish", "surname" => "Jones", "email" => "ham@seek.com" ],
    [ "name" => "Sam!!", "surname" => "Walters", "email" => "sam!@walters.org" ],
    [ "name" => "Daley", "surname" => "Thompson", "email" => "daley@yahoo.co.nz" ],
    [ "name" => "Kevin", "surname" => "Ruley", "email" => "kevin.ruley@gmail.com" ],
//    [ "name" => "Edward", "surname" => "Jikes", "email" => "dward@jikes@com.au" ],
  ];

  function testValidateLine() {
    $row = [ ];
    $this->assertEquals("Name entry missing", validate_line($row));

    $row["name"] = "x";
    $this->assertEquals("Surame entry missing", validate_line($row));

    $row["surname"] = "y";
    $this->assertEquals("Email entry missing", validate_line($row));

    $row["email"] = "edward@jikes@com.au";
    $this->assertEquals("Email address '{$row["email"]}' is invalid", validate_line($row));

    $row["email"] = "edward@jikes.com.au";
    $this->assertFalse(validate_line($row));
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
      $this->assertEquals($expected[$i], process_line($v));
  }

  function testReadCsvToArray()
  {
    $response = read_csv_to_array(self::INPUT_FILE);
    $this->assertEquals(self::EXPECTED_PROCESSED_FILE, $response["data"]);
  }
}


