<?php

namespace Core\Helpers;

class Transformer
{
    /**
     * @param array $data
     * @return array
     */
    public static function multidimensionalToSingleArray(array $data)
    {
        $result = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, self::multidimensionalToSingleArray($value));
            } else {
                $result[$key] = $value;
            }
        }

        return $result;

//        array_walk_recursive($data, function($item, $value) {
//            $result[$item] = $value;} );

//        return iterator_to_array(
//            new \RecursiveIteratorIterator(new \RecursiveArrayIterator($data)));
    }
}