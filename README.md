### Creating the API client
```php
use Sellvation\OneWelcome\APIClient;

//Create client
$httpClient = new GuzzleHttp\Client();
$apiClient = new APIClient($httpClient);
```

### Creating the config
```php
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
```

### Obtaining the credentials
```php
try {
    $credentials = $apiClient->obtainCredentials($config);
    $credentials->getAccessToken();
    $credentials->getExpiresAt();
    $credentials->...
    
} catch (AssertionFailedException | APIException $exception) {
    var_dump($exception->getMessage());
}
```
*Note: obtained credentials can be stored due to the fact that they are 3600 seconds valid. There is no need to retrieve credentials for every API request.*

### Scim API
Getting user information by user id:
```php
use Sellvation\OneWelcome\Scim\ScimClient;

try {
    $scimAPI = new ScimClient($apiClient, $credentials);
    $user = $scimAPI->getUserById('61b7688b-9efd-4d79-a14a-18e3966132c5');
    $user->getName();
    $user->getId();
    $user->getPrimaryEmailAddress();
    $user->...
    
} catch (AssertionFailedException | APIException $exception) {
    var_dump($exception->getMessage());
}
```

### RITM API
Getting user information by user id:
```php
use Sellvation\OneWelcome\RITM\RITMClient;

try {
    $ritmAPI = new RITMClient($apiClient, $credentials);
    $user = $ritmAPI->getUserById('61b7688b-9efd-4d79-a14a-18e3966132c5');
    $user->getFirstName();
    $user->getUUID();
    $user->getEmailCollection();
    $user->...
    
} catch (AssertionFailedException | APIException $exception) {
    var_dump($exception->getMessage());
}
```

### Consent API
Getting the consent by user id:
```php
use Sellvation\OneWelcome\Consent\ConsentClient;

try {
    $consentAPI = new ConsentClient($apiClient, $credentials);
    $consents = $consentAPI->getConsentByUserId('61b7688b-9efd-4d79-a14a-18e3966132c5');
    
    foreach($consents as $consent) {
        $consent->getConsentId();
        $consent->...
        $consent->getDocumentInfo()->getName();
        $consent->getDocumentInfo()->...
    }
    
    //Create consent
    $consent = $consentAPI->createConsent('61b7688b-9efd-4d79-a14a-18e3966132c5', '1', new Carbon());
    
    //Delete consent for user
    $consentAPI->deleteAllConsent('61b7688b-9efd-4d79-a14a-18e3966132c5');
    
} catch (AssertionFailedException | APIException $exception) {
    var_dump($exception->getMessage());
}
```

### Notification API
Retrieving notifications with events
```php
use Sellvation\OneWelcome\Notification\NotificationClient;

try {
    $notificationAPI = new NotificationClient($apiClient, $credentials);
    $notification = $notificationAPI->getNotificationBySubscriptionId('6229a671acdc77154125cec6');
    
    $notification->getPage();
    $notification->getSize();
    
    foreach($notification->getEventCollection() as $event) {
        $event->getTypeId();   
        $event->getName();   
        $event->getUserId();
        $event->...   
    }
    
} catch (AssertionFailedException | APIException $exception) {
    var_dump($exception->getMessage());
}
```

### Fallback
If for some reason the API throws an exception, you can get the original request object.
```php
echo $apiClient->getOriginalResponse()->getStatusCode();
echo $apiClient->getOriginalResponse()->getBody();
```


## License
The files in this archive are released under the GNU GENERAL PUBLIC LICENSE. You can find a copy of this license in [LICENSE.md](LICENSE.md).

Please visit us sometime soon at https://sellvation.nl