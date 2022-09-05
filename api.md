# Usage (time.pondersource.com)

To export timesheet information, do something like the following snippet, the project is optional argument:
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

You can just working with import, export, remove entry. 

1) Push timesheet entries you can use 

```        
            command        timestamp             worker      project

curl -d'["20 September 2021", "stichting", "Peppol for the Masses"]' https://example:password123@time.pondersource.com/v1/worked-hours
````

2) pull timesheet information project name can be optional

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

### Timeld API Call
You need first go to Timeld for configuration need a username and password, that can do a setup mannualy with CLI. Copy from ```.env.example.``` to '''.env```. The Timeld host, username and password. You need to see all of this steps go to https://github.com/m-ld/timeld/blob/main/doc/api.md. After it you can use inside our project.

````
TIMELD_HOST=https://timeld.org/api
TIMELD_USERNAME=YOUR_USERNAME
TIMELD_PASSWORD=YOUR_PASSWORD
````

````                    
         type               id        external      
 curl -d'["Timesheet", "ismoil/ismoil", 1234]' https://example:password123@time.pondersource.com/v1/timeld-api-import
````

### Wiki API Call

You need first have a token for authorize with API Wiki Suite and take the host from Wiki Suite. This API call for getting tabulars and export and import timedata. Change in ```env``` file your credentials to your own. Add below information inside ```env``` file. You can take here https://timesheet.dev3.evoludata.com/Timesheets-homepage the first need register username and password, after you take token from Wiki Victor can send. By example you can ask me for sending token. After register you can check API sending by this link https://timesheet.dev3.evoludata.com/api/. Import data and export data from Wiki. Save data from Wiki API like a JSON file and save Information in database.

```
WIKI_TOKEN=GET_WIKI_TOKEN
WIKI_HOST=GET_WIKI_HOST
```

````
          remote
 curl -d'["wiki"]' https://example:password123@time.pondersource.com/v1/wiki-api-export

````

````
          remote
 curl -d'["wiki"]' https://example:password123@time.pondersource.com/v1/wiki-api-import

````