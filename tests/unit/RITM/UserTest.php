<?php
declare(strict_types=1);

namespace unit\RITM;

use Assert\AssertionFailedException;
use JsonException;
use PHPUnit\Framework\TestCase;
use Sellvation\OneWelcome\RITM\ValueObjects\User;

class UserTest extends TestCase
{
    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException
     */
    public function testShouldFormatResponseToUserObjectWhenOneWelcomeResponseIsProvided(): void
    {
        $info = [
            'uid' => '4c13d-d13-498-aa0-41e115651',
            'emails' => [
                [
                    'value' => 'mail@mailinator.com',
                    'type' => 'home'
                ]
            ],
            'phoneNumbers' => [
                [
                    'value' => '+91102456789',
                    'type' => 'work'
                ]
            ],
            'urn:scim:schemas:extension:iwelcome:1.0:ritm' => [],
            getenv('RITM_CUSTOMER_TAG') => [
                'IsB2C' => true,
                'DeliveryAddresses' => [
                    [
                        'Street' => 'Parker Street',
                        'HouseNumber' => '901',
                        'Phone' => '+91102456789',
                        'HouseNumberAddition' => 'Queen Towers',
                        'PostalCode' => '22031',
                        'City' => 'Ibiza',
                        'Country' => 'Netherland',
                        'CountryISO2' => 'Netherland12',
                        'ToAttentionOf' => 'IT',
                        'IsPreferred' => true
                    ],
                    [
                        'Street' => 'Baker Block',
                        'HouseNumber' => '891',
                        'Phone' => '+91102456789',
                        'HouseNumberAddition' => 'Peter Towers',
                        'PostalCode' => '2211',
                        'City' => 'Amsterdam',
                        'Country' => 'Netherland',
                        'CountryISO2' => 'Netherland12',
                        'ToAttentionOf' => 'HR',
                        'IsPreferred' => false
                    ]
                ],
                'IsB2B' => true,
                'Payment' => [
                    'PreferredPaymentMethod' => 'DebitCard',
                    'DebitID' => '12345678998765431',
                    'PrintItemsOnCustomerInvoice' => 'true',
                    'IsSaleOnAccount' => 'true',
                    'IsDirectDebit' => 'true'
                ],
                "PreferencesOrder" => [
                    'CommunicationLevel' => 'details',
                    'MyStore' => '110',
                    'CustomerRemark' => 'THT code3 dagen',
                    'AlternateProduct' => 'YES',
                    'CommunicationChannel' => 'SMS',
                    'GenericRemark' => 'always fresh products'
                ],
                'FirstName' => 'Foo',
                'SurName' => 'Bar',
                getenv('RITM_CUSTOMER_KEY') => 1000,
                'HasLoyaltyCard' => true,
            ],
            'urn:scim:schemas:extension:iwelcome:1.0' => [
                'state' => 'ACTIVE'
            ],
            'InvoiceAddresses' => [
                [
                    'Street' => 'Parker Street',
                    'HouseNumber' => '901',
                    'HouseNumberAddition' => 'Queen Towers',
                    'PostalCode' => '22031',
                    'City' => 'Ibiza',
                    'Country' => 'Netherland',
                    'CountryISO2' => 'Netherland12',
                    'POBoxNumber' => '97902',
                    'POBoxNumberPostalCode' => '63257',
                    'POBoxNumberCity' => 'AJND33'
                ]
            ],
            'B2B' => [
                'CompanyName' => 'onewelcome',
                'Department' => 'IT',
                'KVKNumber' => '123456789',
                'VATNumber' => '98765431',
                'CostCenter' => '123aSE'
            ],
            'Loyalty' => [
                [
                    'AssetID' => 'pen',
                    'KindOfSaving' => 'save'
                ],
                [
                    'AssetID' => 'pencil',
                    'KindOfSaving' => 'don\'t_save'
                ]
            ],
            'PostAddresses' => [
                [
                    'Street' => 'Baker Street',
                    'HouseNumber' => '881',
                    'HouseNumberAddition' => 'Thomas Towers',
                    'PostalCode' => '2201',
                    'City' => 'Amsterdam',
                    'Country' => 'Netherland',
                    'CountryISO2' => 'Netherland12',
                    'POBoxNumber' => '4683',
                    'POBoxNumberPostalCode' => '99302',
                    'POBoxNumberCity' => 'AMS23'
                ],
                [
                    'Street' => 'Baker Block',
                    'HouseNumber' => '891',
                    'HouseNumberAddition' => 'Peter Towers',
                    'PostalCode' => '2211',
                    'City' => 'Amsterdam',
                    'Country' => 'Netherland',
                    'CountryISO2' => 'Netherland12',
                    'POBoxNumber' => '4242',
                    'POBoxNumberPostalCode' => '99502',
                    'POBoxNumberCity' => 'AMS25'
                ]
            ],
            'IsEmployee' => false,
            'HasEmployeeDiscount' => true,
        ];


        $user = User::fromArray(['profileInformation' => $info]);
        $scimArray = $info[getenv('RITM_CUSTOMER_TAG') ];
        $paymentArray = $scimArray['Payment'];
        $paymentObject = $user->getPayment();
        $b2bArray = $info['B2B'];
        $b2bObject = $user->getB2b();
        $postAddressArray = $info['PostAddresses'][0];
        $postAddressObject = $user->getPostAddresses()->current();

        $invoiceAddressArray = $info['InvoiceAddresses'][0];
        $invoiceAddressObject = $user->getInvoiceAddresses()->current();

        $deliveryAddressArray = $scimArray['DeliveryAddresses'][0];
        $deliveryAddressObject = $user->getDeliveryAddresses()->current();
        $loyaltyArray = $info['Loyalty'][0];
        $loyaltyObject = $user->getLoyalties()->current();
        $preferencesOrderArray = $scimArray['PreferencesOrder'];
        $preferencesOrderObject = $user->getPreferencesOrder();

        //Loyalties
        $this->assertEquals($loyaltyArray['AssetID'], $loyaltyObject->getAssetId());
        $this->assertEquals($loyaltyArray['KindOfSaving'], $loyaltyObject->getKindOfSaving());

        //Generic
        $this->assertEquals($info['uid'], $user->getUUID());
        $this->assertEquals($scimArray['FirstName'], $user->getFirstName());
        $this->assertEquals($scimArray['SurName'], $user->getLastName());
        $this->assertEquals($info['IsEmployee'], $user->isEmployee());
        $this->assertEquals($info['HasEmployeeDiscount'], $user->hasEmployeeDiscount());

        //Scim
        $this->assertEquals($scimArray['CustomerID'], $user->getCustomerId());
        $this->assertEquals($scimArray['IsB2B'], $user->isB2B());
        $this->assertEquals($scimArray['IsB2C'], $user->isB2C());
        $this->assertEquals($scimArray['HasLoyaltyCard'], $user->hasLoyaltyCard());

        //B2B
        $this->assertEquals($b2bArray['VATNumber'], $b2bObject->getVatNumber());
        $this->assertEquals($b2bArray['CompanyName'], $b2bObject->getCompanyName());
        $this->assertEquals($b2bArray['Department'], $b2bObject->getDepartment());
        $this->assertEquals($b2bArray['CostCenter'], $b2bObject->getCostCenter());
        $this->assertEquals($b2bArray['KVKNumber'], $b2bObject->getChamberOfCommerceNumber());

        //Payment
        $this->assertEquals($paymentArray['DebitID'], $paymentObject->getDebitId());
        $this->assertEquals('true' === $paymentArray['IsSaleOnAccount'], $paymentObject->isSaleOnAccount());
        $this->assertEquals('true' === $paymentArray['IsDirectDebit'], $paymentObject->isDirectDebit());
        $this->assertEquals($paymentArray['PreferredPaymentMethod'], $paymentObject->getPreferredPaymentMethod());
        $this->assertEquals('true' === $paymentArray['PrintItemsOnCustomerInvoice'], $paymentObject->shouldPrintItemsOnCustomerInvoice());
        $this->assertEquals($info['urn:scim:schemas:extension:iwelcome:1.0']['state'], $user->getState());

        //Post address
        $this->assertEquals($postAddressArray['Street'], $postAddressObject->getStreet());
        $this->assertEquals($postAddressArray['HouseNumber'], $postAddressObject->getHouseNumber());
        $this->assertEquals($postAddressArray['HouseNumberAddition'], $postAddressObject->getHouseNumberAddition());
        $this->assertEquals($postAddressArray['PostalCode'], $postAddressObject->getPostalCode());
        $this->assertEquals($postAddressArray['City'], $postAddressObject->getCity());
        $this->assertEquals($postAddressArray['Country'], $postAddressObject->getCountry());
        $this->assertEquals($postAddressArray['CountryISO2'], $postAddressObject->getCountryISO2());
        $this->assertEquals($postAddressArray['POBoxNumber'], $postAddressObject->getPOBoxNumber());
        $this->assertEquals($postAddressArray['POBoxNumberPostalCode'], $postAddressObject->getPOBoxNumberPostalCode());
        $this->assertEquals($postAddressArray['POBoxNumberCity'], $postAddressObject->getPOBoxNumberCity());

        //Delivery address
        $this->assertEquals($deliveryAddressArray['Street'], $deliveryAddressObject->getStreet());
        $this->assertEquals($deliveryAddressArray['HouseNumber'], $deliveryAddressObject->getHouseNumber());
        $this->assertEquals($deliveryAddressArray['HouseNumberAddition'], $deliveryAddressObject->getHouseNumberAddition());
        $this->assertEquals($deliveryAddressArray['PostalCode'], $deliveryAddressObject->getPostalCode());
        $this->assertEquals($deliveryAddressArray['City'], $deliveryAddressObject->getCity());
        $this->assertEquals($deliveryAddressArray['Country'], $deliveryAddressObject->getCountry());
        $this->assertEquals($deliveryAddressArray['CountryISO2'], $deliveryAddressObject->getCountryISO2());
        $this->assertEquals($deliveryAddressArray['POBoxNumber'], $deliveryAddressObject->getPOBoxNumber());
        $this->assertEquals($deliveryAddressArray['POBoxNumberPostalCode'], $deliveryAddressObject->getPOBoxNumberPostalCode());
        $this->assertEquals($deliveryAddressArray['POBoxNumberCity'], $deliveryAddressObject->getPOBoxNumberCity());

        //Invoice address
        $this->assertEquals($invoiceAddressArray['Street'], $invoiceAddressObject->getStreet());
        $this->assertEquals($invoiceAddressArray['HouseNumber'], $invoiceAddressObject->getHouseNumber());
        $this->assertEquals($invoiceAddressArray['HouseNumberAddition'], $invoiceAddressObject->getHouseNumberAddition());
        $this->assertEquals($invoiceAddressArray['PostalCode'], $invoiceAddressObject->getPostalCode());
        $this->assertEquals($invoiceAddressArray['City'], $invoiceAddressObject->getCity());
        $this->assertEquals($invoiceAddressArray['Country'], $invoiceAddressObject->getCountry());
        $this->assertEquals($invoiceAddressArray['CountryISO2'], $invoiceAddressObject->getCountryISO2());
        $this->assertEquals($invoiceAddressArray['POBoxNumber'], $invoiceAddressObject->getPOBoxNumber());
        $this->assertEquals($invoiceAddressArray['POBoxNumberPostalCode'], $invoiceAddressObject->getPOBoxNumberPostalCode());
        $this->assertEquals($invoiceAddressArray['POBoxNumberCity'], $invoiceAddressObject->getPOBoxNumberCity());

        //PreferencesOrder
        $this->assertEquals($preferencesOrderArray['AlternateProduct'], $preferencesOrderObject->getAlternateProduct());
        $this->assertEquals($preferencesOrderArray['CommunicationChannel'], $preferencesOrderObject->getCommunicationChannel());
        $this->assertEquals($preferencesOrderArray['CommunicationLevel'], $preferencesOrderObject->getCommunicationLevel());
        $this->assertEquals($preferencesOrderArray['CustomerRemark'], $preferencesOrderObject->getCustomerRemark());
        $this->assertEquals($preferencesOrderArray['GenericRemark'], $preferencesOrderObject->getGenericRemark());
        $this->assertEquals($preferencesOrderArray['MyStore'], $preferencesOrderObject->getMyStore());
    }

    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException
     */
    public function testShouldFormatMinimalResponseToUserObjectWhenOneWelcomeResponseIsProvided(): void
    {
        $info = [
            'uid' => '4c13d-d13-498-aa0-41e115651',
            'emails' => [
                [
                    'value' => 'mail@mailinator.com',
                    'type' => 'home'
                ]
            ],
            'urn:scim:schemas:extension:iwelcome:1.0:ritm' => [],
            getenv('RITM_CUSTOMER_TAG') => [
                getenv('RITM_CUSTOMER_KEY') => 1000,
                'IsB2B' => true,
                'IsB2C' => true,
                'HasLoyaltyCard' => true,
            ],
            'urn:scim:schemas:extension:iwelcome:1.0' => [
                'state' => 'ACTIVE'
            ],
        ];

        $user = User::fromArray(['profileInformation' => $info]);
        $this->assertCount(0, $user->getPhoneNumbers());
        $this->assertCount(0, $user->getPostAddresses());
        $this->assertCount(0, $user->getDeliveryAddresses());
        $this->assertCount(0, $user->getInvoiceAddresses());
        $this->assertCount(0, $user->getLoyalties());
        $this->assertNull($user->getPayment());
        $this->assertNull($user->getB2b());
        $this->assertNull($user->getMiddleName());
        $this->assertNull($user->getLastName());
        $this->assertNull($user->getFirstName());
    }

    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException
     */
    public function testShouldReturnCorrectEmailAndPhoneNumberWhenFilteringItems(): void
    {
        $user = User::fromArray([
            'profileInformation' => [
                'uid' => '61e7688b-95fd-4d79-a14a-28e3966832c5',
                'name' => [
                    'familyName' => 'name',
                    'middleName' => 'name',
                    'givenName' => 'name',
                ],
                'emails' => [
                    [
                        'value' => 'email1@domain.com',
                        'type' => 'home'
                    ], [
                        'value' => 'email2@domain.com',
                        'type' => 'other'
                    ], [
                        'value' => 'email3@domain.com',
                        'type' => 'work'
                    ]
                ],
                'phoneNumbers' => [
                    [
                        'value' => '0123456789',
                        'type' => 'home'
                    ], [
                        'value' => '9876543210',
                        'type' => 'other'
                    ]
                ],
                getenv('RITM_CUSTOMER_TAG') => [
                    getenv('RITM_CUSTOMER_KEY') => '123456789'
                ],
                'urn:scim:schemas:extension:iwelcome:1.0' => [
                    'state' => 'ACTIVE'
                ]
            ]
        ]);

        $this->assertEquals('email2@domain.com', $user->getEmails()->filterType('other')->current()->getValue());
        $this->assertEquals('9876543210', $user->getPhoneNumbers()->filterType('other')->current()->getValue());
    }


    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException|JsonException
     */
    public function testShouldCompareFingerprintsWhenTwoConsentsWithDifferentDataAreCreated(): void
    {
        $info = [
            'uid' => '4c13d-d13-498-aa0-41e115651',
            'emails' => [
                [
                    'value' => 'mail@mailinator.com',
                    'type' => 'home'
                ]
            ],
            'urn:scim:schemas:extension:iwelcome:1.0:ritm' => [],
            getenv('RITM_CUSTOMER_TAG') => [
                getenv('RITM_CUSTOMER_KEY') => 1000,
                'IsB2B' => true,
                'IsB2C' => true,
                'HasLoyaltyCard' => true,
            ],
            'urn:scim:schemas:extension:iwelcome:1.0' => [
                'state' => 'ACTIVE'
            ],
        ];

        $user = User::fromArray(['profileInformation' => $info]);
        $info['uid'] = '1';
        $user2 = User::fromArray(['profileInformation' => $info]);

        $this->assertIsString($user->getFingerprint());
        $this->assertNotSame($user->getFingerprint(), $user2->getFingerprint());
    }
}