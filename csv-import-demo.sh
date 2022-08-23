#!/bin/bash
set -e

cat ./schema.sql | psql prejournal
curl -d'["admin","secret"]' http://admin:secret@localhost:8080/v1/register
curl -d'["admin"]' http://admin:secret@localhost:8080/v1/claim-component
curl -i -d'["default-project"]' http://admin:secret@localhost:8080/v1/print-timesheet-json

curl -i --data-binary "@tests/fixtures/saveMyTime-CSV.csv" -H"Content-Type:saveMyTime-CSV" http://admin:secret@localhost:8080/v1-upload/import-hours
curl -i --data-binary "@tests/fixtures/scoro-JSON.json" -H"Content-Type:scoro-JSON" http://admin:secret@localhost:8080/v1-upload/import-hours
curl -i --data-binary "@tests/fixtures/stratustime-JSON.json" -H"Content-Type:stratustime-JSON" http://admin:secret@localhost:8080/v1-upload/import-hours
curl -i --data-binary "@tests/fixtures/time-CSV.csv" -H"Content-Type:saveMyTime-CSV" http://admin:secret@localhost:8080/v1-upload/import-hours
curl -i --data-binary "@tests/fixtures/timeBro-CSV.csv" -H"Content-Type:saveMyTime-CSV" http://admin:secret@localhost:8080/v1-upload/import-hours
curl -i --data-binary "@tests/fixtures/timecamp-CSV.csv" -H"Content-Type:saveMyTime-CSV" http://admin:secret@localhost:8080/v1-upload/import-hours
curl -i --data-binary "@tests/fixtures/timeDoctor-CSV.csv" -H"Content-Type:saveMyTime-CSV" http://admin:secret@localhost:8080/v1-upload/import-hours
curl -i --data-binary "@tests/fixtures/timely-CSV.csv" -H"Content-Type:saveMyTime-CSV" http://admin:secret@localhost:8080/v1-upload/import-hours
curl -i --data-binary "@tests/fixtures/timeManager-CSV.csv" -H"Content-Type:saveMyTime-CSV" http://admin:secret@localhost:8080/v1-upload/import-hours
curl -i --data-binary "@tests/fixtures/timesheet-CSV.csv" -H"Content-Type:saveMyTime-CSV" http://admin:secret@localhost:8080/v1-upload/import-hours
curl -i --data-binary "@tests/fixtures/timesheetMobile-CSV.csv" -H"Content-Type:saveMyTime-CSV" http://admin:secret@localhost:8080/v1-upload/import-hours
curl -i --data-binary "@tests/fixtures/timetracker-XML.xml" -H"Content-Type:saveMyTime-CSV" http://admin:secret@localhost:8080/v1-upload/import-hours
curl -i --data-binary "@tests/fixtures/timetrackerCli-JSON.json" -H"Content-Type:saveMyTime-CSV" http://admin:secret@localhost:8080/v1-upload/import-hours
curl -i --data-binary "@tests/fixtures/timetip-JSON.json" -H"Content-Type:saveMyTime-CSV" http://admin:secret@localhost:8080/v1-upload/import-hours
curl -i --data-binary "@tests/fixtures/timely-CSV." -H"Content-Type:saveMyTime-CSV" http://admin:secret@localhost:8080/v1-upload/import-hours

curl -i -d'["default-project"]' http://admin:secret@localhost:8080/v1/print-timesheet-json

# [Savemytime](https://play.google.com/store/apps/details?id=com.godmodev.optime)
# [Scoro](https://www.scoro.com/time-management-software/)
# [Stratustime](https://stratustime.centralservers.com/)
# [Time](https://github.com/wbbly/time)
# [TimeBro](https://www.timebro.com/)
# [Timecamp](https://www.timecamp.com/)
# [Time Doctor](https://www.timedoctor.com/)
# [Timely](https://www.freshworks.com/apps/freshdesk/timely/)
# [Time Manager](https://apps.nextcloud.com/apps/timemanager)
# [Timesheet](https://play.google.com/store/apps/details?id=robin.urenapp)
# [Timesheet Mobile](https://apps.apple.com/us/app/timesheet-mobile/id560462162)
# Timesheets Time Tracker (see Veryfi)
# [Timetip](https://github.com/rstacruz/timetip)
# [Timetracker by Anuko](https://github.com/anuko/timetracker)
# [Time Tracker CLI](https://github.com/danibram/time-tracker-cli)
# [Time Tracker](https://play.google.com/store/apps/details?id=com.cg.android.ebillitytimetracker)
# [Time Tracker](https://apps.nextcloud.com/apps/timetracker)
