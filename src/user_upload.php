<?php

require __DIR__."/../vendor/autoload.php";

require_once __DIR__."/cmd_line.php";
require_once __DIR__."/do_file.php";
require_once __DIR__."/do_create.php";


function main() {
  global $argv;

  try {
    array_shift($argv); // Strip the program name from the args.
    $params = parse_command_line($argv);

    switch ($params["command"]) {
      case "file":
        do_file(
          $params["fileName"],
          $params["isDryRun"],
          $params["userName"],
          $params["password"],
          $params["hostName"]
        );
        break;

      case "create":
        do_create(
          $params["userName"],
          $params["password"],
          $params["hostName"]
        );
        break;

      case "help":
        print_command_line_help();
        break;

      default:
        throw new \LogicException("Unknown command {$params["command"]}");
    }
  } catch (\Exception $e) {
    echo "Error: ", $e->getMessage(), "\n";
    return 1;
  }

  return 0;
}


return main();
