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

# Usage (time.pondersource.com)

To export timesheet information, do something like the following snippet:
```
curl -d'["nlnet-timesh:Federated Timesheets","0","100"]' https://example:password123@time.pondersource.com/v1/print-timesheet-json
[
    {
        "id": 1,
        "worker": "ismoil",
        "project": "nlnet-timesh:Federated Timesheets",
        "timestamp_": "2022-09-12 00:00:00",
        "amount": "40",
        "description": ""
    }
]
```

In general, the structure for data submitted includes the following fields:  

`command`: this can be `worked-hours`, `update-entry`, etc.
`id`: the URI for the entry  
`worker`: the user identity whose time is being recorded  
`project`: the project the timesheet relates to  
`timestamp`: the date of the timesheet entry  
`amount`: the duration of time worked in the entry  
`description`: optional 

For each timesheet entry a "movement" and a "statement" are created. The movement is the real-world economic event. The statement is the source document from which time-pondersource-com learned about it. There can be multiple statements pointing to one movement.

Note that Prejournal (or at least this current configuration of it) uses a Source-as-Truth architecture, and its database contents will be reset from the original source documents periodically. This means write operations that you push to its API don't stick! You can push data to time-pondersource-com but it will only exist until the next time the server is reset. It will then reload its data from your system using API "pull", and (if all pushes were correct) reach the same state as before the server reset.

You can just working with import, export, remove entry. 

1) Push timesheet entries you can use 

```        
            command        timestamp             worker      project

curl -d'["20 September 2021", "stichting", "Peppol for the Masses"]' https://example:password123@time.pondersource.com/v1/worked-hours
````

2) pull timesheet information project name is optional


```
         min  max     projectName

curl -d'["0","100", "nlnet-timesh:Federated Timesheets"]' https://example:password123@time.pondersource.com/v1/print-timesheet-json

         min   max
curl -d'["0","100"]' https://example:password123@time.pondersource.com/v1/print-timesheet-json

```

3) Remove timesheet information 


```
         command        type     id

curl -d'["worked", 1]' https://example:password123@time.pondersource.com/v1/remove-entry

```

4) Update timesheet entry


```
            timestamp            worker        project                 amount   description          id
 curl -d'["23 August 2021"      "Add worker"    "Add Project"        2        "Add Description"              2]' https://example:password123@time.pondersource.com/v1/update-entry

```



### PHP CS fix

If you need to fix your PHP standard working you can go to terminal and run this command.

```
./vendor/bin/php-cs-fixer fix your_folder_that_you_would_like_to_fix
```

```
composer install
sudo apt install postgresql postgresql-contrib
cp .env.example .env
GEN_SQL=1 php schema.php > schema.sql
psql -h localhost -d prejournal -U your_username -f schema.sql
php src/cli-single.php register admin secret
perl -i -pe's/PREJOURNAL_ADMIN_PARTY=true/PREJOURNAL_ADMIN_PARTY=false/g' .env
```
If you don't have perl on your system, you can also open `.env` with a text editor and change the value for 'PREJOURNAL_ADMIN_PARTY' from 'true' to 'false' by hand.

### Run tests
```
DB_DATABASE=prejournal_test DB_USER=prejournal_test  DB_PASSWORD=123456 DB_HOST=localhost DB_DRIVER=pdo_pgsql WIKI_HOST=https://timesheet.dev3.evoludata.com/api/tabulars WIKI_TOKEN=YOUR_TOKEN ./vendor/bin/phpunit tests
PHPUnit 9.5.20 #StandWithUkraine

...........................hello
.......                                34 / 34 (100%)

Time: 00:05.803, Memory: 6.00 MB

OK (34 tests, 79 assertions)
```

### Timeld API Call
You need first go to Timeld for configuration need a username and password, that can do a setup mannualy with CLI. Copy from ```.env.example.``` to '''.env```. The Timeld host, username and password. You need to see all of this steps go to https://github.com/m-ld/timeld/blob/main/doc/api.md. After it you can use inside our project.

