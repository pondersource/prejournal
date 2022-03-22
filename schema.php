<?php

# Prejournal internal database schema, version 1

function getTables() {
    return [
"drop table if exists users;",
"create table users (
  id integer primary key autoincrement,
  username varchar unique,
  passwordhash varchar
);",

"drop table if exists components;",
"create table components (
  id integer primary key autoincrement,
  name varchar unique
);",

"drop table if exists movements;",
"create table movements (
  id integer primary key autoincrement,
  type_ varchar, /* 'invoice', 'payment' */
  fromComponent int,
  toComponent int,
  timestamp_ timestamp,
  amount float
);",

"drop table if exists statements;",
"create table statements (
  id integer primary key autoincrement,
  movementId int,
  userId int,
  sourceDocumentFormat varchar, /* could be an invoice, bank statement csv file, API call etc */
  sourceDocumentFilename varchar, /* TODO: work out how to store files when on Heroku */
  timestamp_ timestamp
)",

"drop table if exists componentGrants;",
"create table componentGrants (
  id integer primary key autoincrement,
  fromUser int,
  toUser int,
  componentId int
)"
  ];
}


if (isset($_SERVER["GEN_SQL"])) {
  echo "-- Created from schema.php, DO NOT EDIT DIRECTLY!\n";
  echo "-- To regenerate: GEN_SQL=1 php schema.php > schemal.xml\n\n";
  $tables = getTables();
  for ($i = 0; $i < count($tables); $i++) {
      echo $tables[$i] . "\n\n";
  }
}