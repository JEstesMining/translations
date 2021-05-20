<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *   name="user_event_store",
 *   indexes={
 *     @ORM\Index(name="idx_01F65S09T9MJZS3ZQNDJ5X0F9J", columns={"aggregate_root_id"}, options={"where":"(aggregate_root_id IS NOT NULL)"})
 *   }
 * )
 * @ORM\Entity()
 */
class UserEventStore extends EventStore
{
}
