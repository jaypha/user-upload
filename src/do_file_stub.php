<?php

/*
 * File Command Stub
 * A stub for testing command line processing.
 */


/**
 * Execute the 'file' command.
 */
function do_file(string $fileName, bool $isDryRun, string $userName, string $password, string $hostName) {
  echo "do_file\n";
  echo "file name: $fileName\n";
  echo "username: $userName\n";
  echo "password: $password\n";
  echo "host name: $hostName\n";
  echo "dry run?: ",($isDryRun?"Y":"N"),"\n";
}

