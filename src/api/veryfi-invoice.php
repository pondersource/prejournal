<?php

declare(strict_types=1);
require_once('../platform.php');

$CLIENT_ID = $_SERVER["VERIFY_CLIENT_ID"];
$ENVIRONMENT_URL = $_SERVER["VERIFY_ENVIROMENT_URL"];
$username = $_SERVER["VERIFY_USERNAME"];
$api_key = $_SERVER["VERIFY_API_KEY"];

# You can send the list of categories that is relevant to your case
# Veryfi will try to choose the best one that fits this file
$categories = array("Office Expense", "Meals & Entertainment", "Utilities", "Auto");


//$file_path = "/invoice.pdf";
//$file_name = "invoice.pdf";
$file_mime = "application/pdf";

$mime_boundary=rand(0, time());

$eol = "\r\n";

$url = "{$ENVIRONMENT_URL}api/v7/partner/documents/";

$fields = array(
    'file_name' => $file_name,
    'categories' => json_encode($categories)
);

$data = '';

# Build field data
foreach ($fields as $name => $content) {
    $data .= '--' . $mime_boundary . $eol;
    $data .= 'Content-Disposition: form-data; name="' . $name . '"' . $eol . $eol;
    $data .= $content . $eol;
}

# Build file data
$data .= '--' . $mime_boundary . $eol;
$data .= 'Content-Disposition: form-data; name="file"; filename="' . $file_path . '"' . $eol;
$data .= 'Content-Type: ' . $file_mime . $eol;
$data .= 'Content-Transfer-Encoding: base64' . $eol . $eol;
$data .= chunk_split(base64_encode(file_get_contents($file_path))) . $eol;
$data .= "--" . $mime_boundary . "--" . $eol;

$headers = array(
    "Accept: application/json",
    "Content-Type: multipart/form-data; boundary={$mime_boundary}",
    "Content-Length: " . strlen($data),
    "AUTHORIZATION: apikey $username:$api_key",
    "CLIENT-ID: $CLIENT_ID"
);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$json_response = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$json_result = json_encode(json_decode($json_response), JSON_PRETTY_PRINT);
echo '<pre>' . $json_result . '</pre>';

file_put_contents("api_responses/../../verifyInvoice-JSON.json", $json_result);
