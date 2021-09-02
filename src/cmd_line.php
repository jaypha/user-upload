<?php

/*
 * Command line module
 *
 * Functions for processing the command line
 */

use Nette\CommandLine\Parser;

const HELP_FILE = __DIR__."/help.txt";
const DEFAULT_DB_HOSTNAME = "localhost";

/**
 * Prints help info about the command line directives to stdout.
 */
function print_command_line_help() : void
{
  require HELP_FILE;
}

/**
 * Parse the comamnd line and return the parameters in an associative array.
 *
 * @param array $args The command line arguments in a array.
 *
 * @return array The options gleaned from the args.
 */
function parse_command_line(array $args) : array
{
  $params = [
    "hostName" => DEFAULT_DB_HOSTNAME
  ];

  try {
    $parser = new Parser("
      --file file_name
      --create_table
      --dry_run
      -u username
      -p password
      -h hostname
      --help
    ");

    $cmdLine = $parser->parse($args);

    if (isset($cmdLine["--help"]))
      $command = "help";
    else if (isset($cmdLine["--create_table"]))
      $command = "create";
    else if (isset($cmdLine["--file"]))
    {
      $command = "file";
      $params["fileName"] = $cmdLine["--file"];
    }
    else
      throw new \RuntimeException("Please indicate one of --create_table, --file, or --help");

    $params["command"] = $command;

    if ($command == "create" || $command == "file") {
      // Need database parameters

      if (isset($cmdLine["-u"]))
        $params["userName"] = $cmdLine["-u"];
      else
        throw new \RuntimeException("Please supply a username (-u)");

      if (isset($cmdLine["-p"]))
        $params["password"] = $cmdLine["-p"];
      else
        throw new \RuntimeException("Please supply a password (-p)");

      if (isset($cmdLine["-h"]))
        $params["hostName"] = $cmdLine["-h"];
    }

    $params["isDryRun"] = isset($cmdLine["--dry_run"]);
   
  } catch (\Exception $e) {
    // Attach command line help message.

    throw new \RuntimeException($e->getMessage()."\n\n".file_get_contents(HELP_FILE));
  }

  return $params;
}

