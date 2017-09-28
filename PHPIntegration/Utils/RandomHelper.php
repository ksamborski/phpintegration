<?php

namespace PHPIntegration\Utils;

use \PHPIntegration\Randomizable;

/**
 * Bunch of useful random generators.
 */
class RandomHelper
{
    /**
     * Generates random string.
     * @param int $length Length of the string
     * @param string $characters Characters that can appear in the string
     * @return string Random string
     */
    public static function randomString(
        int $length = 10,
        string $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ _-/{}&%@$()*[]?,.<>'
    ) : string {
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Generates random email.
     * @return string Random email string.
     */
    public static function randomEmail() : string
    {
        $p1 = preg_replace(
            '/[_\-.]{2,}/',
            self::randomString(1, '-_.'),
            trim(
                self::randomString(rand(10, 50), '0123456789abcdefghijklmnopqrstuvwxyz-._'),
                '-._'
            )
        );
        $p2 = preg_replace(
            '/[\-]{2,}/',
            '-',
            trim(
                self::randomString(rand(4, 8), '0123456789abcdefghijklmnopqrstuvwxyz-'),
                '-'
            )
        );
        $dot = self::randomString(rand(2, 4), 'abcdefghijklmnopqrstuvwxyz');
        return $p1 . '@' . $p2 . '.' . $dot;
    }

    /**
     * Picks one random value from provided array.
     * @param array $possibleValues Array of possible values
     * @return mixed Random item from the array
     */
    public static function randomOneOf(array $possibleValues)
    {
        return RandomHelper::randomArray($possibleValues, false, 1, 1)[0];
    }

    /**
     * Generates random array consisting of provided values.
     * @param array $possibleValues Array of possible values
     * @param bool $duplicates Whether it can have duplicated values.
     * @param int $minLength Minimum number of elements
     * @param int $maxLength Maximum number of elements
     * @return array Random array
     */
    public static function randomArray(
        array $possibleValues,
        bool $duplicates = true,
        int $minLength = 0,
        int $maxLength = 10
    ) : array {
        $arr = [];
        $len = rand($minLength, $maxLength);
        $max = count($possibleValues) - 1;
        
        $vals = array_values($possibleValues);
        for ($x = 0; $x < $len && $max >= 0; $x++) {
            $idx = rand(0, $max);
            $arr[] = $vals[$idx];

            if (!$duplicates) {
                $max--;
                array_splice($vals, $idx, 1);
            }
        }

        return $arr;
    }

    /**
     * Generates random array consisting of random elements.
     * For example you can write \PHPIntegration\RandomHelper::randomMany('\PHPIntegration\RandomHelper::randomEmail')
     * @param mixed $generatorFunc Function that will generate random value.
     * @param int $minLength Minimum number of elements
     * @param int $maxLength Maximum number of elements
     * @return array Random array of random values
     */
    public static function randomMany(
        $generatorFunc,
        int $minLength = 0,
        int $maxLength = 10
    ) : array {
        $arr = [];
        $len = rand($minLength, $maxLength);
        
        for ($x = 0; $x < $len; $x++) {
            $arr[] = call_user_func($generatorFunc);
        }

        return $arr;
    }

    /**
     * Generates random object. It can be valid and invalid.
     * @param \PHPIntegration\Randomizable $obj Type of object to generate.
     * @param bool $valid Whether to generate valid or invalid object.
     * @return object
     */
    public static function randomObject(Randomizable $obj, bool $valid = true)
    {
        if ($valid === true) {
            return $obj->randomValid();
        } else {
            return $obj->randomInvalid();
        }
    }

    /**
     * Generates random date.
     * @param string Lower bound (inclusive).
     * @param string Upper bound (inclusive).
     * @return string
     */
    public static function randomDate($lowerBound = '0001-01-01', $upperBound = '9999-12-31') : string
    {
        $format = 'Y-m-d';
        $lowerBound = \DateTime::createFromFormat($format, $lowerBound);
        $upperBound = \DateTime::createFromFormat($format, $upperBound);

        if (!$lowerBound || !$upperBound) {
            throw new \Exception('Invalid date format');
        }

        $randTimestamp = mt_rand($lowerBound->getTimestamp(), $upperBound->getTimestamp());

        $randomDate = new \DateTime();
        $randomDate->setTimestamp($randTimestamp);

        return $randomDate->format($format);
    }

    /**
     * Function generates random multidimensional array. It will contain values
     * that will be returned by function $valueBuilder and keys that will be
     * returned by $keyBuilder. Each of them takes an array with a path as
     * parameter.
     * @param $keyBuilder Function that takes path as a parameter and returns new key.
     * @param $valueBuilder Function that takes path as a parameter and returns new value..
     * @return array
     */
    public static function randomArrayOf(
        $keyBuilder,
        $valueBuilder,
        int $maxItems = 10,
        int $maxDepth = 5
    ) : array {
        $p = [];
        return self::generateRandomArrayOf(
            $keyBuilder,
            $valueBuilder,
            $p,
            abs($maxItems),
            abs($maxDepth)
        );
    }

    private static function generateRandomArrayOf(
        $keyBuilder,
        $valueBuilder,
        array &$currentPath,
        int $maxItems,
        int $maxDepth
    ) : array {
        $result = [];

        $items = rand(1, $maxItems);
        for ($i = 0; $i < $items; $i++) {
            $key = call_user_func($keyBuilder, $currentPath);
            $path = array_merge($currentPath, [$key]);

            if ($maxDepth > 1 && rand(0, 1) == 1) {
                $value = self::generateRandomArrayOf(
                    $keyBuilder,
                    $valueBuilder,
                    $path,
                    $maxItems,
                    $maxDepth - 1
                );
            } else {
                $value = call_user_func($valueBuilder, $path);
            }
            $result[$key] = $value;
        }

        return $result;
    }
}
