### Basic usage

```bash
require __DIR__ . '/vendor/autoload.php';

use ZulkifliR\ExcelValidator;

$excelValidator = new ExcelValidator('./sample_file/Type_A.xlsx');
# specify file type and sheet index
$excelValidator->validate('A', 0);

$excelValidator->displayOnCli();
```

### Add new file type

you can add file type by creating new file in **file_types** folder, it's just a regular array with 2 required keys :

- columns_size : Maximum column size,
- columns_header : Header name and order.
