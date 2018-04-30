<?php
/**
 * Created by PhpStorm.
 * User: Ahmed Salah
 * Date: 4/14/2018
 * Time: 1:59 PM
 */


require_once '../src/Objectron.php';



$arr[] = ['id'=> 1, 'name'=>'john', 'class'=>10];
$arr[] = ['id'=> 5, 'name'=>'clam', 'class'=>4];
$arr[] = ['id'=> 8, 'name'=>'robot', 'class'=>1];

$start = microtime(true);

$matches = [];
$re = '%(?>(?P<variable>\%[A-Za-z0-9_\s]+\%))|(?>(?P<name>\b[A-Za-z0-9_\s]+\b))|(?P<equals>\={1}\>?)|(?P<delimiter>,)%i';
$str = ' %id%%, Student Class=%%class%%, Student Name => %%name%% ,Student group=>%%id%%';

//preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
preg_match_all($re, $str, $matches, PREG_PATTERN_ORDER, 0);


$end = microtime(true);
$val = $end - $start;

// Print the entire match result
var_dump($matches);

echo "Done in $val ms";
