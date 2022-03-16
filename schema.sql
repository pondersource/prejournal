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
  fromComponent int,
  toComponent int,
  timestamp_ timestamp,
  amount float
);

create table links (
  cause int,
  effect int
);