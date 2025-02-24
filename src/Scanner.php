<?php

namespace Dorayaki4369\Decoravel;

use Composer\ClassMapGenerator\ClassMapGenerator;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Finder\Finder;

class Scanner
{
    /**
     * @return class-string[]
     */
    public function scanDecoratedClasses(): array
    {
        $classes = $this->scanClasses();

        return array_values(array_filter(array_map(function (string $class): ?string {
            try {
                $ref = new ReflectionClass($class);

                if (!isDecoratableClass($ref)) {
                    return null;
                }

                return $class;
            } catch (ReflectionException) {
                return null;
            }
        }, $classes)));
    }

    /**
     * @return class-string[]
     */
    protected function scanClasses(): array
    {
        $phpFiles = Finder::create()
            ->files()
            ->name('*.php')
            ->in(config('decoravel.scan_directories', []))
            ->getIterator();

        $classes = [];
        foreach ($phpFiles as $info) {
            $class = ClassMapGenerator::createMap($info->getPathname());
            if (empty($class)) {
                continue;
            }

            $classes[] = array_keys($class)[0];
        }

        return $classes;
    }
}