````
TIMELD_HOST=https://timeld.org/api
TIMELD_USERNAME=YOUR_USERNAME
TIMELD_PASSWORD=YOUR_PASSWORD
````

````                    command name      type        id             external
php src/cli-single.php timeld-api-import "Timesheet" "ismoil/ismoil" 1234
````


### Wiki API Call

You need first have a token for authorize with API Wiki Suite and take the host from Wiki Suite. This API call for getting tabulars and export and import timedata. Change in ```env``` file your credentials to your own. Add below information inside ```env``` file. You can take here https://timesheet.dev3.evoludata.com/Timesheets-homepage the first need register username and password, after you take token from Wiki Victor can send. By example you can ask me for sending token. After register you can check API sending by this link https://timesheet.dev3.evoludata.com/api/.

```
WIKI_TOKEN=GET_WIKI_TOKEN
WIKI_HOST=GET_WIKI_HOST
```

### Verify API Call

Export and Import API call POST to send invoice, and another GET documents and import invoice in JSON. First you need to sign up here [Verify](https://hub.veryfi.com/), you can find secret, username, and api key, for sending call. Change in ```env``` file your credentials to your own.

```
VERIFY_USERNAME=YOUR_USERNAME
VERIFY_CLIENT_ID=YOUR_CLIENT_ID
VERIFY_ENVIROMENT_URL=https://api.veryfi.com/
VERIFY_API_KEY=YOUR_KEY
```

After it go to the folder ```cd src/api``` You can first POST data or you can use exists documents if you have just get information. Run this command ```php verify-get.php```.

# Usage (batch processing from .pj file)

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
The app's main branch is automatically deployed to https://api.prejournal.org/ on each commit
You can try for instance:
```
curl -d'["alice","alice123"]' https://admin:secret@api.prejournal.org/v1/register # requires admin permissions
curl https://alice:alice123@api.prejournal.org/v1/hello
```
You can also create a Heroku app yourself and deploy a branch of the code there. Feel free, it's open source!

# Database schema (version 1)

See [schema.sql](./schema.sql).

## TABLES

### 1. Users

| KEY | TYPE | DESCRIPTION |
| --- | --- | --- |
| id | SERIAL PRIMARY KEY | User ID |
| username | varchar(54) UNIQUE | Current Username |
| passwordhash | varchar | password |

### 2. components

 A _component_ is can be an organisation, a department, a person, or a budget / asset group. Components will often map to accounts in GAAP, or to Agents in REA, but this mapping is not exact.

| KEY | TYPE | DESCRIPTION |
| --- | --- | --- |
| id | SERIAL PRIMARY KEY | Component's ID |
| name | varchar  UNIQUE | Component's name |



### 3. movements

Movements can be invoices or payments.

| KEY | TYPE | DESCRIPTION |
| --- |  --- |  --- |
| id | SERIAL PRIMARY KEY | Movement's ID |
| type_ | varchar(54) |'invoice', 'payment', 'worker', 'transport'| Type of movement |
| fromComponent | Integer |  From which component(ID) |
| toComponent | Integer |  To which component(ID) |
| timestamp_ | timestamp |  When the transaction happened |
| amount | decimal |   Amount of transer money |


### 4. statements

| KEY | TYPE | DESCRIPTION |
| --- | --- |  --- |
| id | SERIAL PRIMARY KEY |  Statement's ID |
| movementId | Integer |  n/a |
| userId | Integer |  Whose User's is the statement |
| sourceDocumentFormat | character |  invoice, bank statement csv file, API call etc |
| sourceDocumentFilename | character |  TODO: work out how to store files when on Heroku |
| timestamp_ | timestamp |  n/a |


### 5. componentGrants

| KEY | TYPE | DESCRIPTION |
| --- |  --- |  --- |
| id | SERIAL PRIMARY KEY | componentGrants's ID |
| fromUser | numeric | Sender(User ID) of component() |
| toUser | numeric | Receiver(ID) of component(?) |
| componentId | numeric | Which component(ID) is tranfered |

### 6. sync 

| KEY | TYPE | DESCRIPTION |
| --- |  --- |  --- |
|  internal_type |  varchar | “component” or “movement” | 
|  internal_id | numeric | Matching the internal component_id or movement_id |
|  remote_id | varchar | The identifier for this component or movement in a time tracker application | 
|  remote_system | varchar | Time tracker application | 

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



See [https://prejournal.org/example](https://prejournal.org/example) for some example PHP code.


#### Why?

In traditional (GAAP / double entry) bookkeeping, the journal already makes important choices about the system boundaries of an organisation and about depreciation time scales. For instance, if on a given day I bought a laptop and a banana, and then import my bank statement into a generic bookkeeping software package, the first transaction might get booked from `assets : bank : checking` to `assets : equipment : computers` and the other might be journaled as `liabilities : creditcard` to `expenses : groceries`.

Assets, liabilities, and expenses are fundamentally different in traditional bookkeeping, but the act of buying a laptop with your debit card is not fundamentally different from the act of buying a banana with your credit card, and when you federate bookkeeping systems, the local choices about what is an expense (something that lasts less than a month, like a banana) and what is an asset (something that lasts more than a month, like a laptop) should not get exported. That's why we are now experimenting with the federation of bookkeeping systems at the pre-journal phase.


## Commands

| Command | minimal PJ version | Usage | Example |
| ------- | ------- | ------- |  ------- |
| `createMovement` | 1.0 | Create a new Movement entry | - |
| `createStatement` | 1.0 | Create a new Statement entry | - |
| `createCompany` | 1.0 | Create a new Company entry | - |
| `enter` | 1.0 | Enter a new data for every step component,movement and statement | php src/cli-single.php enter "from component" "to component" "1.23" "2021-12-31T23:00:00.000Z" "invoice" "ponder-source-agreement-192" |
| `grant` | 1.0 | Add a new data to componentGrant | curl -d'["bob", "from component"]' http://alice:alice123@localhost:8080/v1/grant |
| `hello` | 1.0 | Works more as a test command, to check if registration was successful | `php src/cli-single.php hello` |
| `import-bank-statement` | 1.0 | - | `php src/cli-single.php import-bank-statement asnbank-CSV ./example.csv "2022-03-31 12:00:00"` |
| `import-hours` | 1.1 | Import timesheet data through CSV/JSON/XML files | `php src/cli-single.php import-hours time-CSV ./example.csv "2022-03-31 12:00:00"` |
| `import-invoice` | 1.1 | Import invoice throuh CSV/JSON/XML | `php src/cli-single.php import-bank-statement asnbank-CSV ./example.csv "2022-03-31 12:00:00"` |
| `list-new` | 1.1 | - |
| `minimal-version` | 1.0 | Check the prejournal version | `php src/cli-single.php minimal-version 1.0` |
| `pta-me` | 1.0 | - | - |
| `register <username> <password>` | 1.0 | Register a new user | `php src/cli-single.php register <username> <password>` |
| `submit-expense` (7 words) | 1.1 | Includes two movements(payment/invoice) | `php src/cli-single.php submit-expense "28 August 2021" "Dutch railways" "Degrowth Conference train tickets" "transport" 100 "michiel"`|
| `submit-expense` (8 words) | 1.2 | Includes two movements(payment/invoice) | `php src/cli-single.php submit-expense "28 August 2021" "stichting" "Dutch railways" "Degrowth Conference train tickets" "transport" 100 "michiel"`|
| `wiki-api-export` | 1.2 | Save data from Wiki API like a JSON file and save Information in database | `php src/cli-single.php wiki-api-export wiki`|
| `wiki-api-import` | 1.2 | Import data inside Wiki | `php src/cli-single.php wiki-api-import wiki`|

