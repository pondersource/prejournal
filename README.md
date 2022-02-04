# prejournal
An experiment in pre-journal bookkeeping. See [https://prejournal.org/example](https://prejournal.org/example).

```sh
php importAsnCsv.php asn-checking-account-statement.csv > asn-checking.journal
php importAsnCsv.php asn-savings-account-statement.csv > asn-savings.journal
php mergeJournals.php asn-checking.journal asn-savings.journal > asn-merged.journal
```

## Why?

In traditional (GAAP / double entry) bookkeeping, the journal already makes important choices about the system boundaries of an organisation and about depreciation time scales. For instance, if on a given day I bought a laptop and a banana, and then import my bank statement into a generic bookkeeping software package, the first transaction might get booked from `assets : bank : checking` to `assets : equipment : computers` and the other might be journaled as `liabilities : creditcard` to `expenses : groceries`.

Assets, liabilities, and expenses are fundamentally different in traditional bookkeeping, but the act of buying a laptop with your debit card is not fundamentally different from the act of buying a banana with your credit card, and when you federate bookkeeping systems, the local choices about what is an expense (something that lasts less than a month, like a banana) and what is an asset (something that lasts more than a month, like a laptop) should not get exported. That's why we are now experimenting with the federation of bookkeeping systems at the pre-journal phase.
