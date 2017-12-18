<?php


namespace Kibo\Phast\Diagnostics;


use Kibo\Phast\Environment\Configuration;
use Kibo\Phast\Environment\Exceptions\PackageHasNoDiagnosticsException;
use Kibo\Phast\Environment\Package;
use Kibo\Phast\Exceptions\RuntimeException;

class SystemDiagnostics {

    /**
     * @param array $config
     * @return Status[]
     */
    public function run(array $config) {
        $results = [];
        foreach ($this->getExaminedItems($config) as $type => $group) {
            foreach ($group['items'] as $name) {
                $enabled = in_array($name, $group['runtime']);
                $package = Package::fromPackageClass($name, $type);
                try {
                    $diagnostic = $package->getDiagnostics();
                    $diagnostic->diagnose($config);
                    $results[] = new Status($package, true, '', $enabled);
                } catch (PackageHasNoDiagnosticsException $e) {
                    $results[] = new Status($package, true, '', $enabled);
                } catch (RuntimeException $e) {
                    $results[] = new Status($package, false, $e->getMessage(), $enabled);
                } catch (\Exception $e) {
                    $results[] = new Status(
                        $package,
                        false,
                        sprintf(
                            'Unknown error: Exception: %s, Message: %s, Code: %s',
                            get_class($e),
                            $e->getMessage(),
                            $e->getCode()
                        ),
                        $enabled
                    );
                }
            }
        }
        return $results;
    }

    private function getExaminedItems(array $config) {
        $runtimeConfig = (new Configuration($config))->toArray();
        return [
            'HTMLFilter' => [
                'items' => array_keys($config['documents']['filters']),
                'runtime' => array_keys($runtimeConfig['documents']['filters'])
            ],
            'ImageFilter' => [
                'items' => array_keys($config['images']['filters']),
                'runtime' => array_keys($runtimeConfig['images']['filters'])
            ]
        ];
    }
}
