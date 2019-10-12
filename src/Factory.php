<?php

declare(strict_types=1);

namespace Amneale\Torrent;

class Factory
{
    /**
     * @var Encoder
     */
    private $encoder;

    /**
     * @var Decoder
     */
    private $decoder;

    /**
     * @param Encoder $encoder
     * @param Decoder $decoder
     */
    public function __construct(Encoder $encoder, Decoder $decoder)
    {
        $this->encoder = $encoder;
        $this->decoder = $decoder;
    }

    /**
     * @param string $path
     *
     * @return Torrent
     */
    public function fromFile(string $path): Torrent
    {
        $data = $this->decoder->decode(
            file_get_contents($path)
        );

        $torrent = new Torrent(
            $data['info']['name'],
            $this->getHash($data['info']),
            $this->getTrackers($data),
            $this->getSize($data),
            $this->getCreationDate($data)
        );

        return $torrent;
    }

    /**
     * @param array $info
     *
     * @return string
     */
    private function getHash(array $info): string
    {
        $infoString = $this->encoder->encode($info);

        return sha1($infoString);
    }

    /**
     * @param $data
     *
     * @return array
     */
    private function getTrackers($data): array
    {
        $trackers = [];

        if (isset($data['announce-list'])) {
            array_walk_recursive(
                $data['announce-list'],
                static function (string $uri) use (&$trackers): void {
                    $trackers[] = $uri;
                }
            );

            return $trackers;
        }

        if (isset($data['announce'])) {
            return [$data['announce']];
        }

        return $trackers;
    }

    /**
     * @param $data
     *
     * @return array|null
     */
    private function getSize($data): ?int
    {
        if (isset($data['files'])) {
            return array_sum(
                array_column($data['files'], 'length')
            );
        }

        if (isset($data['length'])) {
            return $data['length'];
        }

        return null;
    }

    /**
     * @param array $data
     *
     * @return \DateTime|null
     */
    private function getCreationDate(array $data): ?\DateTime
    {
        if (!empty($data['creation date'])) {
            return \DateTime::createFromFormat('U', (string) $data['creation date']);
        }

        return null;
    }
}
