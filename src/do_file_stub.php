<?php

/*
 * File Command Stub
 * A stub for testing command line processing.
 */


/**
 * Execute the 'file' command.
 */
function do_file(array $params) {
  echo "do_file\n";
  echo "file name: {$params["fileName"]}\n";
  echo "username: {$params["userName"]}\n";
  echo "password: {$params["password"]}\n";
  echo "host name: {$params["hostName"]}\n";
  echo "dry run?: ",($params["isDryRun"]?"Y":"N"),"\n";
}

