<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader;
use Illuminate\Http\Request;
use App\Jobs\ImportQueue;
use App\ImportData;
use App\ImportFile;
use App\Product;


class ImportController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    protected function excel2import() {
        $arrImport = array();

        $arrAllFiles = Storage::files();

        $arrAllXlsxFiles = array_filter(
                    $arrAllFiles,
                    function($fileName) {
                        $arrFileName = explode('.', $fileName);
                        return strtolower($arrFileName[1]) == 'xlsx';
                    }
                );

        if ($arrAllXlsxFiles) {
            foreach ($arrAllXlsxFiles as $xlsxFile) {

                $path = storage_path('app') . '/' . $xlsxFile;

                $category_id = null;
                $arrImport = [];

                /**  Identify the type of $inputFileName  **/
                $inputFileType = IOFactory::identify($path);

                /**  Create a new Reader of the type that has been identified  **/
                $reader = IOFactory::createReader($inputFileType);

                $reader->setReadDataOnly(true);

                try {
                    /**  Load $inputFileName to a Spreadsheet Object  **/
                    $spreadsheet = $reader->load($path);

                    $worksheet = $spreadsheet->getActiveSheet();

                    // Get the highest row number and column letter referenced in the worksheet
                    $highestRow = $worksheet->getHighestRow(); // e.g. 10

                    $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'

                    // Increment the highest column letter
                    $highestColumn++;

                    for ($row = 1; $row <= $highestRow; ++$row) {

                        $id = $name = $free_shipping = $description = $price = null;

                        for ($col = 'A'; $col != $highestColumn; ++$col) {
                            $value = $worksheet->getCell($col . $row)->getValue();

                            // Category
                            if ($col == 'B' && $row == 1) {
                                $category_id = $value;
                            }
                            // Import Data
                            elseif ($row > 3) {
                                switch ($col) {
                                    case 'A':
                                        $id = $value;
                                        break;
                                    case 'B':
                                        $name = $value;
                                        break;
                                    case 'C':
                                        $free_shipping = $value;
                                        break;
                                    case 'D':
                                        $description = $value;
                                        break;
                                    case 'E':
                                        $price = $value;
                                        break;
                                }
                            }
                        }

                        if (!empty($id)) {
                            $arrImport[] = [
                                'id' => $id, 
                                'name' => $name, 
                                'free_shipping' => $free_shipping,
                                'description' => $description, 
                                'price' => $price, 
                                'category_id' => $category_id, 
                                'import_file_id' => null,
                            ]; 
                        }
    
                    }
                }
                catch(Reader\Exception $e) {
                    die('Error loading file: '.$e->getMessage());
                }

                if (!empty($arrImport)) {
                    $importFile = new ImportFile(['filename' => $xlsxFile]);
                    $importFile->save();

                    for ($i=0; $i < count($arrImport); $i++) {
                        $arrImport[$i]['import_file_id'] = $importFile->id;
                    }
                
                    if (ImportData::insert($arrImport)) {
                        $importFile->status = true;
                        $importFile->save();
                    }
                    else {
                        echo "Erro no processo de transferência da planilha para base de dados!<br />";
                    }
                }
                else {
                    echo "Erro no formato do arquivo!<br />";
                }
            }
        }
        else {
            echo "Não há novos arquivos para importação!<br />";
        }
    }

    protected function import2product() {
        $import = ImportData::all()->unique();
        $import = json_decode($import, true);
        $arrRows = $arrCols = array();

        foreach ($import as $index) {
            foreach ($index as $key => $value) {
                $arrCols[$key] = $value;   
            }

            if (!Product::find($arrCols['id'])) {
                $product = new Product($arrCols);
                $product->save();
            }
        }
    }

    public function index() {
        $objImportFile = ImportFile::all();

        if (count($objImportFile) > 0) {
            echo '<table border="1">';
            echo '<tr>';
            echo '<th>Arquivo</th>';
            echo '<th>Status</th>';
            echo '<th>Data/Hora</th>';
            echo '</tr>';
            foreach ($objImportFile as $importFile) {
                $status = $importFile->status ? 'OK' : 'NOK';
                echo '<tr>';
                echo '<td>' . $importFile->filename . '</td>';
                echo '<td align="center">' . $status . '</td>';
                echo '<td align="center">' . $importFile->updated_at . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        
        }
        else {
            echo '<p>Nenhum arquivo foi importado!</p>';
        }
    }

    public function importall() {
        $this->excel2import();
        $this->import2product();
    }

    public function execqueue() {
        $job = (new ImportQueue())->delay(5);
        $this->dispatch($job);
    }    
}
