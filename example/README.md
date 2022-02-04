```
cd example/
php importAcmeBankCsv.php acme-bank.csv | hledger -f - print -x > imported.journal
php generateImpliedPurchases.php imported.journal suppliers.json NL91ACME0417164300 | hledger -f - print -x > implied.journal
cat imported.journal implied.journal > main.journal
php generateJournal.php main.journal config.json report.json
```

Expected output:
```
Date:2022-01-16
From assets:bank:checking
To assets:groceries
Amount:16.47
Date:2022-01-19
From assets:bank:checking
To assets:equipment:laptop
Amount:799
Date:2022-01-25
From income:salary:bookiez
To assets:bank:checking
Amount:1000
```