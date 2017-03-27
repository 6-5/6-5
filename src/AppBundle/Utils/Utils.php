<?php

namespace AppBundle\Utils;

class Utils
{
    public static function getConstants($class, $startsWith)
    {
        $reflection = new \ReflectionClass($class);
        $filter = function ($value) use (&$startsWith) {
            return false !== strpos($value, $startsWith);
        };
        return array_filter($reflection->getConstants(), $filter, ARRAY_FILTER_USE_KEY);
    }
}
