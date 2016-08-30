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

    public function testRandomDate()
    {
        $results = [];
        $format = 'Y-m-d';
        for ($i = 0; $i < 10000; $i++) {
            $date = RandomHelper::randomDate();
            $vDate = \DateTime::createFromFormat($format, $date);
            $this->assertEquals($date, $vDate->format($format), 'Invalid date');
            $results[] = $date;
        }

        $maxDuplicates = 3;
        $counted = [];
        foreach ($results as $date) {
            if (array_key_exists($date, $counted)) {
                $counted[$date]++;
            } else {
                $counted[$date] = 1;
            }
        }
        $this->assertTrue($maxDuplicates >= max($counted),
            'Too many duplicated entries: ' . max($counted));

        $dateFrom = new \DateTime('2000-01-01 00:00:00');
        $dateTo = new \DateTime('2001-06-30 23:59:59');
        for ($i = 0; $i < 10000; $i++) {
            $date = RandomHelper::randomDate($dateFrom->format($format),
                $dateTo->format($format));
            $vDate = \DateTime::createFromFormat($format, $date);
            $this->assertTrue(($vDate->getTimestamp() >= $dateFrom->getTimestamp())
                && ($vDate->getTimestamp() <= $dateTo->getTimestamp())
                , 'Date out of bounds');
        }
    }
}
