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

$result1 = Objectron::toObject($arr, 'id', ' %id%, Student Class=%class%, Student Name => %name% ,Student group=>%id%');

$end = microtime(true);
$val = $end - $start;

print_r($result1);

echo "Done in $val ms";