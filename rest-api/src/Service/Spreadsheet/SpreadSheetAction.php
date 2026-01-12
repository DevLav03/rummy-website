<?php

namespace App\Action\Spreadsheet;

use App\Renderer\JsonRenderer;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

//service
use App\Domain\Admin_Panel\Users\Service\UsersService;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

final class SpreadSheetAction
{
    private UsersService $service;
    private JsonRenderer $renderer;

    public function __construct(UsersService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    

    public function getSpreadsheet(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {

        $excel = new Spreadsheet();
        
        $sheet = $excel->setActiveSheetIndex(0);

        $users = $this->service->getUsers();
        $arr = (array) $users;
    
        $row_count= count($arr['users']);
        // print_r($arr['users']); exit;

        $final_excel_array=array();
        foreach($arr_user  as $key => $val){
            array_push($individual_array,$val);
        }
        for($i=0;$i<$row_count;$i++){
            $individual_array=array();
            $arr_user=(array)$arr['users'][$i];
            // print_r($arr_user); exit;
            if($i==0){
                foreach($arr_user  as $key => $val){
                    array_push($individual_array,strtoupper($key));
                }
                array_push($final_excel_array,$individual_array);
            }
            //print_r( $arr_user);exit;
            $individual_array=array();
            foreach($arr_user  as $key => $val){
                array_push($individual_array, $val);
            }
            array_push($final_excel_array,$individual_array);
           
        }
        //print_r($final_excel_array);
        //exit;

       

        // $database = [
        //     [ 'Tree',  'Height', 'Age', 'Yield', 'Profit' ],
        //     [ 'Apple',  18,       20,    14,      105.00  ],
        //     [ 'Pear',   12,       12,    10,       96.00  ],
        //     [ 'Cherry', 13,       14,     9,      105.00  ],
        //     [ 'Apple',  14,       15,    10,       75.00  ],
        //     [ 'Pear',    9,        8,     8,       76.80  ],
        //     [ 'Apple',   8,        9,     6,       45.00  ],
        // ];
       /* 
        $criteria = [
            [ 'Tree',      'Height', 'Age', 'Yield', 'Profit', 'Height' ],
            [ '="=Apple"', '>10',    NULL,  NULL,    NULL,     '<16'    ],
            [ '="=Pear"',  NULL,     NULL,  NULL,    NULL,     NULL     ],
        ];  */
        
        $len=$row_count+1;
        $cell_num='A'.$len;
        //$sheet->fromArray( $database, NULL, $cell_num );
        
        $sheet->fromArray($final_excel_array, NULL, 'A1');
        // $cell = $sheet->getCell('A1');
        // $cell->setValue('Test');
        // $sheet->setSelectedCells('A1');
        // $excel->setActiveSheetIndex(0);

        $excelWriter = new Xlsx($excel);

        // We have to create a real temp file here because the
        // save() method doesn't support in-memory streams.
        $tempFile = tempnam(File::sysGetTempDir(), 'phpxltmp');
        $tempFile = $tempFile ?: __DIR__ . '/temp.xlsx';
        $excelWriter->save($tempFile);

        // For Excel2007 and above .xlsx files   
        $response = $response->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response = $response->withHeader('Content-Disposition', 'attachment; filename="file.xlsx"');

        // $stream = fopen($tempFile, 'r+');

        // return $response->withBody(new \Slim\Http\Stream($stream));

        $stream = fopen($tempFile, 'r+');

        $response->getBody()->write(fread($stream, (int)fstat($stream)['size']));

        return $response;

    }

}

    