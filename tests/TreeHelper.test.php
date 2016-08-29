<?php
require __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use PHPIntegration\Utils\TreeHelper;
use PHPIntegration\Utils\RandomHelper;

class TreeHelperTest extends TestCase
{
    private function generateRandomArray(int $deep)
    {
        $arr = [];

        $count = rand(1, $deep);
        for ($i = 0; $i < $count; $i++) {
            if ($deep > 0) {
                $k = RandomHelper::randomString();
                $arr[$k] = rand(0, 3) == 1
                    ? $this->generateRandomArray($deep - 1)
                    : $k;
            } else {
                $k = RandomHelper::randomString();
                $arr[$k] = $k;
            }
        }

        return $arr;
    }

    public function testRandomPath()
    {
        $arr = $this->generateRandomArray(rand(1, 20));
        $paths = [];

        for ($i = 0; $i < 10000; $i++) {
            $key = TreeHelper::randomPath($arr);
            $tmpKey = $key;
            $tmpVal = $arr;
            while (is_array($tmpVal) && !empty($tmpKey)) {
                $k = array_shift($tmpKey);
                if (array_key_exists($k, $tmpVal)) {
                    $tmpVal = $tmpVal[$k];
                }
            }

            $pathString = implode(' -> ', $key);
            $this->assertTrue(
                is_string($tmpVal),
                'Key ' .  $pathString
                . ' doesn\'t exist in ' .  var_export($arr, true)
            );

            if (array_key_exists($pathString, $paths)) {
                $paths[$pathString]++;
            } else {
                $paths[$pathString] = 1;
            }
        }

        $allPaths = array_map(
            function ($p) {
                return implode(' -> ', $p);
            },
            TreeHelper::allPaths($arr)
        );

        $this->assertEmpty(
            array_filter(
                $paths,
                function ($p) use ($allPaths) {
                    return !in_array($p, $allPaths);
                },
                ARRAY_FILTER_USE_KEY
            ),
            'Some paths were not visited.' . "\n"
            . var_export($paths, true) . "\n" . var_export($allPaths, true)
        );
    }
}

