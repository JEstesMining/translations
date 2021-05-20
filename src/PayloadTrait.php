<?php declare(strict_types=1);

namespace App;

trait PayloadTrait
{
    private $_payload = [];

    public function getPayload(): array
    {
        return $this->_payload;
    }

    public function setPayload(array $_payload): self
    {
        if (!empty($this->_payload)) {
            throw new \Exception('Cannot set payload while an existing payload exists.');
        }

        $this->_payload = $_payload;

        return $this;
    }

    public function getPayloadValue(string $key)
    {
        if ($this->hasPayloadValue($key)) {
            return $this->_payload[$key];
        }
    }

    public function hasPayloadValue(string $key): bool
    {
        return isset($this->_payload[$key]);
    }

    public function isPayloadValueEmpty(string $key): bool
    {
        return empty($this->_payload[$key]);
    }
}
