<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\RITM\ValueObjects;

use Assert\Assertion;
use Assert\AssertionFailedException;
use JsonException;
use Sellvation\OneWelcome\Notification\Event;
use Sellvation\OneWelcome\RITM\Collections\AddressCollection;
use Sellvation\OneWelcome\RITM\Collections\B2BCollection;
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

    private $isPasswordChangeRequired;

    private $lastSuccessfulLogin;

    /**
     * @var string
     */
    private $birthDate;

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
     * @var string
     */
    private $lastActivityDate = '0000-00-00 00:00:00';
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
     * @var bool
     */
    private $hasAgreedPLUSPrivacyPolicy = false;

    /**
     * @var string
     */
    private $privacyPolicyConsentDate = '0000-00-00 00:00:00';

    /**
     * @var b2bCollection
     */
    private $b2bCollection;

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
        $this->b2bCollection = new B2BCollection();
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
        $instance->lastActivityDate = ($info['LastActivityDate'] ? date('Y-m-d H:i:s', strtotime($info['LastActivityDate'])) : '0000-00-00 00:00:00');
        $instance->isEmployee = (bool) ($profile['IsEmployee'] ?? false);
        $instance->hasEmployeeDiscount = (bool) ($profile['HasEmployeeDiscount'] ?? false);
        $instance->hasLoyaltyCard = (bool) ($info['HasLoyaltyCard'] ?? false);
        $instance->hasAgreedPLUSPrivacyPolicy = (bool) ($info['HasAgreedPLUSPrivacyPolicy'] ?? false);
        $instance->privacyPolicyConsentDate = ($info['PrivacyPolicyConsentDate'] ? date('Y-m-d H:i:s', strtotime($info['PrivacyPolicyConsentDate'])) : '0000-00-00 00:00:00');
        $instance->state = $profile['urn:scim:schemas:extension:iwelcome:1.0']['state'];
        $instance->lastSuccessfulLogin = $profile['urn:scim:schemas:extension:iwelcome:1.0']['lastSuccessfulLogin'];
        $instance->isPasswordChangeRequired = (bool) ($profile['urn:scim:schemas:extension:iwelcome:1.0']['IsPasswordChangeRequired'] ?? false);
        $instance->birthDate = $profile['urn:scim:schemas:extension:iwelcome:1.0']['birthDate'] ?? null;
        $instance->emailCollection = EmailCollection::fromArray($profile['emails']);

        $instance->firstName = $info['FirstName'] ?? null;
        $instance->lastName = $info['SurName'] ?? null;
        $instance->middleName = $info['SurNamePrefix'] ?? null;

        if (true === isset($info['PreferencesOrder'])) {
            Assertion::isArray($info, 'PreferencesOrder');
            $instance->preferencesOrder = PreferencesOrder::fromArray($info['PreferencesOrder']);
        }

        if (true === isset($info['B2BAccounts'])) {
            Assertion::isArray($info, 'B2BAccounts');
            $instance->b2bCollection = B2BCollection::fromArray($info['B2BAccounts']);
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

    public function getIsPasswordChangeRequired(): ?bool
    {
        return $this->isPasswordChangeRequired;
    }

    public function setIsPasswordChangeRequired($isPasswordChangeRequired): self
    {
        $this->isPasswordChangeRequired = $isPasswordChangeRequired;
        return $this;
    }

    public function getBirthDate(): ?string
    {
        return $this->birthDate;
    }

    public function setBirthDate(string $birthDate): self
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    public function getLastActivityDate(): ?string
    {
        return $this->lastActivityDate;
    }

    public function setLastActivityDate(string $lastActivityDate): self
    {
        $this->lastActivityDate = $lastActivityDate;
        return $this;
    }

    public function getHasAgreedPLUSPrivacyPolicy(): bool
    {
        return $this->hasAgreedPLUSPrivacyPolicy;
    }

    public function setHasAgreedPLUSPrivacyPolicy(bool $hasAgreedPLUSPrivacyPolicy): self
    {
        $this->hasAgreedPLUSPrivacyPolicy = $hasAgreedPLUSPrivacyPolicy;
        return $this;
    }

    public function getPrivacyPolicyConsentDate(): ?string
    {
        return $this->privacyPolicyConsentDate;
    }

    public function setPrivacyPolicyConsentDate(string $privacyPolicyConsentDate): self
    {
        $this->privacyPolicyConsentDate = $privacyPolicyConsentDate;
        return $this;
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

    public function getB2bs(): B2BCollection
    {
        return $this->b2bCollection;
    }

    public function setB2bs(B2BCollection $b2bs): self
    {
        $this->b2bCollection = $b2bs;
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

    public function getLastSuccessfulLogin(): ?string
    {
        return $this->lastSuccessfulLogin;
    }

    public function setLastSuccessfulLogin($lastSuccesfulLogin): self
    {
        $this->lastSuccessfulLogin = $lastSuccesfulLogin;
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
            'hasAgreedPrivacyPolicy' => $this->hasAgreedPLUSPrivacyPolicy,
            'hasAgreedPLUSPrivacyPolicy' => $this->hasAgreedPLUSPrivacyPolicy,
            'privacyPolicyConsentDate' => $this->privacyPolicyConsentDate,
            'isPasswordChangeRequired' => $this->isPasswordChangeRequired,
            'lastActivityDate' => $this->lastActivityDate,
            'lastSuccessfulLogin' => $this->lastSuccessfulLogin,
            'birthDate' => $this->birthDate,
            'emails' => $this->emailCollection->toArray(),
            'phoneNumberCollection' => $this->phoneNumberCollection->toArray(),
            'postAddressCollection' => $this->postAddressCollection->toArray(),
            'invoiceAddressCollection' => $this->invoiceAddressCollection->toArray(),
            'deliveryAddressCollection' => $this->deliveryAddressCollection->toArray(),
            'preferencesOrder' => $this->preferencesOrder->toArray(),
            'isB2B' => $this->isB2B,
            'isB2C' => $this->isB2C,
            'hasLoyaltyCard' => $this->hasLoyaltyCard,
            'b2b' => $this->b2bCollection->toArray(),
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
                    'state' => $this->getState(),
                    'birthDate' => $this->getBirthDate(),
                    'lastSuccessfulLogin' => $this->getLastSuccessfulLogin(),
                    'IsPasswordChangeRequired' => $this->getIsPasswordChangeRequired(),
                ],
                getenv('RITM_CUSTOMER_TAG') => [
                    getenv('RITM_CUSTOMER_KEY') => $this->getCustomerId(),
                    'IsB2B' => $this->isB2B(),
                    'IsB2C' => $this->isB2C(),
                    'HasLoyaltyCard' => $this->hasLoyaltyCard(),
                    'HasAgreedPLUSPrivacyPolicy' => $this->getHasAgreedPLUSPrivacyPolicy(),
                    'PrivacyPolicyConsentDate' => $this->getPrivacyPolicyConsentDate(),
                    'LastActivityDate' => $this->getLastActivityDate(),
                ]
            ]
        ];

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
    
    public function getPrimaryEmailAddress(): ?Email
    {
        foreach ($this->emailCollection as $email) {
            if (false === $email->getPrimary()) {
                continue;
            }

            return $email;
        }

        return null;
    }


}
