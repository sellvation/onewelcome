<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\RITM\ValueObjects;

class Address
{
    /**
     * @var string|null
     */
    private $street;

    /**
     * @var string|null
     */
    private $houseNumber;

    /**
     * @var string|null
     */
    private $houseNumberAddition;

    /**
     * @var string|null
     */
    private $postalCode;

    /**
     * @var string|null
     */
    private $city;

    /**
     * @var string|null
     */
    private $country;

    /**
     * @var string|null
     */
    private $countryISO2;

    /**
     * @var string|null
     */
    private $pOBoxNumber;

    /**
     * @var string|null
     */
    private $pOBoxNumberPostalCode;

    /**
     * @var string|null
     */
    private $pOBoxNumberCity;

    private function __construct()
    {
    }

    public static function fromArray(array $data): self
    {
        $instance = new self();
        $instance->street = $data['Street'] ?? null;
        $instance->houseNumber = $data['HouseNumber'] ?? null;
        if(isset($data['HouseNumberAddition']) && is_array($data['HouseNumberAddition'])){
            $instance->houseNumberAddition = null;
        }else{
            $instance->houseNumberAddition = $data['HouseNumberAddition'] ?? null;
        }
        $instance->postalCode = $data['PostalCode'] ?? null;
        $instance->city = $data['City'] ?? null;
        $instance->country = $data['Country'] ?? null;
        $instance->countryISO2 = $data['CountryISO2'] ?? null;
        $instance->pOBoxNumber = $data['POBoxNumber'] ?? null;
        $instance->pOBoxNumberPostalCode = $data['POBoxNumberPostalCode'] ?? null;
        $instance->pOBoxNumberCity = $data['POBoxNumberCity'] ?? null;

        return $instance;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getHouseNumber(): ?string
    {
        return $this->houseNumber;
    }

    public function getHouseNumberAddition(): ?string
    {
        return $this->houseNumberAddition;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getCountryISO2(): ?string
    {
        return $this->countryISO2;
    }

    public function getPOBoxNumber(): ?string
    {
        return $this->pOBoxNumber;
    }

    public function getPOBoxNumberPostalCode(): ?string
    {
        return $this->pOBoxNumberPostalCode;
    }

    public function getPOBoxNumberCity(): ?string
    {
        return $this->pOBoxNumberCity;
    }

    public function toOneWelcomeFormat(): array
    {
        return [
            'Street' => $this->getStreet(),
            'HouseNumber' => $this->getHouseNumber(),
            'HouseNumberAddition' => $this->getHouseNumberAddition(),
            'PostalCode' => $this->getPostalCode(),
            'City' => $this->getCity(),
            'Country' => $this->getCountry(),
            'CountryISO2' => $this->getCountryISO2(),
            'POBoxNumber' => $this->getPOBoxNumber(),
            'POBoxNumberPostalCode' => $this->getPOBoxNumberPostalCode(),
            'POBoxNumberCity' => $this->getPOBoxNumberCity(),
        ];
    }
}
