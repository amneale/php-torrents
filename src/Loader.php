<?php

declare(strict_types=1);

namespace Amneale\Torrent;

use Amneale\Torrent\Engine\Decoder;
use Amneale\Torrent\Engine\Encoder;

final class Loader
{
    private Encoder $encoder;
    private Decoder $decoder;
    private Provider $provider;

    public function __construct(Encoder $encoder, Decoder $decoder, Provider $provider)
    {
        $this->encoder = $encoder;
        $this->decoder = $decoder;
        $this->provider = $provider;
    }

    public function fromInfoHash(string $hash): Torrent
    {
        return $this->fromFile(
            $this->provider->getDownloadUrl($hash)
        );
    }

    public function fromMagnetUri(string $uri): Torrent
    {
        $magnet = Magnet::fromUri($uri);

        return $this->fromInfoHash($magnet->infoHash);
    }

    public function fromFile(string $path): Torrent
    {
        $data = $this->decoder->decode(
            file_get_contents($path)
        );

        return new Torrent(
            $this->getHash($data['info']),
            $data['info']['name'],
            $this->getTrackers($data),
            $this->getSize($data),
            $this->getCreationDate($data)
        );
    }

    private function getHash(array $info): string
    {
        $infoString = $this->encoder->encode($info);

        return sha1($infoString);
    }

    private function getTrackers(array $data): array
    {
        if (isset($data['announce-list'])) {
            $trackers = [];
            array_walk_recursive(
                $data['announce-list'],
                static function (string $uri) use (&$trackers): void {
                    $trackers[] = $uri;
                }
            );

            return array_unique($trackers);
        }

        if (isset($data['announce'])) {
            return [$data['announce']];
        }

        return [];
    }

    private function getSize($data): ?int
    {
        if (isset($data['files'])) {
            return array_sum(
                array_column($data['files'], 'length')
            );
        }

        return $data['length'] ?? null;
    }

    private function getCreationDate(array $data): ?\DateTime
    {
        if (!empty($data['creation date'])) {
            return \DateTime::createFromFormat('U', (string) $data['creation date']);
        }

        return null;
    }
}
