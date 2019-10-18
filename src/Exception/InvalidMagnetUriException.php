<?php

declare(strict_types=1);

namespace Amneale\Torrent\Exception;

use Amneale\Torrent\Exception;

class InvalidMagnetUriException extends \InvalidArgumentException implements Exception
{
}
