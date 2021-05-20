<?php declare(strict_types=1);

namespace App\Message;

use App\PayloadTrait;
use App\MetadataTrait;

abstract class AbstractGenericMessage
{
    use PayloadTrait;
    use MetadataTrait;

    public function __construct(array $payload, array $metadata = [])
    {
        $this->setPayload($payload);
        $this->setMetadata($metadata);
    }
}
