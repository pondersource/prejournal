## Billing API PHP Client

### Requirements
- PHP 7.3+ or 8.x

A PHP client for the Billing Platform API working for Invoices, Account, Apps .etc

### Usage
```composer require pondersource/billing-api```
* Checkout this repo
* Create a .env file you can do it go to the CLI and run ```cp .env.example .env```
* Comment/uncomment the service for which you want to retrieve invoices or usage info (Google, AWS, Github, Heroku).
* Run `composer install`
* Go to https://github.com/settings/tokens/new and create a personal access token. Tick 'admin:org' and 'user' as scopes.
* Save this in your `.env` file as:
```
GITHUB_ACCESS_TOKEN=ghp_0AwgbEb....
```
* You need to show your own Github Username:
```
$user_billing = $github->getUserSharedStorageBilling("michielbdejong");
```
* Run `echo -n; heroku auth:token` to get a personal token for Heroku or you can do it simple just run `heroku auth:token` inside CLI.
* Save this in your `.env` file as:
```
HEROKU_API_KEY=30a84169-7c38-4b71-a19...
```
* Working with google you need to run first ```cloud auth application-default login``` from CLI
* It generate you json file that you can copy and paste inside Google
 ```putenv('GOOGLE_APPLICATION_CREDENTIALS='.realpath("application_default_credentials.json"))```
* Run `php index.php`
* When you run `php index.php` every service will generate xml or json array collection of item UBL and JSON

## Heroku API Client

````php
use PonderSource\HerokuApi\HerokuClient;

$her = new HerokuClient([
    'apiKey' =>  getenv('HEROKU_API_KEY'),
]);

$her->getHerokuInvoice();
$her->getHerokuTeamInvoices();
````

We are using Heroku Invoices for simple user and we can use in the same way for the teams and I will show you response of one of the function. We will work directly with platform API of the Heroku and fetching inforamtion.

* Response json from first method for simple Heroku Invoice

```json
[
    {
        "charges_total": 0,
        "created_at": "2022-02-17T02:45:30Z",
        "credits_total": 0,
        "id": "22e1c650-291e-48b7-b462-4bbd9eba4a0e",
        "number": 62983158,
        "period_end": "2022-03-01",
        "period_start": "2022-02-01",
        "state": 1,
        "total": 0,
        "updated_at": "2022-03-01T12:59:11Z"
    },
    {
        "charges_total": 0,
        "created_at": "2022-03-01T02:44:04Z",
        "credits_total": 0,
        "id": "e4bbdb15-40db-4de8-b89f-d2f921766302",
        "number": 63410419,
        "period_end": "2022-03-17",
        "period_start": "2022-03-01",
        "state": 0,
        "total": 0,
        "updated_at": "2022-03-17T02:40:34Z"
    }
]
```
* Reponse UBL from first method for simple Heroku Invoice

```xml
<?xml version="1.0"?>
<x1:invoice xmlns:x1="http://example.org/invoices">
 <x1:InvoiceTeamItem xmlns:x1="http://example.org/invoices">
  <x1:charges_total xmlns:x1="http://example.org/invoices">0</x1:charges_total>
  <x1:created_at xmlns:x1="http://example.org/invoices">2022-02-17T02:45:30Z</x1:created_at>
  <x1:credits_total xmlns:x1="http://example.org/invoices"/>
  <x1:id xmlns:x1="http://example.org/invoices">22e1c650-291e-48b7-b462-4bbd9eba4a0e</x1:id>
  <x1:number xmlns:x1="http://example.org/invoices">62983158</x1:number>
  <x1:period_start xmlns:x1="http://example.org/invoices">2022-02-01</x1:period_start>
  <x1:period_end xmlns:x1="http://example.org/invoices">2022-03-01</x1:period_end>
  <x1:state xmlns:x1="http://example.org/invoices">1</x1:state>
  <x1:total xmlns:x1="http://example.org/invoices">0</x1:total>
  <x1:updated_at xmlns:x1="http://example.org/invoices">2022-03-01T12:59:11Z</x1:updated_at>
 </x1:InvoiceTeamItem>
 <x1:InvoiceTeamItem xmlns:x1="http://example.org/invoices">
  <x1:charges_total xmlns:x1="http://example.org/invoices">0</x1:charges_total>
  <x1:created_at xmlns:x1="http://example.org/invoices">2022-03-01T02:44:04Z</x1:created_at>
  <x1:credits_total xmlns:x1="http://example.org/invoices"/>
  <x1:id xmlns:x1="http://example.org/invoices">e4bbdb15-40db-4de8-b89f-d2f921766302</x1:id>
  <x1:number xmlns:x1="http://example.org/invoices">63410419</x1:number>
  <x1:period_start xmlns:x1="http://example.org/invoices">2022-03-01</x1:period_start>
  <x1:period_end xmlns:x1="http://example.org/invoices">2022-03-17</x1:period_end>
  <x1:state xmlns:x1="http://example.org/invoices">0</x1:state>
  <x1:total xmlns:x1="http://example.org/invoices">0</x1:total>
  <x1:updated_at xmlns:x1="http://example.org/invoices">2022-03-17T02:40:34Z</x1:updated_at>
 </x1:InvoiceTeamItem>
</x1:invoice>

```
## Google API Client
````php
use PonderSource\GoogleApi\Google;

