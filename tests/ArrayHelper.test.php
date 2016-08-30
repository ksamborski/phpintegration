<?php
require __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use PHPIntegration\Utils\ArrayHelper;

class ArrayHelperTest extends TestCase
{
    public function testUnsetPath()
    {
        $arr = [
            "key1" => true,
            "key2" => [
                ["key3" => true],
                ["key3" => true],
                ["key3" => true]
            ],
            "key4" => [
                ["key5" => true],
                ["key5" => true],
                "key6" => true
            ]
        ];

        $test1 = $arr;
        $this->assertTrue(ArrayHelper::unsetPath(["key2"], $test1));
        $this->assertTrue(!array_key_exists("key2", $test1));

        $test2 = $arr;
        $this->assertTrue(
            ArrayHelper::unsetPath(
                ["key2", "key3"],
                $test2,
                ArrayHelper::ARRAY_TAKE_RANDOM
            )
        );
        $this->assertCount(
            2,
            array_filter(
                $test2["key2"],
                function ($i) {
                    return array_key_exists("key3", $i);
                }
            )
        );

        $test3 = $arr;
        $this->assertTrue(
            ArrayHelper::unsetPath(
                ["key2", "key3"],
                $test3,
                ArrayHelper::ARRAY_TAKE_ALL
            )
        );
        $this->assertEmpty(
            array_filter(
                $test3["key2"],
                function ($i) {
                    return array_key_exists("key3", $i);
                }
            )
        );

        $test4 = $arr;
        $this->assertNotTrue(
            ArrayHelper::unsetPath(
                ["key2", "key3"],
                $test4,
                ArrayHelper::ARRAY_TAKE_NONE
            )
        );
        $this->assertCount(
            3,
            array_filter(
                $test4["key2"],
                function ($i) {
                    return array_key_exists("key3", $i);
                }
            )
        );

        $test5 = $arr;
        $this->assertNotTrue(ArrayHelper::unsetPath(["key4", "key5"], $test5));
        $this->assertTrue(array_key_exists("key6", $test5["key4"]));
        $this->assertCount(
            2,
            array_filter(
                $test5["key4"],
                function ($i) {
                    return is_array($i) && array_key_exists("key5", $i);
                }
            )
        );

        $test6 = $arr;
        $this->assertNotTrue(ArrayHelper::unsetPath(["key41", "key52"], $test6));

        $test7 = $arr;
        $this->assertTrue(ArrayHelper::unsetPath(["key4", 0, "key5"], $test7));
        $this->assertCount(
            1,
            array_filter(
                $test7["key4"],
                function ($i) {
                    return is_array($i) && array_key_exists("key5", $i);
                }
            )
        );
    }

    public function testUpdatePath()
    {
        $arr = [
            "key1" => true,
            "key2" => [
                ["key3" => true],
                ["key3" => true],
                ["key3" => true]
            ],
            "key4" => [
                ["key5" => true],
                ["key5" => true],
                "key6" => true
            ]
        ];

        $not = function ($v) {
            return !is_array($v) && !$v;
        };

        $test1 = $arr;
        $this->assertTrue(ArrayHelper::updatePath(["key2"], $test1, $not));
        $this->assertTrue($test1["key2"] === false);

        $test2 = $arr;
        $this->assertTrue(
            ArrayHelper::updatePath(
                ["key2", "key3"],
                $test2,
                $not,
                ArrayHelper::ARRAY_TAKE_RANDOM
            )
        );
        $this->assertCount(
            2,
            array_filter(
                $test2["key2"],
                function ($i) {
                    return array_key_exists("key3", $i) && $i["key3"] === true;
                }
            )
        );

        $test3 = $arr;
        $this->assertTrue(
            ArrayHelper::updatePath(
                ["key2", "key3"],
                $test3,
                $not,
                ArrayHelper::ARRAY_TAKE_ALL
            )
        );
        $this->assertEmpty(
            array_filter(
                $test3["key2"],
                function ($i) {
                    return array_key_exists("key3", $i) && $i["key3"] === true;
                }
            )
        );

        $test4 = $arr;
        $this->assertNotTrue(
            ArrayHelper::updatePath(
                ["key2", "key3"],
                $test4,
                $not,
                ArrayHelper::ARRAY_TAKE_NONE
            )
        );
        $this->assertCount(
            3,
            array_filter(
                $test4["key2"],
                function ($i) {
                    return array_key_exists("key3", $i) && $i["key3"] === true;
                }
            )
        );

        $test5 = $arr;
        $this->assertNotTrue(ArrayHelper::updatePath(["key4", "key5"], $test5, $not));
        $this->assertTrue($test5["key4"]["key6"]);
        $this->assertCount(
            2,
            array_filter(
                $test5["key4"],
                function ($i) {
                    return is_array($i) && array_key_exists("key5", $i) && $i["key5"] === true;
                }
            )
        );

        $test6 = $arr;
        $this->assertNotTrue(ArrayHelper::updatePath(["key41", "key52"], $test6, $not));

        $test7 = $arr;
        $this->assertTrue(ArrayHelper::updatePath(["key4", 0, "key5"], $test7, $not));
        $this->assertCount(
            1,
            array_filter(
                $test7["key4"],
                function ($i) {
                    return is_array($i) && array_key_exists("key5", $i) && $i["key5"] === true;
                }
            )
        );
    }
}

