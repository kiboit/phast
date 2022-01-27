<?php

namespace Kibo\Phast\Environment;

use Kibo\Phast\Environment\Exceptions\PackageHasNoDiagnosticsException;
use Kibo\Phast\Environment\Exceptions\PackageHasNoFactoryException;
use PHPUnit\Framework\TestCase;

class PackageTest extends TestCase {
    /**
     * @var Package
     */
    private $p1;

    /**
     * @var Package
     */
    private $p2;

    public function setUp(): void {
        $this->assertTrue(class_exists(TestPackage2\Factory::class));
        $this->assertTrue(class_exists(TestPackage1\Diagnostics::class));
        $this->p1 = Package::fromPackageClass(TestPackage1\Filter::class);
        $this->p2 = Package::fromPackageClass(TestPackage2\Cache::class);
    }

    public function testFromPackageClass() {
        $this->assertInstanceOf(Package::class, $this->p1);
        $this->assertInstanceOf(Package::class, $this->p2);
    }

    public function testCorrectlyInferredType() {
        $this->assertEquals('Filter', $this->p1->getType());
        $this->assertEquals('Cache', $this->p2->getType());
    }

    public function testExplicitType() {
        $p = Package::fromPackageClass(TestPackage1\Filter::class, 'HTMLFilter');
        $this->assertEquals('HTMLFilter', $p->getType());
    }

    public function testGetNamespace() {
        $this->assertEquals(TestPackage1::class, $this->p1->getNamespace());
        $this->assertEquals(TestPackage2::class, $this->p2->getNamespace());
    }

    public function testHasFactory() {
        $this->assertFalse($this->p1->hasFactory());
        $this->assertTrue($this->p2->hasFactory());
    }

    public function testGetFactory() {
        $this->assertInstanceOf(TestPackage2\Factory::class, $this->p2->getFactory());
        $this->expectException(PackageHasNoFactoryException::class);
        $this->p1->getFactory();
    }

    public function testHasDiagnostics() {
        $this->assertTrue($this->p1->hasDiagnostics());
        $this->assertFalse($this->p2->hasDiagnostics());
    }

    public function testGetDiagnostics() {
        $this->assertInstanceOf(TestPackage1\Diagnostics::class, $this->p1->getDiagnostics());
        $this->expectException(PackageHasNoDiagnosticsException::class);
        $this->p2->getDiagnostics();
    }
}
