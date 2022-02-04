```
cd example/
php importAcmeBankCsv.php acme-bank.csv | hledger -f - areg NL91ACME0417164300 > NL91ACME0417164300.areg
php generateImpliedPurchases.php NL91ACME0417164300.areg suppliers.json > implied.journal
php generateJournal.php NL91ACME0417164300.areg implied.journal config.json report.json