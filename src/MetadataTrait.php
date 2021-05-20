<?php declare(strict_types=1);

namespace App;

trait MetadataTrait
{
    private $_metadata = [];

    public function getMetadata(): array
    {
        return $this->_metadata;
    }

    public function setMetadata(array $_metadata): self
    {
        if (!empty($this->_metadata)) {
            throw new \Exception('Cannot set metadata while an existing metadata exists.');
        }

        $this->_metadata = $_metadata;

        return $this;
    }

    public function getMetadataValue(string $key)
    {
        if ($this->has($key)) {
            return $this->_metadata[$key];
        }
    }

    public function hasMetadataValue(string $key): bool
    {
        return isset($this->_metadata[$key]);
    }

    public function isMetadataValueEmpty(string $key): bool
    {
        return empty($this->_metadata[$key]);
    }
}
