<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\RITM\ValueObjects;

class Payment
{
    /**
     * @var string|null
     */
    private $debitId;

    /**
     * @var bool
     */
    private $isSaleOnAccount = false;

    /**
     * @var bool
     */
    private $isDirectDebit = false;

    /**
     * @var string|null
     */
    private $preferredPaymentMethod;

    /**
     * @var bool
     */
    private $shouldPrintItemsOnCustomerInvoice = false;

    private function __construct()
    {
    }

    public static function fromArray(array $data): self
    {
        $instance = new self();
        $instance->debitId = $data['DebitID'] ?? null;
        $instance->isSaleOnAccount = 'true' === ($data['IsSaleOnAccount'] ?? null);
        $instance->isDirectDebit = 'true' === ($data['IsDirectDebit'] ?? null);
        $instance->preferredPaymentMethod = $data['PreferredPaymentMethod'] ?? null;
        $instance->shouldPrintItemsOnCustomerInvoice = 'true' === ($data['PrintItemsOnCustomerInvoice'] ?? null);

        return $instance;
    }

    public function getDebitId(): ?string
    {
        return $this->debitId;
    }

    public function isSaleOnAccount(): bool
    {
        return $this->isSaleOnAccount;
    }

    public function isDirectDebit(): bool
    {
        return $this->isDirectDebit;
    }

    public function getPreferredPaymentMethod(): ?string
    {
        return $this->preferredPaymentMethod;
    }

    public function shouldPrintItemsOnCustomerInvoice(): bool
    {
        return $this->shouldPrintItemsOnCustomerInvoice;
    }

    public function toOneWelcomeFormat(): array
    {
        return [
            'PreferredPaymentMethod' => $this->getPreferredPaymentMethod(),
            'DebitID' => $this->getDebitId(),
            'PrintItemsOnCustomerInvoice' => $this->shouldPrintItemsOnCustomerInvoice(),
            'IsSaleOnAccount' => $this->isSaleOnAccount(),
            'IsDirectDebit' => $this->isDirectDebit()
        ];
    }
}
