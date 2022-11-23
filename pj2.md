# PJ2 file format

The .pj format lists commands as lines in a text file.
The origin of this is that we wanted to have a restful interface to the data that would
be easily usable from the CLI, but we also wanted to batch these scripts up, into something
that becomes a hybrid between a procedural batch of commands, a flexible data format for
source documents, and a DSL.

We experienced that positional arguments in these commands quickly became cumbersome, and that
versioning the syntax of commands became hard.

Also, escaping whitespace was unnecessarily cumbersome, and it felt unnecessary to use anything
else than JSON in our server API.

So the PJ2 format is the same as the PJ format, but restricted to a fresh set of commands, defined
from scratch, and in JSON. We renamed the commands to entries, to shift the emphasis away from the
procedural and towards the declarative. This is the specification of PJ2!

## Specification
A PJ2 file is a JSON document containing an array of entries. Each entry is an object, with at least
a "type" field. Depending on that field, we can interpret the entry as follows.

### Worked
Fields:
* type: "worked"
* worker: string
* organization: string
* project: string
* date: string with three-letter month, e.g. "12 Jan 2022"
* hours: number
* description: string (optional)

Interpretation:
A `worked` entry describes an event where a `worker` worked on a `project` for an `organization`, for a number of `hours` on a given `date`.
Multiple `worked` entries can exist for the same combination of worker, organization, project and date. The amounts of hours just sum up in
this case.

### Salary
Fields:
* type: "salary"
* worker: string
* organization: string
* amount: number
* paid: string with three-letter month, e.g. "26 Jan 2022"
* from: string with three-letter month, e.g. "1 Jan 2022"
* to: string with three-letter month, e.g. "31 Jan 2022"
* description: string (optional)

Interpretation:
All hours the worker did for this organization on `from` or on `to` or on any day inbetween, are compensated by the payment made on `paid`.

### Contract
Fields:
* type: "contract"
* worker: string
* organization: string
* hours: number
* amount: number
* from: string with three-letter month, e.g. "1 Jan 2022"
* to: string with three-letter month, e.g. "31 Jan 2022"

The `worker` should work for the `organization` for `hours` hours each week from `from` to `to` inclusive, and receive `amount` salary each month.
If similar contracts overlap, their hours and amounts add up. Two checks will be performed:
* for each week, was the correct number of hours logged?
* for each month, was the correct salary amount paid?

### Expense
Fields:
* type: "expense"
* worker: string (optional)
* organization: string
* supplier: string
* amount: number
* paid: string with three-letter month, e.g. "26 Jan 2022"
* from: string with three-letter month, e.g. "1 Jan 2022"
* to: string with three-letter month, e.g. "31 Jan 2022"
* reimbursed: string with three-letter month, e.g. "26 Jan 2022" (optional)
* description: string (optional)

Interpretation:
The worker (optional) or the organization paid `amount` for something from `supplier` for the `organization`,
which is useful on `from` and on `to` and on any day inbetween.
The organization may have `reimbursed` the expense or not (this field is optional).
In the meantime, the worker is entitled to 1% interest per month.
In practice an expense would usually be bound to some project or to some workers, but we're not modelling that relationship (yet).

### Loan
Fields:
* type: "loan"
* worker: string
* organization: string
* amount: number
* paid: string with three-letter month, e.g. "26 Jan 2022"
* reimbursed: string with three-letter month, e.g. "26 Jan 2022" (optional)
* description: string (optional)

Interpretation:
The worker either transferred money to the organization, or did not receive their salary.
The organization may have `reimbursed` the loan or not (this field is optional). In the meantime, the worker is entitled to 1% interest per month.

### Income
Fields:
* type: "income"
* organization: string
* project: string
* paid: string with three-letter month, e.g. "12 Jan 2022"
* from: string with three-letter month, e.g. "12 Jan 2022"
* to: string with three-letter month, e.g. "12 Jan 2022"
* amount: number
* description: string (optional)

Interpretation:
All the hours worked and expenses made for the project on `from`, `to`, or any day inbetween, are compensated by the payment made on `paid`.

## Modelling
Timesheets go into `worked` events.
Incoming invoices go into `expense` events (may have been paid by a worker or by the organization itself).
Outgoing invoices go into `income` events.
Loans go into `loan` events.
That's about it! :)
Then, for each period you can calculate how much we earned on each project;
See which percentage of total income over a period was spent on expenses.
Spread the salary costs over the projects according to hours worked.
Now for each day of the calendar, for each project, you can see:

salary costs + expenses costs + profit = income.

The organization's equity is the accummulative sum of income over all projects.
