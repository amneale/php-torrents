<?php

declare(strict_types=1);

namespace spec\Amneale\Torrent;

use Amneale\Torrent\Torrent;
use PhpSpec\ObjectBehavior;

class TorrentSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Torrent::class);
    }
}
