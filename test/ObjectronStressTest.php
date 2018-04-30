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

for ($i = 0; $i < 1000; $i++) {
    Objectron::toObject($arr, 'id', '%id%, Student Class=%class%, Student Name => %name% ,Student group=>%id%');
}

$end = microtime(true);
$val = $end - $start;

echo "Objectron Done in $val ms" . "\n";

$start = microtime(true);

for ($i = 0; $i < 1000; $i++) {
    $myarr = new stdClass();
    foreach ($arr as $myarrid => $myarrval) {
        $myarr->{$myarrval['id']} = new stdClass();
        $myarr->{$myarrval['id']}->{0} = $myarrval['id'];
        $myarr->{$myarrval['id']}->{'Student Class'} = $myarrval['class'];
        $myarr->{$myarrval['id']}->{'Student Name'}  = $myarrval['name'];
        $myarr->{$myarrval['id']}->{'Student group'} = $myarrval['id'];
    }
}

$end = microtime(true);
$val = $end - $start;

echo "Clone Done in $val ms" . "\n";

