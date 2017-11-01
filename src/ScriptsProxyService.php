<?php

namespace Kibo\Phast;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Exceptions\ItemNotFoundException;
use Kibo\Phast\Security\ServiceSignature;

class ScriptsProxyService extends Service {

    /**
     * @var ObjectifiedFunctions
     */
    private $functions;

    /**
     * ScriptsProxyService constructor.
     *
     * @param ServiceSignature $signature
     */
    public function __construct(ServiceSignature $signature, ObjectifiedFunctions $functions = null) {
        $this->signature = $signature;
        $this->functions = is_null($functions) ? new ObjectifiedFunctions() : $functions;
    }

    protected function handle(array $request) {
        $result = @$this->functions->file_get_contents($request['src']);
        if ($result === false) {
            throw new ItemNotFoundException("Could not get {$request['src']}!");
        }
        return $result;
    }

}
