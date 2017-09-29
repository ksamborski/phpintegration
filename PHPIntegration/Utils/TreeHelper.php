<?php

namespace PHPIntegration\Utils;

/**
 * Bunch of useful function when working with arrays as trees.
 * A tree is a structure that could be either a value (a leaf) or an array of trees (a forest).
 * For example:
 * [
 *      "leaf1",
 *      "forest name" => [
 *           "innerLeaf1"
 *      ],
 *      "leaf2"
 * ]
 */
class TreeHelper
{
    /**
     * Function generates path to random leaf in the tree with the leaf itself.
     * @param array $arr Tree to generate path from.
     * @return array Path to random leaf.
     */
    public static function randomPath(array $arr) : array
    {
        if (empty($arr)) {
            return [];
        }

        $k = array_rand($arr);
        $v = $arr[$k];
        $path = [];
        $path[] = is_array($v) ? $k : $v;

        while (is_array($v)) {
            $k = array_rand($v);
            $path[] = is_array($v[$k]) ? $k : $v[$k];
            $v = $v[$k];
        }

        return $path;
    }

    /**
     * Function generates all possible paths to the leafs in the tree (with the
     * leafs themselves).
     * @param array $arr Tree to generate paths from.
     * @return array Array of all possible paths (arrays).
     */
    public static function allPaths(array $arr) : array
    {
        if (empty($arr)) {
            return [];
        }

        $paths = [];

        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $path = array_map(
                    function ($vv) use ($k) {
                        $newv = $vv;
                        array_unshift($newv, $k);
                        return $newv;
                    },
                    self::allPaths($v)
                );

                if (!empty($path)) {
                    $paths = array_merge($paths, $path);
                }
            } else {
                $paths[] = [$v];
            }
        }

        return $paths;
    }
}
