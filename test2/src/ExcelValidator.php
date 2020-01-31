<?php

namespace ZulkifliR;

use PhpOffice\PhpSpreadsheet\IOFactory;
use jc21\CliTable;


class ExcelValidator
{
    public $spreadsheet;
    private $sheet;
    private $extension;
    private $errors;
    private $config;
    private $header;

    public function __construct($files)
    {
        $this->extension = ucfirst(pathinfo($files, PATHINFO_EXTENSION));
        $reader = IOFactory::createReader($this->extension);
        $reader->setReadDataOnly(true);
        $this->spreadsheet = $reader->load($files);

        // $this->validate();
    }

    public function validate($file_type = 'A', $sheet = 0)
    {
        $this->config = file_type($file_type);
        $this->sheet = $this->spreadsheet->getSheet($sheet);

        if ($this->validateColumnSize($this->config['columns_size']) == false) {
            $this->errors[0][] = 'Maximum column size for Type ' . $file_type . ' is ' . $this->config['columns_size'];
            return false;
        }
        if ($this->validateHeader($this->config['columns_header']) == false) {
            $this->errors[0][] = 'Type ' . $file_type . ' header must follow the following name and order : ' . implode(', ', $this->config['columns_header']);
            return false;
        }

        foreach ($this->sheet->getRowIterator() as $key => $row) {

            $cellIterator = $row->getCellIterator();

            foreach ($cellIterator as $column => $cell) {
                $cellValue = $cell->getValue();
                if ($key == 1) {

                    if (substr($cellValue, 0, 1) == '#') {
                        $rule[$column] = 'no_space_allowed';
                    }

                    if (substr($cellValue, -1) == '*') {
                        $rule[$column] = 'required';
                    }
                } else {
                    $validatedValue = $this->validateValue($cellValue, $column, $rule[$column] ?? null);

                    if ($validatedValue && $validatedValue['status'] == false) {
                        $this->errors[$key][] = $validatedValue['message'];
                    }
                }
            }
        }
    }


    private function validateValue($value, $column, $rule = null)
    {
        if ($rule) {
            switch ($rule) {
                case 'required':
                    if (!$value) {
                        return ['status' => false, 'message' => 'Missing value in Field_' . $column];
                    }
                    return ['status' => true];
                    break;

                case 'no_space_allowed':
                    if (strpos($value, ' ')) {
                        return ['status' => false, 'message' => 'Field_' . $column . ' should not contain any space'];
                    }
                    return ['status' => true];
                    break;
            }
        }
    }

    private function validateColumnSize($maxWidth)
    {
        return $maxWidth >= \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($this->sheet->getHighestColumn());
    }

    private function validateHeader($headerOrder)
    {
        $this->setHeader();

        return $headerOrder == $this->header;
    }

    private function setHeader()
    {
        $row = $this->sheet->getRowIterator(1)->current();
        $cell = $row->getCellIterator();
        foreach ($cell as $key => $value) {
            $header[] = $value->getValue();
        }
        $this->header = $header;
    }

    public function displayOnWeb()
    {

        echo "<table border=1 cellspacing=0 cellpadding=0 style='border-collapse:collapse'>";
        echo "<tr style='background-color:grey;'>";
        echo "<td style='background-color:grey;padding:10px'>Row</td>";
        echo "<td>Erorr</td>";
        echo "</tr>";

        foreach ($this->errors as $key => $value) {
            echo "<tr>";
            echo "<td> " . $key . " </td>";
            echo "<td> " . implode(', ', $value) . " </td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    public function displayOncli()
    {
        $table = new CliTable();
        $table->setTableColor('blue');
        $table->setHeaderColor('cyan');
        $table->addField('Row', 'row',    false,                               'white');
        $table->addField('Error', 'errors',    false,                               'white');

        foreach ($this->errors as $key => $value) {
            $errors[$key]['row'] = $key;
            $errors[$key]['errors'] = implode(', ', $value);
        }
        $table->injectData($errors);
        $table->display();
    }
}
