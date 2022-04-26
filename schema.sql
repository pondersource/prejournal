-- Created from schema.php, DO NOT EDIT DIRECTLY!
-- To regenerate: GEN_SQL=1 php schema.php > schemal.xml

drop table if exists users;

create table users (
  id SERIAL PRIMARY KEY,
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
  type_ varchar(54), /* 'invoice', 'payment', 'worked' */
  fromComponent integer,
  toComponent integer,
  timestamp_ timestamp,
  amount decimal,
  description varchar
);

drop table if exists statements;

create table statements (
  id SERIAL PRIMARY KEY,
  movementId integer,
  userId integer,
  sourceDocumentFormat character, /* could be an invoice, bank statement csv file, API call etc */
  sourceDocumentFilename character, /* TODO: work out how to store files when on Heroku */
  timestamp_ timestamp
);

drop table if exists componentGrants;

create table componentGrants (
  id SERIAL PRIMARY KEY,
  fromUser numeric,
  toUser numeric,
  componentId numeric
);


drop table if exists sync;

create table sync (
  internal_type varchar,
  internal_id numeric,
  remote_id  varchar,
  remote_system varchar
);



