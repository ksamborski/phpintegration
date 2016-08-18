<?php

namespace PHPIntegration\Utils;

/**
 * Bunch of useful random generators.
 */
class RandomHelper
{
    /**
     * Generates rundom string.
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
        $p1 = trim(self::randomString(rand(10, 50), '0123456789abcdefghijklmnopqrstuvwxyz-._'), '-._');
        $p2 = trim(self::randomString(rand(4, 8), '0123456789abcdefghijklmnopqrstuvwxyz-_'), '-_');
        $dot = trim(self::randomString(rand(4, 8), '0123456789abcdefghijklmnopqrstuvwxyz-_'), '-_');
        return $p1 . '@' . $p2 . '.' . $dot;
    }

    /**
     * Picks one random value from provided array.
     * @param array $possibleValues Array of possible values
     * @return array Random item from the array
     */
    public static function randomOneOf(array $possibleValues)
    {
        return RandomHelper::randomArray($possibleValue, false, 1, 1)[0];
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
}
