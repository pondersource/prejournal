```
cd example/
php importAcmeBankCsv.php acme-bank.csv | hledger -f - print -x > imported.journal
php generateImpliedPurchases.php imported.journal suppliers.json NL91ACME0417164300 | hledger -f - print -x > implied.journal
cat imported.journal implied.journal >> main.journal
php generateJournal.php main.journal config.json report.json