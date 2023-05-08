<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\Consent\Attribute;

use Assert\AssertionFailedException;
use Exception;
use Sellvation\OneWelcome\AbstractClient;
use Sellvation\OneWelcome\Consent\Attribute\Collections\ConsentCollection;
use Sellvation\OneWelcome\Exceptions\APIException;

class ConsentClient extends AbstractClient
{
    /**
     * @phpstan-ignore-next-line
     * @throws APIException|AssertionFailedException
     */
    public function getConsentByUserId(string $userId): ConsentCollection
    {
        $response = $this->request('get', sprintf(getenv('CONSENT_URL') . 'attribute-consents/users/%s', $userId));
        return ConsentCollection::fromArray($response);
    }

    /**
     * Deletes consent for a provided consent id
     * @phpstan-ignore-next-line
     * @throws APIException
     */
    public function deleteConsentById(string $id): void
    {
        $this->request(
            'delete',
            sprintf(getenv('CONSENT_URL') . 'attribute-consents/%s', $id),
            ['Content-Type' => 'application/json'],
        );
    }

    /**
     * @phpstan-ignore-next-line
     * @throws APIException|AssertionFailedException
     */
    public function createConsent(string $userId, string $name, string $locale = 'nl_NL'): AttributeConsent
    {
        try {
            $consent = json_encode($consent = [
                'userId' => $userId,
                'processingPurposeId' => $name,
                'attribute' => [
                    'name' => $name
                ],
                'consent' => [
                    'locale' => $locale
                ]
            ], JSON_THROW_ON_ERROR);
        } catch (Exception $exception) {
        }

        $response = $this->request(
            'post',
            getenv('CONSENT_URL') . 'attribute-consents/',
            ['Content-Type' => 'application/json'],
            $consent
        );

        return AttributeConsent::fromArray($response);
    }
}
