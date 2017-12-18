<?php


namespace Kibo\Phast\Diagnostics;


use Kibo\Phast\Environment\Configuration;
use Kibo\Phast\Environment\Package;
use Kibo\Phast\Exceptions\ImageProcessingException;

class SystemDiagnostics {

    /**
     * @param array $config
     * @return Status[]
     */
    public function run(array $config) {

        $runtimeConfig = (new Configuration($config))->toArray();

        $results = [];
        foreach (array_keys($config['images']['filters']) as $filter) {
            $enabled = isset ($runtimeConfig['images']['filters'][$filter]);
            $package = Package::fromPackageClass($filter);
            $diagnostic = $package->getDiagnostics();
            try {
                $diagnostic->diagnose($config);
                $results[] = new Status($package, true, '', $enabled);
            } catch (ImageProcessingException $e) {
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
        return $results;
    }
}
