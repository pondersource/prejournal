-- Created from schema.php, DO NOT EDIT DIRECTLY!
-- To regenerate: GEN_SQL=1 php schema.php > schemal.xml

drop table if exists users;

create table users (
  id SERIAL PRIMARY KEY,
  /**uuid uuid DEFAULT uuid_generate_v4 (),**/
  username varchar(54) UNIQUE,
  passwordhash varchar
);

drop table if exists components;

create table components (
  id SERIAL PRIMARY KEY,
  name varchar,
  UNIQUE(name)
);

drop table if exists movements;

create table movements (
  id SERIAL PRIMARY KEY,
  userId integer,
  type_ varchar(54), /* 'invoice', 'payment', 'worked' */
  fromComponent integer,
  toComponent integer,
  timestamp_ timestamp,
  amount decimal
);

drop table if exists statements;

create table statements (
  id SERIAL PRIMARY KEY,
  movementId integer,
  userId integer,
  sourceDocumentFormat varchar, /* could be an invoice, bank statement csv file, API call etc */
  sourceDocumentFilename varchar, /* TODO: work out how to store files when on Heroku */
  timestamp_ timestamp,
  description varchar,
  internal_type varchar,
  remote_id  varchar,
  UNIQUE(remote_id),
  remote_system varchar
);

drop table if exists componentGrants;

create table componentGrants (
  id SERIAL PRIMARY KEY,
  fromUser numeric,
  toUser numeric,
  componentId numeric
);

