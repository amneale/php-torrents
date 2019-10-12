<?php

declare(strict_types=1);

namespace spec\Amneale\Torrent;

use Amneale\Torrent\Decoder;
use Amneale\Torrent\Encoder;
use Amneale\Torrent\Torrent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Vfs\FileSystem;

class FactorySpec extends ObjectBehavior
{
    private const TORRENT_HASH = '2fd4e1c67a2d28fced849ee1bb76e7391b93eb12';
    private const TORRENT_DATA = [
        'info' => [
            'name' => 'my-torrent',
        ],
    ];

    private static $fileSystemMounted = false;

    public function let(Encoder $encoder, Decoder $decoder): void
    {
        $encoder->encode(Argument::any())->willReturn(self::TORRENT_HASH);

        $this->beConstructedWith($encoder, $decoder);
    }

    public function it_loads_a_torrent_from_a_file(Decoder $decoder): void
    {
        $decoder->decode(Argument::any())->willReturn(self::TORRENT_DATA);

        $torrent = $this->fromFile($this->createVirtualFile('torrent-data'));
        $torrent->shouldBeAnInstanceOf(Torrent::class);
        $torrent->getName()->shouldBe(self::TORRENT_DATA['info']['name']);
        $torrent->getInfoHash()->shouldBe(sha1(self::TORRENT_HASH));
    }

    public function it_can_can_read_trackers_from_nested_announce_list(Decoder $decoder): void
    {
        $decoder->decode(Argument::any())->willReturn(
            array_merge(
                self::TORRENT_DATA,
                [
                    'announce-list' => [
                        'foo.bar/tracker',
                        'nested' => ['foo.bar/tracker2'],
                    ],
                ]
            )
        );

        $torrent = $this->fromFile($this->createVirtualFile('torrent-data'));
        $torrent->getTrackers()->shouldBe(['foo.bar/tracker', 'foo.bar/tracker2']);
    }

    public function it_can_read_a_single_tracker_from_torrent_data(Decoder $decoder): void
    {
        $decoder->decode(Argument::any())->willReturn(
            array_merge(self::TORRENT_DATA, ['announce' => 'foo.bar/tracker'])
        );

        $torrent = $this->fromFile($this->createVirtualFile('torrent-data'));
        $torrent->getTrackers()->shouldBe(['foo.bar/tracker']);
    }

    public function it_can_sum_file_sizes(Decoder $decoder): void
    {
        $decoder->decode(Argument::any())->willReturn(
            array_merge(
                self::TORRENT_DATA,
                [
                    'files' => [
                        ['length' => 1],
                        ['length' => 2],
                        ['length' => 3],
                        ['length' => 4],
                    ],
                ]
            )
        );

        $torrent = $this->fromFile($this->createVirtualFile('torrent-data'));
        $torrent->getSize()->shouldBe(10);
    }

    public function it_can_read_length_from_torrent_data(Decoder $decoder): void
    {
        $decoder->decode(Argument::any())->willReturn(
            array_merge(self::TORRENT_DATA, ['length' => 12345])
        );

        $torrent = $this->fromFile($this->createVirtualFile('torrent-data'));
        $torrent->getSize()->shouldBe(12345);
    }

    public function it_can_read_creation_date_from_torrent_data(Decoder $decoder): void
    {
        $time = time();
        $decoder->decode(Argument::any())->willReturn(
            array_merge(self::TORRENT_DATA, ['creation date' => $time])
        );

        $torrent = $this->fromFile($this->createVirtualFile('torrent-data'));
        $torrent->getCreationDate()->shouldBeLike(new \DateTime("@{$time}"));
    }

    /**
     * @param string $data
     *
     * @return string
     */
    private function createVirtualFile(string $data): string
    {
        if (!self::$fileSystemMounted) {
            $filesystem = FileSystem::factory();
            $filesystem->mount();
            self::$fileSystemMounted = true;
        }

        $filename = 'vfs://' . uniqid('file', true);
        file_put_contents($filename, $data);

        return $filename;
    }
}
