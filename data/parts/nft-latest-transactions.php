<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$endpoint = "https://constellations-api.mainnet.stargaze-apis.com/graphql";
// $authToken = "[[your auth token]]";//this is provided by graphcms
$qry = '{"query":"query Sales {\n  events(\n    filter: SALES\n    dataFilters: [\n      {\n        name: \"collection\"\n        value: \"stars19jq6mj84cnt9p7sagjxqf8hxtczwc8wlpuwe4sh62w45aheseues57n420\"\n        operator: EQUAL\n      }\n    ]\n    sortBy: BLOCK_HEIGHT_DESC\n    first: 18\n  ) {\n    edges {\n      node {\n        eventName\n        action\n        createdAt\n        data\n      }\n    }\n  }\n}\n"}';

$headers = array();
$headers[] = 'Content-Type: application/json';
// $headers[] = 'Authorization: Bearer '.$authToken;
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, $qry);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
$result = json_decode($result);
$result->updated = time();
$result = json_encode($result);

file_put_contents("nft-latest-transactions.js", $result);

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
} else echo 'nft-latest-transactions.js updated.';
?>