<?php

namespace Amneale\Torrent;

class Factory
{
    /**
     * @var Decoder
     */
    private $decoder;

    /**
     * @param Decoder $decoder
     */
    public function __construct(Decoder $decoder)
    {
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
//
//        $torrent->setHash($this->getHash($decoded['info']));
//        $torrent->setName($decoded['info']['name']);
//
//        if (isset($decoded['creation date'])) {
//            $torrent->setCreatedAt(\DateTimeImmutable::createFromFormat('U', (string) $decoded['creation date']));
//        }
//
//        $trackerRepository = $this->objectManager->getRepository(Tracker::class);
//        $trackers = $torrent->getTrackers();
//        if (isset($decoded['announce-list'])) {
//            $trackers->clear();
//            array_walk_recursive(
//                $decoded['announce-list'],
//                function (string $uri) use ($trackers, $trackerRepository): void {
//                    if (!$trackers->exists(
//                        function (int $position, Tracker $tracker) use ($uri) {
//                            return $tracker->getUri() === $uri;
//                        }
//                    )) {
//                        $trackers->add($trackerRepository->findOrCreate($uri));
//                    }
//                }
//            );
//        } elseif (isset($decoded['announce'])) {
//            $trackers->clear();
//            $trackers->add($trackerRepository->findOrCreate($decoded['announce']));
//        }
//
//        if (isset($decoded['files'])) {
//            $size = 0;
//            foreach ($decoded['files'] as $file) {
//                $size += $file['length'];
//            }
//            $torrent->setSize($size);
//        } elseif (isset($decoded['length'])) {
//            $torrent->setSize($decoded['length']);
//        }
//
//        $torrent->setParsedAt(new \DateTimeImmutable());
//
//        return $torrent;


        return new Torrent();
    }
}
