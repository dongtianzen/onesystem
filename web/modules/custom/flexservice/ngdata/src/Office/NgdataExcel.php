<?php

namespace Drupal\ngdata\Office;

/**
 * @see "phpoffice/phpspreadsheet"
 */
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 *
  \Drupal::service('ngdata.office.excel')->basic();
 */
class NgdataExcel {

  /**
   * Constructs a new NgdataChartChartjs object.
   */
  public function __construct() {

  }

  /**
   * flip the rows and columns of a 2D array
   */
  function flipArrayRowToColumn($content_array) {
    $output = array();
    foreach ($content_array as  $rowkey => $row) {
      foreach($row as $colkey => $col){
        $output[$colkey][$rowkey]=$col;
      }
    }
    return $output;
  }

  /**
   * @see "phpoffice/phpspreadsheet"
   */
  public function createPHPExcelObject($header_row, $content_array, $excel_file_name = 'excel', $sheet_name = 'sheet1') {
    $objPHPExcel = new Spreadsheet();

    // Set document properties
    $objPHPExcel->getProperties()->setCreator("Chris Dodd")
                ->setLastModifiedBy("Chris Dodd")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Program Report")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Program Report");

    // Adding Questions Title
    $objPHPExcel->getActiveSheet()
                ->fromArray($header_row, NULL, 'A1');

    // Adding Comments Answer
    $row = 2; // comments starting row
    $col = 1;
    foreach ($content_array as $key => $value) {
      foreach ($value as $key => $group) {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, strip_tags($group));
        $row++;
      }

      $col++;
      $row = 2;
    }

    // setting width
    foreach (range('A', 'Z') as $columnID) {
      $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
        ->setWidth(40);
    }

    // setting height
    foreach (range('1', '2000') as $rowID) {
      $objPHPExcel->getActiveSheet()->getRowDimension($rowID)->setRowHeight(40);
    }

    // font settings
    $header_font_styles = array(
      'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => '1f75bc'),
        'size' => 12,
        'name' => 'Calibri',
      )
    );
    $objPHPExcel->getActiveSheet()->getStyle('A1:Z1')->applyFromArray($header_font_styles);
    $objPHPExcel->getActiveSheet()->getStyle('A1:Z999')->getAlignment()->setWrapText(true);

    // Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle($sheet_name);

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename=" ' . $excel_file_name . '.xlsx"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP/1.1
    header("Cache-Control: post-check=0, pre-check=0", false);
    header('Pragma: public'); // HTTP/1.0
    header("Expires: 0");

    $objWriter = IOFactory::createWriter($objPHPExcel, 'Xlsx');

    // check first, otherwise it show error with no buffer to delete
    if (ob_get_contents()) {
      ob_end_clean();
    }

    $objWriter->save('php://output');

    exit;
  }

}
