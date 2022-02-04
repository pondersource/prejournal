This code is currently broken:

```sh
php importAsnCsv.php asn-checking-account-statement.csv > asn-checking.journal
php importAsnCsv.php asn-savings-account-statement.csv > asn-savings.journal
php mergeJournals.php asn-checking.journal asn-savings.journal > asn-merged.journal
```