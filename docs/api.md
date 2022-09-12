# Usage (time.pondersource.com)


## Register a new user

The first you need a register a new user before, and give access to your user. In the curl command you can see you set username and password.

The first argument is ```username``` for example it will be alice
The second argument is ```password``` for example it will be alice123

```
curl -d'["example","password123"]' https://example:password123@time.pondersource.com/v1/register

```

## Claim a component or user that created

If you would like claim component it give you access control to our system. They will save your claimed component and userId.

The argument in curl command is ```username``` for example it will be alice

````
curl -d'["example"]' https://example:password123@time.pondersource.com/v1/claim-component
````

After it you can use the commands, that you can see bellow.

## Export timesheet Entry 

To export timesheet information, do something like the following snippet

The first agrugment in curl command `min` value 
The second argument in curl command `max` value
The third argument in curl command `project` is optional

```
curl -d'["0","100", "nlnet-timesh:Federated Timesheets"]' https://example:password123@time.pondersource.com/v1/print-timesheet-json

curl -d'["0","100"]' https://example:password123@time.pondersource.com/v1/print-timesheet-json
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

## Import timesheet Entry 

 Push timesheet entries you can use. To import to some information in our system we can use the following snippet. The first you can use import hours or importing day, or importing week, depends of your requirments. The arguments of curl command you can see bellow.

`command`: this can be `worked-hours`, `worked-day`, ``worked-week``


`timestamp`: the date of the timesheet entry  
`worker`: the user identity whose time is being recorded  
`project`: the project the timesheet relates to  
`amount`: the duration of time worked in the entry  
`description`: optional 

```        

curl -d'["20 September 2021", "stichting", "Peppol for the Masses", 4, "description can be optional"]' https://example:password123@time.pondersource.com/v1/worked-hours
```

## Remove timesheet Entry 

You can remove your movement, that you add depends of your requirments. I think there are two fields here used

`worked` type of timesheet
`id` id of the timesheet

```
curl -d'["worked", 1]' https://example:password123@time.pondersource.com/v1/remove-entry

```

## Update timesheet Entry


`timestamp`  the date of the timesheet entry 

`worker`: the user identity whose time is being recorded  

`project`: the project the timesheet relates to  

`amount`: the duration of time worked in the entry  

`description`: Your description that you would like to update

`id`: ID of the timesheet that you would like update


```
            timestamp            worker        project                 amount   description          id
 curl -d'["23 August 2021"      "Add worker"    "Add Project"        2        "Add Description"              2]' https://example:password123@time.pondersource.com/v1/update-entry

```

### Timeld API Call
You need first go to Timeld for configuration need a username and password, that can do a setup mannualy with CLI. Copy from ```.env.example.``` to '''.env```. The Timeld host, username and password. You need to see all of this steps go to https://github.com/m-ld/timeld/blob/main/doc/api.md. After it you can use inside our project.

````
TIMELD_HOST=https://timeld.org/api
TIMELD_USERNAME=YOUR_USERNAME
TIMELD_PASSWORD=YOUR_PASSWORD
````

`type` for example Timesheet, Entry .etc
`id` for example ismoil/ismoil,
`project` name.
`external` url of id of timesheet
`timestamp` the date that will be save
`amount` amount of working

````                      
 curl -d'["Timesheet", "ismoil/ismoil", "fedb/fedb", http://ex.org/timesheet/1, "22 August 2021", 8]' https://example:password123@time.pondersource.com/v1/timeld-api-import
````

### Wiki API Call

You need first have a token for authorize with API Wiki Suite and take the host from Wiki Suite. This API call for getting tabulars and export and import timedata. Change in ```env``` file your credentials to your own. Add below information inside ```env``` file. You can take here https://timesheet.dev3.evoludata.com/Timesheets-homepage the first need register username and password, after you take token from Wiki Victor can send. By example you can ask me for sending token. After register you can check API sending by this link https://timesheet.dev3.evoludata.com/api/. Import data and export data from Wiki. Save data from Wiki API like a JSON file and save Information in database.

```
WIKI_TOKEN=GET_WIKI_TOKEN
WIKI_HOST=GET_WIKI_HOST
```

`remote` for example with be wiki remote name.

````
 curl -d'["wiki"]' https://example:password123@time.pondersource.com/v1/wiki-api-export

````

````
 curl -d'["wiki"]' https://example:password123@time.pondersource.com/v1/wiki-api-import

````

### Verify API Call

Export and Import API call POST to send invoice, and another GET documents and import invoice in JSON. First you need to sign up here [Verify](https://hub.veryfi.com/), you can find secret, username, and api key, for sending call. Change in ```env``` file your credentials to your own.

```
VERIFY_USERNAME=YOUR_USERNAME
VERIFY_CLIENT_ID=YOUR_CLIENT_ID
VERIFY_ENVIROMENT_URL=https://api.veryfi.com/
VERIFY_API_KEY=YOUR_KEY
```

After it go to the folder ```cd src/api``` You can first POST data or you can use exists documents if you have just get information. Run this command ```php verify-get.php```.