<?php

namespace App\Entity;

use App\Repository\InviteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV6Generator;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=InviteRepository::class)
 */
class Invite
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\CustomIdGenerator(class=UuidV6Generator::class)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $code;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        if ($this->id && $this->id != $id) {
            throw new \Exception('ID Cannot be modified');
        }

        $this->id = $id;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
