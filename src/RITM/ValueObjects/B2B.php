<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\RITM\ValueObjects;

class B2B
{
    /**
     * @var string|null
     */
    private $vatNumber;

    /**
     * @var string|null
     */
    private $companyName;

    /**
     * @var string|null
     */
    private $department;

    /**
     * @var string|null
     */
    private $costCenter;

    /**
     * @var string|null
     */
    private $chamberOfCommerceNumber;

    private function __construct()
    {
    }

    public static function fromArray(array $data): self
    {
        $instance = new self();
        $instance->vatNumber = $data['VATNumber'] ?? null;
        $instance->companyName = $data['CompanyName'] ?? null;
        $instance->department = $data['Department'] ?? null;
        $instance->costCenter = $data['CostCenter'] ?? null;
        $instance->chamberOfCommerceNumber = $data['KVKNumber'] ?? null;

        return $instance;
    }

    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function getCostCenter(): ?string
    {
        return $this->costCenter;
    }

    public function getChamberOfCommerceNumber(): ?string
    {
        return $this->chamberOfCommerceNumber;
    }

    public function toOneWelcomeFormat(): array
    {
        return [
            'CompanyName' => $this->getCompanyName(),
            'Department' => $this->getDepartment(),
            'KVKNumber' => $this->getChamberOfCommerceNumber(),
            'VATNumber' => $this->getVatNumber(),
            'CostCenter' => $this->getCostCenter()
        ];
    }
}
