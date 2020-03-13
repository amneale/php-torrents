<?php

declare(strict_types=1);

namespace spec\Amneale\Torrent;

use Amneale\Torrent\Magnet;
use PhpSpec\ObjectBehavior;

class TorrentSpec extends ObjectBehavior
{
    private const HASH = '0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33';
    private const NAME = 'Foo bar baz torrent file';
    private const TRACKERS = [
        'http://foo.bar.tracker:80',
        'udp://foo.bar.tracker:1234',
    ];

    public function it_can_return_a_magnet(): void
    {
        $this->beConstructedWith(
            self::HASH,
            self::NAME,
            self::TRACKERS,
            1024 * 1024 * 1024,
            new \DateTime()
        );

        $magnet = $this->toMagnet();

        $magnet->shouldBeAnInstanceOf(Magnet::class);
        $magnet->infoHash->shouldBe(self::HASH);
        $magnet->name->shouldBe(self::NAME);
        $magnet->trackers->shouldBe(self::TRACKERS);
    }
}
