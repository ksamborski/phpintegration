<?php
require __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use PHPIntegration\Utils\RandomHelper;

class RandomHelperTest extends TestCase
{
    public function testRandomString()
    {
        $results = [];
        for ($i = 0; $i < 10000; $i++) {
            $str = RandomHelper::randomString();
            $this->assertTrue(!in_array($str, $results), 'Duplicated string');
            $results[] = $str;
        }
    }

    public function testRandomStringLenAndChars()
    {
        $characters = str_split('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ _-/{}&%@$()*[]?,.<>ąęłóśćńżźĄĘÓŁŚĆŃŻŹ=');
        $chars_len = count($characters);
        for ($i = 0; $i < 10000; $i++) {
            shuffle($characters);
            $count = rand(1, 500);
            $chars = substr(
                join('', $characters),
                rand(0, $chars_len / 2),
                rand($chars_len / 2, $chars_len)
            );
            $str = RandomHelper::randomString($count, $chars);

            $this->assertEquals($count, strlen($str));
            $this->assertEmpty(
                array_filter(
                    str_split($str),
                    function ($c) use ($chars) {
                        return strpos($chars, $c) === false;
                    }
                )
            );
        }
    }

    public function testRandomEmail()
    {
        for ($i = 0; $i < 10000; $i++) {
            $email = RandomHelper::randomEmail();
            $filtered = filter_var($email, FILTER_VALIDATE_EMAIL);
            $this->assertTrue($filtered !== false, 'Bad email: ' . $email);
        }
    }

    public function testRandomArray()
    {
        $possibleValues = [];

        for ($i = 0; $i < 10000; $i++) {
            $possibleValues[] = $i;
        }

        for ($i = 0; $i < 500; $i++) {
            $duplicates = rand(0, 1) == 0;
            $min = rand(0, 100);
            $max = rand($min, 100);
            $arr = RandomHelper::randomArray($possibleValues, $duplicates, $min, $max);

            $this->assertTrue(count($arr) >= $min && count($arr) <= $max);
            $this->assertEmpty(
                array_filter(
                    $arr,
                    function ($item) use ($possibleValues) {
                        return !in_array($item, $possibleValues);
                    }
                )
            );

            if (!$duplicates) {
                $this->assertEquals(count($arr), count(array_unique($arr, SORT_NUMERIC)));
            }
        }
    }

    public function testRandomOneOf()
    {
        $possibleValues = [];

        for ($i = 0; $i < 10000; $i++) {
            $possibleValues[] = $i;
        }

        for ($i = 0; $i < 10000; $i++) {
            $item = RandomHelper::randomOneOf($possibleValues);
            $this->assertTrue(in_array($item, $possibleValues));
        }
    }
}
