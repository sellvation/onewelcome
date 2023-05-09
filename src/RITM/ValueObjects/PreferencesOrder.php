<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\RITM\ValueObjects;

class PreferencesOrder
{
    /**
     * @var string
     */
    private $communicationLevel;

    /**
     * @var string
     */
    private $myStore;

    /**
     * @var string
     */
    private $customerRemark;

    /**
     * @var string
     */
    private $alternateProduct;

    /**
     * @var string
     */
    private $communicationChannel;

    /**
     * @var string
     */
    private $genericRemark;

    private function __construct()
    {
    }

    /**
     * @phpstan-ignore-next-line
     */
    public static function fromArray(array $data): self
    {
        $instance = new self();
        $instance->communicationLevel = $data['CommunicationLevel'] ?? null;
        $instance->myStore = $data['MyStore'] ?? null;
        $instance->customerRemark = $data['CustomerRemark'] ?? null;
        $instance->alternateProduct = $data['AlternateProduct'] ?? null;
        $instance->communicationChannel = $data['CommunicationChannel'] ?? null;
        $instance->genericRemark = $data['GenericRemark'] ?? null;

        return $instance;
    }

    public function getCommunicationLevel(): ?string
    {
        return $this->communicationLevel;
    }

    public function getMyStore(): ?string
    {
        return $this->myStore;
    }

    public function getCustomerRemark(): ?string
    {
        return $this->customerRemark;
    }

    public function getAlternateProduct(): ?string
    {
        return $this->alternateProduct;
    }

    public function getCommunicationChannel(): ?string
    {
        return $this->communicationChannel;
    }

    public function getGenericRemark(): ?string
    {
        return $this->genericRemark;
    }

    public function toArray(): array
    {
        return [
            'communicationLevel' => $this->getCommunicationLevel(),
            'myStore' => $this->getMyStore(),
            'customerRemark' => $this->getCustomerRemark(),
            'alternateProduct' => $this->getAlternateProduct(),
            'communicationChannel' => $this->getCommunicationChannel(),
            'genericRemark' => $this->getGenericRemark(),
        ];
    }

    public function toOneWelcomeFormat(): array
    {
        return [
            'CommunicationLevel' => $this->getCommunicationLevel(),
            'MyStore' => $this->getMyStore(),
            'CustomerRemark' => $this->getCustomerRemark(),
            'AlternateProduct' => $this->getAlternateProduct(),
            'CommunicationChannel' => $this->getCommunicationChannel(),
            'GenericRemark' => $this->getGenericRemark(),
        ];
    }
}
