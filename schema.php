<?php

# Prejournal internal database schema, version 1

function getTables() {
    return [
"drop table if exists users;",
"create table users (
  id SERIAL PRIMARY KEY,
  username varchar(54) UNIQUE,
  passwordhash varchar
);",

"drop table if exists components;",
"create table components (
  id SERIAL PRIMARY KEY,
  name varchar,
  UNIQUE(name)
);",

"drop table if exists movements;",
"create table movements (
  id SERIAL PRIMARY KEY,
  type_ varchar(54), /* 'invoice', 'payment' */
  fromComponent integer,
  toComponent integer,
  timestamp_ timestamp,
  amount decimal
);",

"drop table if exists statements;",
"create table statements (
  id SERIAL PRIMARY KEY,
  movementId integer,
  userId integer,
  sourceDocumentFormat character, /* could be an invoice, bank statement csv file, API call etc */
  sourceDocumentFilename character, /* TODO: work out how to store files when on Heroku */
  timestamp_ timestamp
);",

"drop table if exists componentGrants;",
"create table componentGrants (
  id SERIAL PRIMARY KEY,
  fromUser numeric,
  toUser numeric,
  componentId numeric
);"
  ];
}


if (isset($_SERVER["GEN_SQL"])) {
  echo "-- Created from schema.php, DO NOT EDIT DIRECTLY!\n";
  echo "-- To regenerate: GEN_SQL=1 php schema.php > schemal.xml\n\n";
  $tables = getTables();
  for ($i = 0; $i < count($tables); $i++) {
      echo $tables[$i] . "\n\n";
  }
} else if (isset($_SERVER["GEN_SQL_PG"])) {
  echo "-- Created from schema.php, DO NOT EDIT DIRECTLY!\n";
  echo "-- To regenerate: GEN_SQL=1 php schema.php > schemal.xml\n\n";
  $tables = getTables();
  for ($i = 0; $i < count($tables); $i++) {
      echo str_replace('integer primary key autoincrement', 'serial primary key', $tables[$i]) . "\n\n";
  }
}