-- Prejournal internal database schema, version 1

create table users (
  id serial primary key,
  username varchar unique,
  passwordhash varchar
);

create table components (
  id serial primary key,
  name varchar unique
);

create table movements (
  id serial primary key,
  type varchar, /* 'invoice', 'payment' */
  fromComponent int,
  toComponent int,
  timestamp_ timestamp,
  amount float,
  agreementId int /* ties e.g. an invoice to a payment */
);

create table statements {
  id serial primary key,
  movementId int,
  userId int,
  timestamp_ timestamp
}

create table componentGrants {
  id serial primary key,
  fromUser int,
  toUser int,
  componentId int
}