<?php

# Prejournal internal database schema, version 1

function getTables() {
    return [
"create table userzs (
  id serial primary key,
  username varchar unique,
  passwordhash varchar
);",

"create table components (
  id serial primary key,
  name varchar unique
);",

"create table movements (
  id serial primary key,
  type varchar, /* 'invoice', 'payment' */
  fromComponent int,
  toComponent int,
  timestamp_ timestamp,
  amount float
);",

"create table statements (
  id serial primary key,
  movementId int,
  userId int,
  sourceDocumentFormat varchar, /* could be an invoice, bank statement csv file, API call etc */
  sourceDocumentFilename varchar, /* TODO: work out how to store files when on Heroku */
  timestamp_ timestamp
)",

"create table componentGrants (
  id serial primary key,
  fromUser int,
  toUser int,
  componentId int
)"
  ];
}


if (isset($_SERVER["GEN_XML"])) {
  echo "-- Created from schema.php, DO NOT EDIT DIRECTLY!\n";
  echo "-- To regenerate: GEN_XML=1 php schema.php > schemal.xml\n\n";
  $tables = getTables();
  for ($i = 0; $i < count($tables); $i++) {
      echo $tables[$i] . "\n\n";
  }
}