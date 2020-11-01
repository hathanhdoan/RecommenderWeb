<?php
function normalizing($owner_row = null){
    $array_filter = array_filter($owner_row,function($rating){
        return $rating != '-';
    });

    $avg = array_sum($array_filter)/count($array_filter);
    $avg = round($avg,2);
    $owner_row = array_map(function($rating) use($avg){
        return ($rating == '-') ? '-' : ($rating - $avg);
    }, $owner_row);

    return $owner_row;

}
function echo_now($s)
{
    echo $s . PHP_EOL;

    flush();
}
