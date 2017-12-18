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
        if (!isset ($config['images'])) {
            return [new Status('Filters\Image', false, '"images" section not found in config!', false)];
        }
        if (!isset ($config['images']['filters'])) {
            return [new Status('Filters\Image', false, '"images.filters" section not found in config!', false)];
        }
        $runtimeConfig = (new Configuration($config))->toArray();

        $results = [];
        foreach (array_keys($config['images']['filters']) as $filter) {
            $enabled = isset ($runtimeConfig['images']['filters'][$filter]);
            $package = Package::fromPackageClass($filter);
            $diagnostic = $package->getDiagnostics();
            try {
                $diagnostic->diagnose($config);
                $results[] = new Status($filter, true, '', $enabled);
            } catch (ImageProcessingException $e) {
                $results[] = new Status($filter, false, $e->getMessage(), $enabled);
            } catch (\Exception $e) {
                $results[] = new Status(
                    $filter,
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
