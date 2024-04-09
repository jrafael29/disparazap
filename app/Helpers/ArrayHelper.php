<?php

namespace App\Helpers;

class ArrayHelper
{

    public static function divideArrayItems($items, $itemsPerBatch = 50)
    {
        $subArrays = [];
        $count = 0;
        $totalItems = count($items);
        for ($i = 0; $i < $totalItems; $i += $itemsPerBatch) {
            $subArrays[] = array_slice($items, $i + 1, $itemsPerBatch);
        }
        // dd($subArrays);
        return $subArrays;
    }
}
