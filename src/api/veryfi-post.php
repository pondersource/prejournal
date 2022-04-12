<?php
    $CLIENT_ID = "vrf9VRVOe2I2Kc38RkEj3Bzmgi6kKy12EPHu7yC";
    $ENVIRONMENT_URL = "https://api.veryfi.com/";

    $username = "tt3009117";
    $api_key = "0469cd5a13ede3bfed1590b3b39207f1";

    # You can send the list of categories that is relevant to your case
    # Veryfi will try to choose the best one that fits this file
    $categories = array("Office Expense", "Meals & Entertainment", "Utilities", "Auto");


    $file_path = __DIR__ ."/invoice.pdf";
    $file_name = "invoice.pdf";
    $file_mime = "application/pdf";

    $mime_boundary=md5(time());
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
        "Content-Type: multipart/form-data; boundary=${mime_boundary}",
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

    print("json_response = " . $json_response);
?>