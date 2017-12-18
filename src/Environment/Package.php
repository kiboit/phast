<?php

namespace Kibo\Phast\Environment;


use Kibo\Phast\Diagnostics\Diagnostics;
use Kibo\Phast\Environment\Exceptions\PackageHasNoDiagnosticsException;
use Kibo\Phast\Environment\Exceptions\PackageHasNoFactoryException;

class Package {

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @param $className
     * @param string|null $type
     * @return Package
     */
    public static function fromPackageClass($className, $type = null) {
        $instance = new self();
        $lastSeparatorPosition = strrpos($className, '\\');
        $instance->type = empty ($type) ? substr($className, $lastSeparatorPosition + 1) : $type;
        $instance->namespace = substr($className, 0, $lastSeparatorPosition);
        return $instance;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getNamespace() {
        return $this->namespace;
    }

    /**
     * @return bool
     */
    public function hasFactory() {
        return class_exists($this->getFactoryClassName());
    }

    /**
     * @return mixed
     */
    public function getFactory() {
        if ($this->hasFactory()) {
            $class = $this->getFactoryClassName();
            return new $class();
        }
        throw new PackageHasNoFactoryException("Package {$this->namespace} has no factory");
    }

    /**
     * @return bool
     */
    public function hasDiagnostics() {
        return class_exists($this->getDiagnosticsClassName());
    }

    /**
     * @return Diagnostics
     */
    public function getDiagnostics() {
        if ($this->hasDiagnostics()) {
            $class = $this->getDiagnosticsClassName();
            return new $class();
        }
        throw new PackageHasNoDiagnosticsException("Package {$this->namespace} has no diagnostics");
    }

    private function getFactoryClassName() {
        return $this->getClassName('Factory');
    }

    private function getDiagnosticsClassName() {
        return $this->getClassName('Diagnostics');
    }

    private function getClassName($class) {
        return $this->namespace . '\\' . $class;
    }

}
