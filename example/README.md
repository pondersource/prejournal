Here is an example:
```
git clone https://github.com/federatedbookkeeping/prejournal
cd prejournal
cd example/
php importAcmeBankCsv.php acme-bank.csv | hledger -f - print -x > imported.journal
php generateImpliedPurchases.php imported.journal suppliers.json NL91ACME0417164300 | hledger -f - print -x > implied.journal
cat imported.journal implied.journal > main.journal
php generateJournal.php main.journal config.json report.json | hledger -f - print > 2022.journal
hledger -f 2022.journal close >> 2022.journal
```

Expected output: see [2022.journal](./2022.journal).
