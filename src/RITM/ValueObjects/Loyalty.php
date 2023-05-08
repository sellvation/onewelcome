<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\RITM\ValueObjects;

class Loyalty
{
    /**
     * @var string|null
     */
    private $assetId;

    /**
     * @var string|null
     */
    private $kindOfSaving;

    private function __construct()
    {
    }

    public static function fromArray(array $data): self
    {
        $instance = new self();
        $instance->assetId = $data['AssetID'] ?? null;
        $instance->kindOfSaving = $data['KindOfSaving'] ?? null;

        return $instance;
    }

    public function getAssetId(): ?string
    {
        return $this->assetId;
    }

    public function getKindOfSaving(): ?string
    {
        return $this->kindOfSaving;
    }

    public function toOneWelcomeFormat(): array
    {
        return [
            'AssetID' => $this->getAssetId(),
            'KindOfSaving' => $this->getKindOfSaving()
        ];
    }
}
