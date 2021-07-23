<?php

require 'R:\OSPanel\modules\php\PHP_7.3-x64\vendor\autoload.php';
/*
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Hello World !');

$writer = new Xlsx($spreadsheet);
$writer->save('hello world.xlsx');
*/

$q=\PhpOffice\PhpSpreadsheet\IOFactory::load('hello world.xlsx');
$w=$q->getActiveSheet();
//$e=$w->setCellValueByColumnAndRow(2,2,'test');
$r=$w->getCell('A1');
$r->setValue('test ok');
echo $r->getValue();

//echo $spreadsheet->getActiveSheet()->getCell('V14')->getCalculatedValue();

/*
$cellA1 = $workSheet->getCell('A1');
echo 'Value: ', $cellA1->getValue(), '; Address: ', $cellA1->getCoordinate(), PHP_EOL;
*/

