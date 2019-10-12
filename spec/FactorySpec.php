<?php

namespace spec\Amneale\Torrent;

use Amneale\Torrent\Decoder;
use Amneale\Torrent\Torrent;
use PhpSpec\ObjectBehavior;
use Vfs\FileSystem;

class FactorySpec extends ObjectBehavior
{
    public function let(Decoder $decoder): void
    {
        $this->beConstructedWith($decoder);
    }

    public function it_loads_a_torrent_from_a_file(): void
    {
        $filesystem = FileSystem::factory();
        $filesystem->mount();

        $filename = 'vfs://' . uniqid('file', true);
        file_put_contents($filename, 'encoded-data');

        $this->fromFile($filename)->shouldBeAnInstanceOf(Torrent::class);
    }
}
