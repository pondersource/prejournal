<?php

# Prejournal internal database schema, version 1

function getTables()
{
    return [
        "drop table if exists users;",
        "create table users (
  id SERIAL PRIMARY KEY,
  /**uuid uuid DEFAULT uuid_generate_v4 (),**/
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
  userId integer,
  type_ varchar(54), /* DEPRECATED */
  fromComponent integer,
  toComponent integer,
  timestamp_ timestamp,
  amount decimal,
  unit varchar,
  subIndex integer default 0,
  deleted boolean default false
);",

        "drop table if exists implications;",
        "create table implications (
id SERIAL PRIMARY KEY,
userId integer,
relation varchar(54), /* 'inner', 'outer', 'delivery', 'consumption', 'production' */
statementId integer,
movementId integer
);",

        "drop table if exists statements;",
        "create table statements (
  id SERIAL PRIMARY KEY,
  userId integer,
  documentId integer,
  entryId integer,
  attributes varchar, /* JSON */
  remote_id varchar,
  UNIQUE(remote_id),
  remote_system varchar
);",

        "drop table if exists documents;",
        "create table documents (
id SERIAL PRIMARY KEY,
userId integer,
content varchar,
language varchar,
origin varchar,
speechAct varchar
);",


        "drop table if exists componentGrants;",
        "create table componentGrants (
  id SERIAL PRIMARY KEY,
  fromUser numeric,
  toUser numeric,
  componentId numeric
);",

        // when creating or updating a movement,
        // the user needs to have access to the
        // fromComponent of that movement
        // via this table
        "drop table if exists accessControl;",
        "create table accessControl (
  id SERIAL PRIMARY KEY,
  componentId numeric UNIQUE,
  userId numeric
);",
        "drop table if exists commandLog;",
        "create table commandLog (
  id SERIAL PRIMARY KEY,
  contextJson varchar,
  commandJson varchar
);",
];
}

if (isset($_SERVER["GEN_SQL"])) {
    echo "-- Created from schema.php, DO NOT EDIT DIRECTLY!\n";
    echo "-- To regenerate: GEN_SQL=1 php schema.php > schemal.xml\n\n";
    $tables = getTables();
    for ($i = 0; $i < count($tables); $i++) {
        echo $tables[$i] . "\n\n";
    }
} elseif (isset($_SERVER["GEN_SQL_PG"])) {
    echo "-- Created from schema.php, DO NOT EDIT DIRECTLY!\n";
    echo "-- To regenerate: GEN_SQL=1 php schema.php > schemal.xml\n\n";
    $tables = getTables();
    for ($i = 0; $i < count($tables); $i++) {
        echo str_replace('integer primary key autoincrement', 'serial primary key', $tables[$i]) . "\n\n";
    }
}
