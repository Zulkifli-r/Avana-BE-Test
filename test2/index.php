<?php
require __DIR__ . '/vendor/autoload.php';

use ZulkifliR\ExcelValidator;

$excelValidator = new ExcelValidator('./sample_file/Type_A.xlsx');
$excelValidator->validate('A', 0);
$excelValidator->displayOncli();
// $excelValidator->displayOnWeb();
