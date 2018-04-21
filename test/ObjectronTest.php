<?php
/**
 * Created by PhpStorm.
 * User: Ahmed Salah
 * Date: 4/14/2018
 * Time: 1:59 PM
 */


require_once '../vendor/autoload.php';



$arr[] = ['id'=> 1, 'name'=>'john', 'class'=>10];
$arr[] = ['id'=> 5, 'name'=>'clam', 'class'=>4];
$arr[] = ['id'=> 8, 'name'=>'robot', 'class'=>1];

$start = microtime(true);

$obj1 = new Objectron($arr, 'id', '%%id%%, Student Class=%%class%%, Student Name => %%name%% ,Student group=>%%id%%');
$result1 = $obj1->toObject();

$obj2 = new Objectron($arr, 'name', '%%id%%, %%class%%, %%name%%');
$result2 = $obj2->toObject();

$obj3 = new Objectron($arr, 'id', '%%name%%');
$result3 = $obj3->toObject();

$obj4 = new Objectron($arr, 'id');
$result4 = $obj4->toObject();

$end = microtime(true);
$val = $end - $start;
echo "Done in $val ms";

print_r($result1);
print_r($result2);
print_r($result3);
print_r($result4);
