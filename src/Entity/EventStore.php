<?php
/**
 * All event store tables extend this class.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;

/**
 * @ORM\MappedSuperclass()
 */
class EventStore
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=26, unique=true)
     */
    protected $eventId;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $eventType;

    /**
     * @ORM\Column(type="string", length=36, nullable=true)
     */
    protected $aggregateRootId;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $aggregateRootVersion;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="json", nullable=false)
     */
    protected $payload;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $metadata;

    /**
     * {@inheritdoc}
     */
    public function getEventId(): string
    {
        return $this->eventId;
    }

    /**
     * {@inheritdoc}
     */
    public function setEventId(string $eventId): self
    {
        if ($this->eventId) {
            throw new \Exception('Event ID cannot be modified');
        }

        $this->eventId = $eventId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEventType(): string
    {
        return $this->eventType;
    }

    /**
     * {@inheritdoc}
     */
    public function setEventType(string $eventType): self
    {
        if ($this->eventType) {
            throw new \Exception('Event Type cannot be modified');
        }

        $this->eventType = $eventType;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAggregateRootId(): ?string
    {
        return $this->aggregateRootId;
    }

    /**
     * {@inheritdoc}
     */
    public function setAggregateRootId(string $aggregateRootId): self
    {
        if ($this->aggregateRootId) {
            throw new \Exception('Aggregate Root ID cannot be modified');
        }

        $this->aggregateRootId = $aggregateRootId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAggregateRootVersion(): int
    {
        if (null === $this->aggregateRootVersion) {
            $this->aggregateRootVersion = 0;
        }

        return $this->aggregateRootVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function setAggregateRootVersion(int $aggregateRootVersion): self
    {
        if ($this->aggregateRootVersion) {
            throw new \Exception('Aggregate Root Version cannot be modified');
        }

        $this->aggregateRootVersion = $aggregateRootVersion;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        if ($this->createdAt) {
            throw new \Exception('Created At cannot be modified');
        }

        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPayload(): array
    {
        if (null === $this->payload) {
            $this->payload = [];
        }

        return $this->payload;
    }

    /**
     * {@inheritdoc}
     */
    public function setPayload(array $payload): self
    {
        if ($this->payload) {
            throw new \Exception('Payload cannot be modified');
        }

        $this->payload = $payload;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata(): array
    {
        if (null === $this->metadata) {
            $this->metadata = [];
        }

        return $this->metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetadata(array $metadata): self
    {
        if ($this->metadata) {
            throw new \Exception('Metadata cannot be modified');
        }

        $this->metadata = $metadata;

        return $this;
    }
}
