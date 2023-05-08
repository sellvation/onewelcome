<?php

declare(strict_types=1);

require_once('vendor/autoload.php');

use Assert\AssertionFailedException;
use Sellvation\OneWelcome\Exceptions\APIException;
use Sellvation\OneWelcome\Notification\Event;
use Sellvation\OneWelcome\RITM\RITMClient;
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
    $ritmAPI = new RITMClient($apiClient, $credentials);
    $user = $ritmAPI->getUserByUUID('41e41a71-af1d-41f1-1c11-1f91e131bf13');

    $user->setState(Event::STATE_INACTIVE);
    $user = $ritmAPI->saveUser($user);

    $ritmAPI->saveStateForUserUUID('41e41a71-af1d-41f1-1c11-1f91e131bf13', Event::STATE_ACTIVE);

    print_r($user);
} catch (AssertionFailedException | APIException $exception) {
    var_dump($exception->getMessage());
}