$google = new Google([
    'apiKey' => putenv('GOOGLE_APPLICATION_CREDENTIALS='.realpath("application_default_credentials.json"))
]);
$google->getCloudbillingSkus();

````

Inside your json file you will generate it in the up you can see instruction. We are using Google Cloud library for take information for services and other stuff.

* Response from JSON one of example
```json
[
    {
        "sku_name": "services\/0069-3716-5463\/skus\/00C0-AE90-D5AE",
        "sku_id": "00C0-AE90-D5AE",
        "sku_description": "Custom Commit Plan QUser",
        "sku_provider_name": "Qubole",
        "sku_service_name": "Qubole Data Service",
        "sku_resource": "ApplicationServices",
        "sku_group": "Qubole",
        "sku_usage_type": "OnDemand",
        "sku_effective_time": "18:42:26",
        "sku_usage_unit": "mo",
        "sku_usage_unit_description": "month",
        "sku_base_unit": "s",
        "sku_base_unit_description": "second",
        "sku_base_unit_conversion_factor": 2674800,
        "sku_display_quantity": 1,
        "sku_start_usage_amount": 0,
        "sku_unit_price": "USD"
    },
]
```

* Response UBL show one of example

```xml
<?xml version="1.0"?>
<x1:billing xmlns:x1="http://example.org/billings">
 <x1:BillingItem xmlns:x1="http://example.org/billings">
  <x1:sku_name xmlns:x1="http://example.org/billings">services/0069-3716-5463/skus/00C0-AE90-D5AE</x1:sku_name>
  <x1:sku_id xmlns:x1="http://example.org/billings">00C0-AE90-D5AE</x1:sku_id>
  <x1:sku_description xmlns:x1="http://example.org/billings">Custom Commit Plan QUser</x1:sku_description>
  <x1:sku_provider_name xmlns:x1="http://example.org/billings">Qubole</x1:sku_provider_name>
  <x1:sku_service_name xmlns:x1="http://example.org/billings">Qubole Data Service</x1:sku_service_name>
  <x1:sku_resource xmlns:x1="http://example.org/billings">ApplicationServices</x1:sku_resource>
  <x1:sku_group xmlns:x1="http://example.org/billings">Qubole</x1:sku_group>
  <x1:sku_usage_type xmlns:x1="http://example.org/billings">OnDemand</x1:sku_usage_type>
  <x1:sku_effective_time xmlns:x1="http://example.org/billings">18:42:26</x1:sku_effective_time>
  <x1:sku_usage_unit xmlns:x1="http://example.org/billings">mo</x1:sku_usage_unit>
  <x1:sku_usage_unit_description xmlns:x1="http://example.org/billings">month</x1:sku_usage_unit_description>
  <x1:sku_base_unit xmlns:x1="http://example.org/billings">s</x1:sku_base_unit>
  <x1:sku_base_unit_description xmlns:x1="http://example.org/billings">second</x1:sku_base_unit_description>
  <x1:sku_base_unit_conversion_factor xmlns:x1="http://example.org/billings">2674800</x1:sku_base_unit_conversion_factor>
  <x1:sku_display_quantity xmlns:x1="http://example.org/billings">1</x1:sku_display_quantity>
  <x1:sku_start_usage_amount xmlns:x1="http://example.org/billings">0</x1:sku_start_usage_amount>
  <x1:sku_unit_price xmlns:x1="http://example.org/billings"/>
 </x1:BillingItem>
</x1:billing>
```

