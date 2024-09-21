<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

function truncateString($string, $length = 100, $append = "...") {
    return (strlen($string) > $length) ? substr($string, 0, $length - strlen($append)) . $append : $string;
}

function getTemplatesFromAirtable() {
    $client = new Client([
        'base_uri' => 'https://api.airtable.com/v0/',
        'headers' => [
            'Authorization' => 'Bearer patw1hkmlWkUiQGo1.3c0fb4f97d4ac921a05401dbb1cfd4df46929d921225c93c5433f32fa66f635a',
            'Content-Type' => 'application/json',
        ]
    ]);

    $baseId = 'appKizJ2RlGZWBg8M';
    $tableName = 'Templates';

    try {
        $response = $client->request('GET', "$baseId/$tableName");
        $data = json_decode($response->getBody(), true);
        
        if (!isset($data['records'])) {
            throw new \Exception("Unexpected response structure: 'records' key not found");
        }

        $templates = [];
        foreach ($data['records'] as $record) {
            $templates[] = [
                'name' => truncateString($record['fields']['Name'] ?? '', 50),
                'image' => $record['fields']['Assets'][0]['thumbnails']['full']['url'] ?? '',
                'type' => $record['fields']['type'] ?? '',
                'purpose' => $record['fields']['purpose'] ?? '',
                'industry' => $record['fields']['industry'] ?? '',
                'designLink' => $record['fields']['asset_url'] ?? '',
                'description' => $record['fields']['description'] ?? '',
                'long_text' => $record['fields']['long_text'] ?? '',
            ];
        }
        
        return $templates;
    } catch (RequestException $e) {
        error_log("Airtable API Error: " . $e->getMessage());
        return ['error' => 'Failed to fetch data from Airtable. Please check your API key and table name.'];
    } catch (\Exception $e) {
        error_log("Unexpected error: " . $e->getMessage());
        return ['error' => 'An unexpected error occurred.'];
    }
}

// Only run this if the file is accessed directly, not when included
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    $result = getTemplatesFromAirtable();
    echo "<pre>";
    var_dump($result);
    echo "</pre>";
}