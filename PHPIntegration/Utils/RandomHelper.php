<?php

namespace PHPIntegration\Utils;

class RandomHelper
{
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

    public static function randomEmail() : string
    {
        $p1 = trim(self::randomString(rand(10, 50), '0123456789abcdefghijklmnopqrstuvwxyz-._'), '-._');
        $p2 = trim(self::randomString(rand(4, 8), '0123456789abcdefghijklmnopqrstuvwxyz-_'), '-_');
        $dot = trim(self::randomString(rand(4, 8), '0123456789abcdefghijklmnopqrstuvwxyz-_'), '-_');
        return $p1 . '@' . $p2 . '.' . $dot;
    }

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
