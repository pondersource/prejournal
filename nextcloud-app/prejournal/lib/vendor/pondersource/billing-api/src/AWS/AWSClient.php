<?php

namespace PonderSource\AWSApi;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\Exception\AwsException;
use Aws\CostExplorer\CostExplorerClient;
use Aws\CostExplorer\Exception;

class AWSClient{

	protected $s3;

	public function __construct(array $config){

		foreach($config as $property => $value) {
				$this->$property = $value;
		}

		// Create a CostExplorerClient
		$this->s3 = new CostExplorerClient($config);
	}

	public function getCostAndUsage($params,$filename){

		$cost_and_usage = $this->s3->getCostAndUsage($params);
		echo '<pre>';
		var_dump($cost_and_usage);
		echo '</pre>';
		file_put_contents($filename, json_encode($cost_and_usage, JSON_PRETTY_PRINT));
		return $cost_and_usage;
	}
}

?>
