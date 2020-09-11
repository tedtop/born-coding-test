<?php

// $a is assigned string value of '1'
$a = '1';

 // $b is created as an alias to the original data in $a which is '1'
$b = &$a;

 // "2$b" evaluates to '21' and assigned to $b which references and updates the value of $a
$b = "2$b";

// both $a and $b reference the value '21'
echo $a.",".$b;

// Output: 21,21