<?php

declare(strict_types=1);

require_once('vendor/autoload.php');

use Assert\AssertionFailedException;
use Sellvation\OneWelcome\Consent\Attribute\ConsentClient;
use Sellvation\OneWelcome\Exceptions\APIException;
use Sellvation\OneWelcome\APIClient;
use Sellvation\OneWelcome\Config\APIConfig;

//Configuration
$authenticationURL = getenv('AUTHENTICATION_URL');
$clientId = getenv('CLIENT_ID');
$clientSecret = getenv('CLIENT_SECRET');
$scope = getenv('SCOPE');
$username = getenv('USERNAME');
$password = getenv('PASSWORD');

//Create config object for authentication
$config = new APIConfig($authenticationURL, $clientId, $clientSecret, $scope, $username, $password);

//Create client
$httpClient = new GuzzleHttp\Client();
$apiClient = new APIClient($httpClient);

try {
    $credentials = $apiClient->obtainCredentials($config);
    $consentAPI = new ConsentClient($apiClient, $credentials);

    //Retrieve all consent
    $consent = $consentAPI->getConsentByUserId('117761491-d7c3-414c-b7a7-c95d10ee4136');

    //Delete consent
    $consentAPI->deleteConsentById('6305f902c2e7f641dd12cb5a');

    //Create consent
    $consent = $consentAPI->createConsent('117761491-d7c3-414c-b7a7-c95d10ee4136', 'wine');
    print_r($consent);
} catch (AssertionFailedException | APIException $exception) {
    print_r($exception);
}
