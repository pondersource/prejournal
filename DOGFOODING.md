# Instructions for alpha users of https://app.prejournal.org/

## Export your employee data to csv
You should have received a `<username>` and `<password>` from Michiel via Slack.

```sh
curl https://<username>:<password>@api.prejournal.org/v1/pta-me > pondersource.journal
hledger bs -f pondersouce.journal
```

## Import from Ponder Source source documents (instructions for Michiel only)
To regenerate the database contents from source documents (this requires access to the private books repo for Stichting Ponder Source):
```sh
git remote -v # should have a remote 'heroku'	to https://git.heroku.com/prejournal.git
heroku pg:backups:capture
heroku pg:backups:download
GEN_SQL_PG=1 php schema.php | heroku psql
node ../../pondersource-books/stichting/reports/to-prejournal-sql.js | heroku psql
sh ../../pondersource-books/stichting/employee-passwords.sh
```

