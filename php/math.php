<?php

function mean(array $a, int $n = null){
    if ($n === null) {
        $n = count($a);
    }
    return array_sum($a) / $n;
}


function sd(array $a, int $n = null, $sample = false){
    if ($n === null) {
        $n = count($a);
    }
    if ($n === 0) {
        trigger_error("The array has zero elements", E_USER_WARNING);
        return false;
    }
    if ($sample && $n === 1) {
        trigger_error("The array has only 1 element", E_USER_WARNING);
        return false;
    }
    $mean = array_sum($a) / $n;
    $carry = 0.0;
    foreach ($a as $val) {
        $d = ((float) $val) - $mean;
        $carry += $d * $d;
    };
    if ($sample) {
        --$n;
    }
    return sqrt($carry / $n);
}


function se(array $a, int $n = null){
    if ($n === null) {
        $n = count($a);
    }
    $sd = sd($a);
    return $sd / sqrt($n - 1);
}

function re(array $a, int $n = null){
    if ($n === null) {
        $n = count($a);
    }
    $mean = mean($a, $n);
    if ($mean == 0) {
        return 0;
    }
    return se($a) / $mean * 100;
}