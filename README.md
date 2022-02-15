# prejournal
An experiment in pre-journal bookkeeping.

Like the [Resources-Events-Agents (REA)](http://mikorizal.org/Fromprivateownershipaccountingtocommonsaccoun.html) model, this is an alternative bookkeeping model. Alternative to dual entry / "generally accepted accounting principles" bookkeeping, that is. It takes a bird's eye view of the economic network, instead of an organisation-centric view.

# In a nutshell

There are five elements to the Prejournal model:
* A _component_ is can be an organisation, a department, a person, or a budget / asset group. Components will often map to accounts in GAAP, or to Agents in REA, but this mapping is not exact.
* A _capacitor_ (like a series capacitor in electronics) holds a balance between two parties. It has a current balance which changes based on invoices, payments and settlements, and can have a maximum and a minimum balance.
* An _invoice_ is an actual invoice (as in, the business document sent from seller to buyer, stating what is owed), but it can also be a different type of situation in which money becomes owed, for instance, on pay day, you would book an "invoice" from each worker to the organisation, even though workers don't really send invoice documents on pay day. Actually it would be more correct to point at the delivery of the goods or service than at the invoicing of the goods or service, but invoices are business documents that can enter a bookkeeping system in a machine-readable way through e-Invoicing, hence the emphasis on them.
* A _payment_ is the movement (transfer, transaction) of money. These generally enter the bookkeeping system when you import a (csv) bank statement.
* A _settlement_ is a loop of events that links a payment to an invoice and closes the business interaction. These enter the system when you _reconcile_ the bank statement entries with the invoices.

In standard bookkeeping, the invoices and bank statements are source document, and from there, the journal is generated. In the journal, accounts are divided into assets, liabilities, expenses, income, and equity. Prejournal makes no such division, although the idea is that a standard journal can be generated from the prejournal model, so that we can still export our data to the language that accountants understand (hence the name).

For instance: Joe works for ACME Corp and buys a laptop from a computer shop, paying with his personal debit card. He then submits the expense and now ACME Corp owes Joe the money he spent at the computer shop.

With the invoice, the computer moves from the shop to ACME Corp.
With the payment, the money moves from Joe's bank account (the capacitor between Joe and my bank) to the computer shop's bank account.
With the settlement, ACME Corp accepts Joe's expense, and commits to owing Joe the reimbursement.

Components:
1. ACME Corp
2. Joe
3. Joe's bank
4. computer shop
5. computer shop's bank

![diagram](https://user-images.githubusercontent.com/408412/154058670-70949077-9365-4047-9abf-4220c7d3c548.jpg)

In the diagram the settlement takes a shortcut, not going through the two banks. I still don't know exactly how to model this. Work in progress! :)
When exporting this to [plain text accounting (PTA)](https://plaintextaccounting.org) journal format for ACME Corp, the journal entry would be something like:
```
1/1/2022 Laptop (expensed by Joe)
  assets:computer equipment  USD 1000
  liabilities:accounts payable:Joe
```

And when generating the PTA books for Joe, it would be something like:
```
1/1/2022 Laptop (expensed for work)
  assets:bank:checking         USD -1000
  assets:accounts receivable:ACME Corp
```

Depending on which component (ACME Corp or Joe) you generate the journal for, the journal looks different. The same would happen if you generate the books for different departments, sub-departments and projects of an organisation. Or if you merge two bookkeeping systems of a company and its suppplier, for instance if this supplier was acquired.

That's why GAAP journals can not really be considered as a database model, they are already better understood as query results, and the underlying data model should be something that sits inbetween the source documents and the journal: "prejournal". :)



See [https://prejournal.org/example](https://prejournal.org/example) for some example PHP code.


## Why?

In traditional (GAAP / double entry) bookkeeping, the journal already makes important choices about the system boundaries of an organisation and about depreciation time scales. For instance, if on a given day I bought a laptop and a banana, and then import my bank statement into a generic bookkeeping software package, the first transaction might get booked from `assets : bank : checking` to `assets : equipment : computers` and the other might be journaled as `liabilities : creditcard` to `expenses : groceries`.

Assets, liabilities, and expenses are fundamentally different in traditional bookkeeping, but the act of buying a laptop with your debit card is not fundamentally different from the act of buying a banana with your credit card, and when you federate bookkeeping systems, the local choices about what is an expense (something that lasts less than a month, like a banana) and what is an asset (something that lasts more than a month, like a laptop) should not get exported. That's why we are now experimenting with the federation of bookkeeping systems at the pre-journal phase.
