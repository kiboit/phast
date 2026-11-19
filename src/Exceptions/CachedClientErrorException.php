<?php

namespace Kibo\Phast\Exceptions;

/**
 * A cached exception whose original was a ClientErrorException.
 */
class CachedClientErrorException extends CachedExceptionException implements ClientErrorException {
}
