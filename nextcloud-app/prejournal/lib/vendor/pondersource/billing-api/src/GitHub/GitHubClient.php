<?php

// https://docs.github.com/en/rest/reference/billing
namespace PonderSource\GitHubApi;

use PonderSource\GitHubApi\Billing;
use PonderSource\GitHubApi\GenerateBilling;

class GitHubClient{

	const BASE_URL = "https://api.github.com";
	private $token;

	public function __construct($token) {
			$this->token = $token;
	}

	public function callGitHubEndpoint($url){
		$headers = [
		    "User-Agent: Example REST API Client",
				"Accept: application/vnd.github.v3+json",
		    "Authorization: token ".$this->token
		];

		$ch = curl_init();

		curl_setopt_array($ch, [
		    CURLOPT_HTTPHEADER => $headers,
		    CURLOPT_RETURNTRANSFER => true
		]);

		curl_setopt($ch, CURLOPT_URL, $url);

		$response = curl_exec($ch);

		curl_close($ch);

		$data = json_decode($response, true);
		return $data;
	}

	// https://api.github.com/orgs/ORG/settings/billing/shared-storage
	// Access tokens must have the repo or admin:org scope.
	public function getOrgSharedStorageBilling($organization){
		$url = self::BASE_URL."/orgs/".$organization."/settings/billing/";
		$shared_storage = $this->callGitHubEndpoint($url."shared-storage");
		$this->getGitHubBilling($shared_storage,'github-billing-storage-organization.xml');
		return $shared_storage;
	}

	// https://api.github.com/orgs/ORG/settings/billing/actions
	// Access tokens must have the repo or admin:org scope.
	public function getOrgActionsBilling($organization){
		$url = self::BASE_URL."/orgs/".$organization."/settings/billing/";
		$actions = $this->callGitHubEndpoint($url."actions");
		//$this->getGitHubBilling($actions,'github-billing-actions-organization.xml');
		return $actions;
	}

	// https://api.github.com/orgs/ORG/settings/billing/packages
	// Access tokens must have the repo or admin:org scope.
	public function getOrgPackagesBillingInfo($organization){
		$url = self::BASE_URL."/orgs/".$organization."/settings/billing/";
		$packages = $this->callGitHubEndpoint($url."packages");
		//$this->getGitHubBilling($packages,'github-billing-packages-organization.xml');
		return $packages;
	}

	// https://api.github.com/users/USERNAME/settings/billing/shared-storage
	public function getUserSharedStorageBilling($username){
		$url = self::BASE_URL."/users/".$username."/settings/billing/";
		$shared_storage = $this->callGitHubEndpoint($url."shared-storage");
		$this->getGitHubBilling($shared_storage,'github-billing-storage-user.xml');
		return $shared_storage;
	}

	// https://api.github.com/users/USERNAME/settings/billing/actions
	public function getUserActionsBilling($username){
		$url = self::BASE_URL."/users/".$username."/settings/billing/";
		$actions = $this->callGitHubEndpoint($url."actions");
		//$this->getGitHubBilling($actions,'github-billing-actions-user.xml');
	}

	// https://api.github.com/users/USERNAME/settings/billing/packages
	public function getUserPackagesBillingInfo($username){
		$url = self::BASE_URL."/users/".$username."/settings/billing/";
		$packages = $this->callGitHubEndpoint($url."packages");
		//$this->getGitHubBilling($packages,'github-billing-packages-user.xml');
		return $packages;
	}


	public function getGitHubBilling($response,$outputXMLFilename){
		$billing = new Billing();
		$billing->days_left_in_billing_cycle = $response["days_left_in_billing_cycle"];
		$billing->estimated_paid_storage_for_month = $response["estimated_paid_storage_for_month"];
		$billing->estimated_storage_for_month = $response["estimated_storage_for_month"];

		$generateBilling = new GenerateBilling();
		$outputXMLString = $generateBilling->billing($billing);

		$dom = new \DOMDocument;
		$dom->loadXML($outputXMLString);
		$dom->save('./api_responses_xml/'.$outputXMLFilename);

		return $billing;
	}
	public function deserializeGitHubBilling($outputXMLString) {
			$deserializeBilling = new DeserializeBilling();
			$deserialize = $deserializeBilling->deserializeBilling($outputXMLString);
	}
}
?>
