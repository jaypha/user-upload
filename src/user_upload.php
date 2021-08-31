<?php

require __DIR__."/../vendor/autoload.php";

require_once __DIR__."/cmd_line.php";
require_once __DIR__."/command_stub.php";


function main() {
  global $argv;

  try {
    array_shift($argv); // Strip the program name from the args.
    $params = parse_command_line($argv);

    switch ($params["command"]) {
      case "file":
        do_file($params);
        break;

      case "create":
        do_create($params);
        break;

      case "help":
        print_command_line_help();
        break;

      default:
        throw new \LogicException("Unknown command {$params["command"]}");
    }
  } catch (\Exception $e) {
    echo "Error: ", $e->getMessage();
    return 1;
  }

  return 0;
}


return main();
