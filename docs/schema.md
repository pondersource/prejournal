# Database schema (version 2)

See [schema.sql](https://github.com/pondersource/prejournal/blob/main/schema.sql).

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
| userId | Integer | Movement's ID |
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
| description | varchar |  Description about each movement |
| internal_type |  varchar | “component” or “movement” | 
| internal_id | numeric | Matching the internal component_id or movement_id |
| remote_id | varchar | The identifier for this component or movement in a time tracker application | 
| remote_system | varchar | Time tracker application | 


### 5. componentGrants

| KEY | TYPE | DESCRIPTION |
| --- |  --- |  --- |
| id | SERIAL PRIMARY KEY | componentGrants's ID |
| fromUser | numeric | Sender(User ID) of component() |
| toUser | numeric | Receiver(ID) of component(?) |
| componentId | numeric | Which component(ID) is tranfered |