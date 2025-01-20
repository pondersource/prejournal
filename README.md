# prejournal
An experiment in pre-journal bookkeeping.

Like the [Resources-Events-Agents (REA)](http://mikorizal.org/Fromprivateownershipaccountingtocommonsaccoun.html) model, this is an alternative bookkeeping model. Alternative to dual entry / "generally accepted accounting principles" bookkeeping, that is. It takes a bird's eye view of the economic network, instead of an organisation-centric view.

# Development
Note that the `psql` command below will drop and recreate all tables in your `prejournal` database on localhost psql
or wherever you have pointed the DATABASE_URL in your `.env` file, so be careful
if that's not what you want. :). I think we need to working with something like a username, password, and provider that will setup in ```.env.example```. You can setup your usernamem database, password .etc. We are using PostqresSQL database by default.

```
DB_USER=prejournal_test
DB_DATABASE=prejournal_test
DB_PASSWORD=123456
DB_HOST=localhost
DB_DRIVER=pdo_pgsql
``` 

# Docker

For the [Docker testnet of Federated Timesheets](https://github.com/federatedbookkeeping/timesheets/issues/32) you can do the following:
```sh
docker build -t pj -f Dockerfile .
docker build -t pjdb -f Dockerfile-postgres .
docker run -d --network=testnet --name=pjdb -e POSTGRES_PASSWORD=mysecretpassword pjdb
docker run -d --network=testnet --name=admin pj
docker run -d --network=testnet --name=pj pj
docker ps
# should show two containers running
docker exec pjdb /bin/bash -c "echo CREATE DATABASE prejournal\; | psql -U postgres"
# should output: CREATE DATABASE
docker exec pjdb /bin/bash -c "psql -U postgres prejournal < schema.sql"
# should output:
# DROP TABLE
# NOTICE:  table "users" does not exist, skipping
# CREATE TABLE
# NOTICE:  table "components" does not exist, skipping
# etc

docker exec -it admin /bin/bash -c "echo PREJOURNAL_ADMIN_PARTY=true >> .env"
docker exec -it admin /bin/bash -c "curl -d'["alice","alice123"]' http://localhost:80/v1/register"
docker exec -it admin /bin/bash -c "curl -d'["bob","bob123"]' http://localhost:80/v1/register"

docker exec -it pj /bin/bash -c "curl -d'["alice"]' http://alice:alice123@localhost:80/v1/claim-component"
docker exec -it pj /bin/bash -c "curl -d'["bob"]' http://bob:bob123@localhost:80/v1/claim-component"
docker exec -it pj /bin/bash -c "curl -d'[\"23 Sep 2022\",\"nlnet-timesh\",\"Federated Timesheets\", 8, \"hard work\"]' http://bob:bob123@localhost:80/v1/worked-hours"
```
Now you created two users, Alice and Bob, and Alice has one timesheet entry, worked 8 hours on Federated Timesheets for client 'nlnet-timesh, on 23 Sep 2022, with description 'hard work'.

### PHP CS fix

If you need to fix your PHP standard working you can go to terminal and run this command.

```
./vendor/bin/php-cs-fixer fix your_folder_that_you_would_like_to_fix
```

### Usage Prejournal locally

```
composer install
sudo apt install postgresql postgresql-contrib
cp .env.example .env
GEN_SQL=1 php schema.php > schema.sql
psql -h localhost -d prejournal -U your_username -f schema.sql
php src/cli-single.php register admin secret
php src/cli-single.php claim-component "admin"
perl -i -pe's/PREJOURNAL_ADMIN_PARTY=true/PREJOURNAL_ADMIN_PARTY=false/g' .env
```
Set in `env` file `PREJOURNAL_OPEN_MODE` to true. If you don't have perl on your system, you can also open `.env` with a text editor and change the value for 'PREJOURNAL_ADMIN_PARTY' from 'true' to 'false' by hand.

### Run tests
```
DB_DATABASE=prejournal_test DB_USER=prejournal_test  DB_PASSWORD=123456 DB_HOST=localhost DB_DRIVER=pdo_pgsql WIKI_HOST=https://timesheet.dev3.evoludata.com/api/tabulars WIKI_TOKEN=YOUR_TOKEN  PREJOURNAL_OPEN_MODE=false ./vendor/bin/phpunit tests
PHPUnit 9.5.20 #StandWithUkraine

...........................hello
.......                                34 / 34 (100%)

Time: 00:05.803, Memory: 6.00 MB

OK (34 tests, 79 assertions)
```

### Documentation

If you would like to see **API** integration you can see [here](https://github.com/pondersource/prejournal/blob/main/docs/api.md). We can talk about **Database Schema** and you can see [here](https://github.com/pondersource/prejournal/blob/main/docs/schema.md). If you would like to see the all **commands** you can see [here](https://github.com/pondersource/prejournal/blob/main/docs/commands.md).


### Usage (batch processing from .pj file)

The `.pj` file format is a very simple batch processing file format.
Each line is a command.
Each command consists of space-separated words.
A word can be quoted (surrounded by `"`) or unquoted.
If a word is unquoted, it cannot contain spaces, because then the space would be interpreted as the start of the next word.
If a word is quoted, it can contain spaces, since for the parser the next word would start after `" `.
Both quoted and unquoted words can contain quotes inside them; the only limitation is that there is no way to put a quote followed by a space (`" `) inside a command word.

Example of a `.pj` file that shows quoted vs unquoted words:

```pj
do-something arg1 arg2 arg3
do-something-else "accounts payable" 1.23
word"with"quote "quoted word"
```

Example of a `.pj` file that checks the Prejournal version and says Hello to the current user:
```pj
minimal-version 1.0
hello
```

```sh
php src/cli-batch.php hello.pj
```

Example output:
```sh
Hello admin, your userId is 1
```

```sh
php src/cli-batch.php example.pj
```

Example output:
```sh
exact match
Created movement 1
Created statement 1
Created movement 2
Created statement 2
Created movement 3
Created statement 3
Blank link in batch file
Created movement 4
[...]
```

# Usage (CLI)

The code is made platform independent through `src/platform.php`. To execute on the command line, try for instance:

* Run through the steps detailed above under [#Development](#development).
* Run `php src/cli-single.php hello`
* If you want to use a .env file from a different directory, try:
```
PREJOURNAL_ENV_FILE_DIR=`pwd` php ../../pondersource/prejournal/src/cli-single.php hello
```

# Usage (localhost)

* `php -S localhost:8080 src/server.php`
* Visit http://localhost:8080/v1/hello with your browser
* Or try:
  * `curl -d'["alice","alice123"]' http://admin:secret@localhost:8080/v1/register` (temporarily set `PREJOURNAL_ADMIN_PARTY=true` to create the 'admin' user)
  * `curl http://alice:alice123@localhost:8080/v1/hello`
* The username and password will be taken from http basic auth if present.
* Otherwise, the username and password will be taken from `.env` PREJOURNAL_USERNAME / PREJOURNAL_PASSWORD if present.

NB: In general, you would never put a password in a URL or even in a `.env` file;
we're doing this here to simplify the setup during rapid initial development. See [#9](https://github.com/pondersource/prejournal/issues/9).

# Usage (Heroku)
The app's main branch is automatically deployed to https://api.prejournal..../ on each commit
You can try for instance:
```
curl -d'["alice","alice123"]' https://admin:secret@api.prejournal..../v1/register # requires admin permissions
curl https://alice:alice123@api.prejournal..../v1/hello
```
You can also create a Heroku app yourself and deploy a branch of the code there. Feel free, it's open source!


### The idea behind

In standard bookkeeping, the invoices and bank statements are source document, and from there, the journal is generated. In the journal, accounts are divided into assets, liabilities, expenses, income, and equity. Prejournal makes no such division, although the idea is that a standard journal can be generated from the prejournal model, so that we can still export our data to the language that accountants understand (hence the name).

For instance: Joe works for ACME Corp and buys a laptop from a computer shop, paying with his personal debit card. He then submits the expense and now ACME Corp owes Joe the money he spent at the computer shop.

With the invoice, the computer moves from the shop to ACME Corp.
With the payment, the money moves from Joe's bank account (the capacitor between Joe and my bank) to the computer shop's bank account.
With the settlement, ACME Corp accepts Joe's expense, and commits to owing Joe the reimbursement.

Components:
1. ACME Corp
2. Joe
3. Joe's bank
4. computer shop
5. computer shop's bank

![diagram](https://user-images.githubusercontent.com/408412/154058670-70949077-9365-4047-9abf-4220c7d3c548.jpg)

In the diagram the settlement takes a shortcut, not going through the two banks. I still don't know exactly how to model this. Work in progress! :)
When exporting this to [plain text accounting (PTA)](https://plaintextaccounting.org) journal format for ACME Corp, the journal entry would be something like:
```
1/1/2022 Laptop (expensed by Joe)
  assets:computer equipment  USD 1000
  liabilities:accounts payable:Joe
```

And when generating the PTA books for Joe, it would be something like:
```
1/1/2022 Laptop (expensed for work)
  assets:bank:checking         USD -1000
  assets:accounts receivable:ACME Corp
```

Depending on which component (ACME Corp or Joe) you generate the journal for, the journal looks different. The same would happen if you generate the books for different departments, sub-departments and projects of an organisation. Or if you merge two bookkeeping systems of a company and its supplier, for instance if this supplier was acquired.

That's why GAAP journals can not really be considered as a database model, they are already better understood as query results, and the underlying data model should be something that sits inbetween the source documents and the journal: "prejournal". :)



See [https://prejournal..../example](https://prejournal..../example) for some example PHP code.


#### Why?

In traditional (GAAP / double entry) bookkeeping, the journal already makes important choices about the system boundaries of an organisation and about depreciation time scales. For instance, if on a given day I bought a laptop and a banana, and then import my bank statement into a generic bookkeeping software package, the first transaction might get booked from `assets : bank : checking` to `assets : equipment : computers` and the other might be journaled as `liabilities : creditcard` to `expenses : groceries`.

Assets, liabilities, and expenses are fundamentally different in traditional bookkeeping, but the act of buying a laptop with your debit card is not fundamentally different from the act of buying a banana with your credit card, and when you federate bookkeeping systems, the local choices about what is an expense (something that lasts less than a month, like a banana) and what is an asset (something that lasts more than a month, like a laptop) should not get exported. That's why we are now experimenting with the federation of bookkeeping systems at the pre-journal phase.

