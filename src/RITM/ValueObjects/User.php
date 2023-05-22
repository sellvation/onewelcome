<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\RITM\ValueObjects;

use Assert\Assertion;
use Assert\AssertionFailedException;
use JsonException;
use Sellvation\OneWelcome\Notification\Event;
use Sellvation\OneWelcome\RITM\Collections\AddressCollection;
use Sellvation\OneWelcome\RITM\Collections\EmailCollection;
use Sellvation\OneWelcome\RITM\Collections\LoyaltyCollection;
use Sellvation\OneWelcome\RITM\Collections\PhoneNumberCollection;

class User
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $customerId;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string|null
     */
    private $middleName;

    /**
     * @var string
     */
    private $state;

    /**
     * @var EmailCollection
     */
    private $emailCollection;

    /**
     * @var PhoneNumberCollection
     */
    private $phoneNumberCollection;

    /**
     * @var AddressCollection
     */
    private $postAddressCollection;

    /**
     * @var AddressCollection
     */
    private $invoiceAddressCollection;

    /**
     * @var AddressCollection
     */
    private $deliveryAddressCollection;
    /**
     * @var bool
     */
    private $isB2B = false;
    /**
     * @var bool
     */
    private $isB2C = false;
    /**
     * @var bool
     */
    private $hasLoyaltyCard = false;
    /**
     * @var bool
     */
    private $isEmployee = false;
    /**
     * @var bool
     */
    private $hasEmployeeDiscount = false;
    /**
     * @var B2B
     */
    private $b2b;

    /**
     * @var Payment
     */
    private $payment;

    /**
     * @var LoyaltyCollection
     */
    private $loyaltyCollection;

    /**
     * @var null|PreferencesOrder
     */
    private $preferencesOrder;

    private function __construct()
    {
        $this->loyaltyCollection = new LoyaltyCollection();
        $this->emailCollection = new EmailCollection();
        $this->phoneNumberCollection = new PhoneNumberCollection();
        $this->postAddressCollection = new AddressCollection();
        $this->invoiceAddressCollection = new AddressCollection();
        $this->deliveryAddressCollection = new AddressCollection();
    }

    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException
     */
    public static function fromArray(array $data): self
    {
        Assertion::keyExists($data, 'profileInformation');
        $profile = $data['profileInformation'];

        Assertion::keyExists($profile, 'uid');
        Assertion::keyExists($profile, 'emails');
        Assertion::isArray($profile, 'emails');
        Assertion::keyExists($profile, 'urn:scim:schemas:extension:iwelcome:1.0');
        Assertion::isArray($profile, 'urn:scim:schemas:extension:iwelcome:1.0');
        Assertion::keyExists($profile['urn:scim:schemas:extension:iwelcome:1.0'], 'state');
        Assertion::keyExists($profile, getenv('RITM_CUSTOMER_TAG'));
        Assertion::keyExists($profile[getenv('RITM_CUSTOMER_TAG')], getenv('RITM_CUSTOMER_KEY'));

        $info = $profile[getenv('RITM_CUSTOMER_TAG')];

        $instance = new self();
        $instance->uuid = $profile['uid'];

        $instance->customerId = (string) $info[getenv('RITM_CUSTOMER_KEY')];
        $instance->isB2B = (bool) ($info['IsB2B'] ?? false);
        $instance->isB2C = (bool) ($info['IsB2C'] ?? false);
        $instance->isEmployee = (bool) ($profile['IsEmployee'] ?? false);
        $instance->hasEmployeeDiscount = (bool) ($profile['HasEmployeeDiscount'] ?? false);
        $instance->hasLoyaltyCard = (bool) ($info['HasLoyaltyCard'] ?? false);
        $instance->state = $profile['urn:scim:schemas:extension:iwelcome:1.0']['state'];
        $instance->emailCollection = EmailCollection::fromArray($profile['emails']);

        $instance->firstName = $info['FirstName'] ?? null;
        $instance->lastName = $info['SurName'] ?? null;
        $instance->middleName = $info['SurNamePrefix'] ?? null;

        if (true === isset($info['PreferencesOrder'])) {
            Assertion::isArray($info, 'PreferencesOrder');
            $instance->preferencesOrder = PreferencesOrder::fromArray($info['PreferencesOrder']);
        }

        if (true === isset($profile['B2B'])) {
            Assertion::isArray($profile, 'B2B');
            $instance->b2b = B2B::fromArray($profile['B2B']);
        }

        if (true === isset($info['Payment'])) {
            Assertion::isArray($info, 'Payment');
            $instance->payment = Payment::fromArray($info['Payment']);
        }

        if (true === isset($profile['Loyalty'])) {
            Assertion::isArray($profile, 'Loyalty');
            $instance->loyaltyCollection = LoyaltyCollection::fromArray($profile['Loyalty']);
        }

        if (true === isset($profile['phoneNumbers'])) {
            Assertion::isArray($profile, 'phoneNumbers');
            $instance->phoneNumberCollection = PhoneNumberCollection::fromArray($profile['phoneNumbers']);
        }

        if (true === isset($profile['PostAddresses'])) {
            Assertion::isArray($profile, 'PostAddresses');
            $instance->postAddressCollection = AddressCollection::fromArray($profile['PostAddresses']);
        }

        if (true === isset($profile['InvoiceAddresses'])) {
            Assertion::isArray($profile, 'InvoiceAddresses');
            $instance->invoiceAddressCollection = AddressCollection::fromArray($profile['InvoiceAddresses']);
        }

        if (true === isset($info['DeliveryAddresses'])) {
            Assertion::isArray($info, 'DeliveryAddresses');
            $instance->deliveryAddressCollection = AddressCollection::fromArray($info['DeliveryAddresses']);
        }

        return $instance;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getEmails(): EmailCollection
    {
        return $this->emailCollection;
    }

    public function setEmails(EmailCollection $emails): self
    {
        $this->emailCollection = $emails;
        return $this;
    }

    public function getPhoneNumbers(): PhoneNumberCollection
    {
        return $this->phoneNumberCollection;
    }

    public function setPhoneNumbers(PhoneNumberCollection $phoneNumbers): self
    {
        $this->phoneNumberCollection = $phoneNumbers;
        return $this;
    }

    public function getPostAddresses(): AddressCollection
    {
        return $this->postAddressCollection;
    }

    public function setPostAddresses(AddressCollection $addresses): self
    {
        $this->postAddressCollection = $addresses;
        return $this;
    }

    public function getInvoiceAddresses(): AddressCollection
    {
        return $this->invoiceAddressCollection;
    }

    public function setInvoiceAddresses(AddressCollection $addresses): self
    {
        $this->invoiceAddressCollection = $addresses;
        return $this;
    }

    public function getDeliveryAddresses(): AddressCollection
    {
        return $this->deliveryAddressCollection;
    }

    public function setDeliveryAddresses(AddressCollection $addresses): self
    {
        $this->deliveryAddressCollection = $addresses;
        return $this;
    }

    public function getUUID(): ?string
    {
        return $this->uuid;
    }

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function setCustomerId(string $customerId): self
    {
        $this->customerId = $customerId;
        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): ?string
    {
        Assertion::inArray($state, Event::STATES);
        return $this->state;
    }

    public function isB2B(): bool
    {
        return $this->isB2B;
    }

    public function isEmployee(): bool
    {
        return $this->isEmployee;
    }

    public function hasEmployeeDiscount(): bool
    {
        return $this->hasEmployeeDiscount;
    }

    public function setIsB2B(bool $isB2B): self
    {
        $this->isB2B = $isB2B;
        return $this;
    }

    public function isB2C(): bool
    {
        return $this->isB2C;
    }

    public function setIsB2C(bool $isB2C): self
    {
        $this->isB2C = $isB2C;
        return $this;
    }

    public function hasLoyaltyCard(): bool
    {
        return $this->hasLoyaltyCard;
    }

    public function getPreferencesOrder(): ?PreferencesOrder
    {
        return $this->preferencesOrder;
    }

    public function setHasLoyaltyCard(bool $hasLoyaltyCard): self
    {
        $this->hasLoyaltyCard = $hasLoyaltyCard;
        return $this;
    }

    public function getB2b(): ?B2B
    {
        return $this->b2b;
    }

    public function setB2b(B2B $b2b): self
    {
        $this->b2b = $b2b;
        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(Payment $payment): self
    {
        $this->payment = $payment;
        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(string $middleName): self
    {
        $this->middleName = $middleName;
        return $this;
    }

    public function getLoyalties(): LoyaltyCollection
    {
        return $this->loyaltyCollection;
    }

    public function setLoyalties(LoyaltyCollection $loyalties): self
    {
        $this->loyaltyCollection = $loyalties;
        return $this;
    }

    /**
     * Returns a unique fingerprint for the current data in the User object.
     * When user data changes, the fingerprint will change as well.
     */
    public function getFingerprint(): string
    {
        return hash('ripemd160', $this->toSerialize());
    }

    /**
     * @throws JsonException
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    public function toSerialize(): string
    {
        return serialize($this->toArray());
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'lastName' => $this->lastName,
            'customerId' => $this->customerId,
            'firstName' => $this->firstName,
            'middleName' => $this->middleName,
            'state' => $this->state,
            'emails' => $this->emailCollection->toArray(),
            'phoneNumberCollection' => $this->phoneNumberCollection->toArray(),
            'postAddressCollection' => $this->postAddressCollection->toArray(),
            'invoiceAddressCollection' => $this->invoiceAddressCollection->toArray(),
            'deliveryAddressCollection' => $this->deliveryAddressCollection->toArray(),
            'preferencesOrder' => $this->deliveryAddressCollection->toArray(),
            'isB2B' => $this->isB2B,
            'isB2C' => $this->isB2C,
            'hasLoyaltyCard' => $this->hasLoyaltyCard,
            'b2b' => $this->b2b,
            'payment' => $this->payment,
        ];
    }

    public function toOneWelcomeFormat(): array
    {
        $invoiceAddresses = $this->getInvoiceAddresses();
        $deliveryAddresses = $this->getDeliveryAddresses();
        $postAddresses = $this->getPostAddresses();
        $loyalties = $this->getLoyalties();
        $payment = $this->getPayment();
        $phoneNumbers = $this->getPhoneNumbers();
        $emails = $this->getEmails();

        $output = [
            'profileInformation' => [
                'name' => [
                    'givenName' => $this->getFirstName(),
                    'familyName' => $this->getLastName(),
                    'middleName' => $this->getMiddleName(),
                ],
                'urn:scim:schemas:extension:iwelcome:1.0' => [
                    'state' => $this->getState()
                ],
                getenv('RITM_CUSTOMER_TAG') => [
                    getenv('RITM_CUSTOMER_KEY') => $this->getCustomerId(),
                    'IsB2B' => $this->isB2B(),
                    'IsB2C' => $this->isB2C(),
                    'HasLoyaltyCard' => $this->hasLoyaltyCard(),
                ]
            ]
        ];

        if ($b2b = $this->getB2b()) {
            $output['B2B'] = $b2b->toOneWelcomeFormat();
        }

        if (null !== $payment) {
            $output['Payment'] = $payment->toOneWelcomeFormat();
        }

        if ($invoiceAddresses->count() > 0) {
            $output['InvoiceAddresses'] = $invoiceAddresses->toOneWelcomeFormat();
        }

        if ($deliveryAddresses->count() > 0) {
            $output['DeliveryAddresses'] = $deliveryAddresses->toOneWelcomeFormat();
        }

        if ($postAddresses->count() > 0) {
            $output['PostAddresses'] = $postAddresses->toOneWelcomeFormat();
        }

        if ($loyalties->count() > 0) {
            $output['Loyalty'] = $loyalties->toOneWelcomeFormat();
        }

        if ($phoneNumbers->count() > 0) {
            $output['phoneNumbers'] = $phoneNumbers->toOneWelcomeFormat();
        }

        if ($emails->count() > 0) {
            $output['emails'] = $emails->toOneWelcomeFormat();
        }

        return $output;
    }
}
