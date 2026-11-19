<?php

namespace Kibo\Phast\Filters\Image\Exceptions;

use Kibo\Phast\Exceptions\ClientErrorException;

/**
 * The image data is empty or cannot be decoded.
 */
class InvalidImageException extends ImageProcessingException implements ClientErrorException {
}
