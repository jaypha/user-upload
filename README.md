# Catalyst IT Coding challenge

by Jason den Dulk

This project was developed as part of the hiring process for Catalyst IT.

The script takes an input from a CSV file and saves the entries into a MySQL database
table.

## Synopsis
```
Usage:
  php user_upload.php [--create_table] [--file <filename>] [--dry_run] [-u <username>] [-p <password>] [-h <host>] [--help]

  One of either create_table, file, or help must be provided.

Options:
  --create_table     Will create the database table. If the table already exists, it will be dropped and recreated.
  --file <filename>  Will load <filename> as a CSV file and use it as the input source.
  --dry_run          When used with --file, will cause the input to be processed, but no insertions into the database will be made.
  -u <username>      The username to access the database.
  -p <password>      The password to access the database
  -h <hostname>      The host name of the database server, will default to 'localhost'
  --help             Print out this help message
```

## Dependencies

The project depends on the following packages.

#### APT
`sudo apt-get install php-mysqli`

#### Packagist

These packages require Composer to be installed on your system. See 
`https://getcomposer.org/` for installation.

- phpunit/phpunit
- jaypha/mysqli-ext
- nette/command-line

Run `composer install` to install them.

## Assumptions

- Decided to not use a shebang.
- No specification is provided about the database name. This project makes it 'user_store'. If it does not exist, then it will be created. Therefore the user is assumed to have create database
privileges.
- The user is also assumed to have create and drop table privileges.
- The table is to be recreated each time when --create_table is invoked. Therefore,
the table is dropped before creating, erasing any existing content.
- No output is given on successful execution.
- Missing fields are considered errors.
- Duplicate email entries are also considered errors. Only the first one is inserted.


## Testing

In order to carry out unit testing, the file phpunit.xml needs to be edited to insert
the database access parameters: hostname, username and password, for DB_HOST, DB_USER
and DB_PASSWD respectivel.

