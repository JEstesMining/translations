<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *   name="invite_event_store",
 *   indexes={
 *     @ORM\Index(name="idx_01F6ADANB2M1SHQ0SAA3GJMHY2", columns={"aggregate_root_id"}, options={"where":"(aggregate_root_id IS NOT NULL)"})
 *   }
 * )
 * @ORM\Entity()
 */
class InviteEventStore extends EventStore
{
}
