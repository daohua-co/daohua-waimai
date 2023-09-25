<?php

$s = microtime(1);
$n = 100000000;

$a = 0;
for ($i = 0; $i < $n; $i++) {
    $a += $i;
}

print microtime(1) - $s;
