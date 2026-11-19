<?php

namespace Kibo\Phast\Exceptions;

/**
 * Marks exceptions caused by the request's input rather than by Phast or the
 * server (e.g. an uploaded image that cannot be decoded). Services answer them
 * with a 4xx status instead of 500.
 */
interface ClientErrorException extends \Throwable {
}
