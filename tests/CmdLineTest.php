<?php

require_once __DIR__."/../src/cmd_line.php";

use PHPUnit\Framework\TestCase;

class CmdLineTest extends TestCase {
  function process_cmd_line($argLine) {
    return parse_command_line(explode(" ", $argLine));
  }

  function isArraySubset($smaller, $larger) {
    foreach ($smaller as $i => $v)
    {
      $this->assertArrayHasKey($i, $larger);
      $this->assertEquals($v, $larger[$i]);
    }
  }

  function testHelp() {
    $args = "--help";
    $expected = [ "command" => "help" ];

    $resposne = $this->process_cmd_line($args);
    $this->isArraySubset($expected, $resposne);
  }

  function testFile() {
    $args = "--file input.csv -u kool-kid -p youch!";
    $expected = [
      "command" => "file",
      "userName" => "kool-kid",
      "password" => "youch!",
      "hostName" => "localhost",
      "isDryRun" => false,
    ];

    $resposne = $this->process_cmd_line($args);
    $this->isArraySubset($expected, $resposne);
  }

  function testFileWithDryRun() {
    $args = "--file input.csv -u kitkat -p not-secure --dry_run";
    $expected = [
      "command" => "file",
      "userName" => "kitkat",
      "password" => "not-secure",
      "hostName" => "localhost",
      "isDryRun" => true,
    ];

    $resposne = $this->process_cmd_line($args);
    $this->isArraySubset($expected, $resposne);
  }

  function testFileWithHost() {
    $args = "--file input.csv -u doodle -p poodle -h some_host";
    $expected = [
      "command" => "file",
      "userName" => "doodle",
      "password" => "poodle",
      "hostName" => "some_host",
      "isDryRun" => false,
    ];

    $resposne = $this->process_cmd_line($args);
    $this->isArraySubset($expected, $resposne);
  }

  function testCreate() {
    $args = "--create_table -u amiga -p rules";
    $expected = [
      "command" => "create",
      "userName" => "amiga",
      "password" => "rules",
      "hostName" => "localhost",
    ];

    $resposne = $this->process_cmd_line($args);
    $this->isArraySubset($expected, $resposne);
  }

  function testCreateWithHost() {
    $args = "--create_table -u something -p creative -h outer-space";
    $expected = [
      "command" => "create",
      "userName" => "something",
      "password" => "creative",
      "hostName" => "outer-space",
    ];

    $resposne = $this->process_cmd_line($args);
    $this->isArraySubset($expected, $resposne);
  }

  function testNoCommand() {
    $args = "-u something -p creative -h outer-space";
    $this->expectException(\RuntimeException::class);


    $resposne = $this->process_cmd_line($args);
  }

  function testCreateNoUser() {
    $args = "--create_table -p creative -h outer-space";

    $this->expectException(\RuntimeException::class);
    $resposne = $this->process_cmd_line($args);
  }

  function testFileNoUser() {
    $args = "--file filename.csv -p creative -h outer-space";

    $this->expectException(\RuntimeException::class);
    $resposne = $this->process_cmd_line($args);
  }

  function testCreateNoPassword() {
    $args = "--create_table -u creative -h outer-space";

    $this->expectException(\RuntimeException::class);
    $resposne = $this->process_cmd_line($args);
  }

  function testFileNoPassword() {
    $args = "--file filename.csv -u creative";

    $this->expectException(\RuntimeException::class);
    $resposne = $this->process_cmd_line($args);
  }

  function testFileNoFile() {
    $args = "--file -u creative";

    $this->expectException(\RuntimeException::class);
    $resposne = $this->process_cmd_line($args);
  }
}


