<?php
require __DIR__ . '/vendor/autoload.php';

use ZulkifliR\ExcelValidator;

$excelValidator = new ExcelValidator('./sample_file/Type_C.xls');
// specify file type and sheet index
$excelValidator->validate('C', 0);
$excelValidator->displayOncli();
// $excelValidator->displayOnWeb();