# GitHub API PHP Client

Namespace  `PonderSource\GitHubApi`

### Authorization

We need to provide the user's TOKEN(We recommend to use TOKENS with expiration date)

### Headers

Recuired Headers to call the GitHub API endpoints:

```
    "User-Agent: Example REST API Client",
    "Accept: application/vnd.github.v3+json",
    "Authorization: token TOKEN"
```
### Methods

We can ask for billing information either for a user or organization

#### Organization

* getOrgSharedStorageBilling($org)
  - [Get GitHub Actions billing for an organization](https://docs.github.com/en/rest/reference/billing#get-github-actions-billing-for-an-organization)
* getOrgActionsBilling($org)
  - [Get GitHub Packages billing for an organization](https://docs.github.com/en/rest/reference/billing#get-github-packages-billing-for-an-organization)
* getOrgPackagesBillingInfo($org)
  - [Get shared storage billing for an organization](https://docs.github.com/en/rest/reference/billing#get-shared-storage-billing-for-an-organization)

##### User

* getUserSharedStorageBilling($user)
  - [Get GitHub Actions billing for a user](https://docs.github.com/en/rest/reference/billing#get-github-actions-billing-for-a-user)
* getUserActionsBilling($user)
  - [Get GitHub Packages billing for a user](https://docs.github.com/en/rest/reference/billing#get-github-packages-billing-for-a-user)
* getUserPackagesBillingInfo($user)
  - [Get shared storage billing for a user](https://docs.github.com/en/rest/reference/billing#get-shared-storage-billing-for-a-user)

#### Example

##### Get shared storage billing for an organization

At the `billing-api/index.php` first we need to create the GitHub Client

1) First we have to initialize the GitHub Client
```
$github = new GitHubClient();
```

2) Now we can choose between the 6 available functions and retrieve billing info(JSON) either for Organization or User.

```
$github->getOrgSharedStorageBilling("org");
```

3) Response

```
{
 "days_left_in_billing_cycle": 20,
 "estimated_paid_storage_for_month": 15,
 "estimated_storage_for_month": 40
}
```

# AWS API PHP Client

Namespace  `PonderSource\AWSApi`

* PHP library for communication with AWS services: [AWS SDK](https://aws.amazon.com/sdk-for-php/).

* Cost Explorer API endpoint: `https://ce.us-east-1.amazonaws.com`.

### Credentials

From `~/.aws/credentials.ini` we can retireve the credentials

* AWS_ACCESS_KEY_ID

* AWS_SECRET_ACCESS_KEY

#### Using temporary security credentials with the AWS CLI

 `aws sts get-session-token --serial-number arn-of-the-mfa-device --token-code code-from-token`

### [Root Access Keys VS IAM Access Keys](https://docs.aws.amazon.com/general/latest/gr/root-vs-iam.html)

 * Root access
   - Allow full access to all resources in the account

 * IAM Access Keys
   -  Access to AWS services and resources for users in your AWS account

### Example

1) At the `billing-api/index.php` first we need to create AWS Client

```
$aws = new AWSClient([
    'region'  => 'us-east-1',
    'version' => 'latest',
    'credentials' => [
      'key' => $key,
      'secret' => $secret
    ],
    'endpoint' => 'https://ce.us-east-1.amazonaws.com'
]);

```
2) Now we can get Cost and Usage report. Please consider, that the User have to enable the Cost Explorer first(It may take some time to ingest the data)

```

$aws->getCostAndUsage([
'Granularity' => 'DAILY', // REQUIRED
'Metrics' => ['BlendedCost'], // REQUIRED
'TimePeriod' => [ // REQUIRED
		'Start' => '2022-01-03', // REQUIRED
    'End' => '2022-02-03', // REQUIRED
	],
],'aws_cost_and_usage');
```
