<?php

/**
 * @file
 */

namespace Drupal\exportinfo\Content;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Drupal\flexpage\Content\FlexpageEventLayout;

/**
 * An example controller.
 */
class ExportinfoExcelGenerator {

  /**
   *
   */
  public function commentsQuestionTitles($meeting_comments) {
    $output = array();

    foreach ($meeting_comments as $key => $group) {
      if (isset($group['top'])) {
        array_push($output, $group['top']['value']);           // question name
      }
    }

    return $output;
  }

  /**
   *
   */
  public function commentsResultAnswers($meeting_comments) {
    $output = array();

    foreach ($meeting_comments as $key => $group) {
      if (isset($group['middle'])) {
        array_push($output, $group['middle']['value']);       // question comments
      }
    }

    return $output;
  }

  /**
   *
   */
  public function commentsAnswerConvert($comments_answers) {
    $output = array();

    foreach ($comments_answers as $key => $value) {
      $result = explode('</li>', $value);
      array_push($output, $result);
    }

    return $output;
  }

  /**
   * @see "phpoffice/phpspreadsheet"
   */
  public function createPHPExcelObject($comments_question_titles, $comments_answers, $entity_id) {
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
                ->fromArray(array($comments_question_titles), NULL, 'A1');

    // Adding Comments Answer
    $row = 2; // comments starting row
    $col = 1;
    foreach ($comments_answers as $key => $value) {
      foreach ($value as $key => $group) {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, strip_tags($group));
        $row++;
      }

      $col++;
      $row = 2;
    }

    //setting width
    foreach (range('A', 'Z') as $columnID) {
      $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
        ->setWidth(40);
    }

    //setting height
    foreach (range('1', '2000') as $rowID) {
      $objPHPExcel->getActiveSheet()->getRowDimension($rowID)->setRowHeight(40);
    }

    //font settings
    $styleArray = array(
      'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => '1f75bc'),
        'size' => 12,
        'name' => 'Calibri',
      )
    );
    $objPHPExcel->getActiveSheet()->getStyle('A1:P1')->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('A1:Z999')->getAlignment()->setWrapText(true);

    // Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle('ProgramReport');

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    $term_name = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getNameByTid($entity_id);

    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename=" ' . $term_name . '.xlsx"');
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

/**
 *
 */
class ExportinfoContentGenerator extends ExportinfoExcelGenerator {

  /**
   * single question
   * exportinfo/comments/question/{$question_tid}/{start}/{end}
   * exportinfo/comments/question/452/1546318800/1572572261
   */
  public function exportSingleQuestionComments($question_tid, $start_time, $end_time) {
    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('meeting');

    // condition date ranger
    if ($start_time && $end_time) {
      $group = $query_container->groupByMeetingTimestamp($query, $start_time, $end_time);
      $query->condition($group);
    }

    $meeting_nids = $query_container->runQueryWithGroup($query);
    $meeting_nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($meeting_nids);

    $FlexpageEventLayout = new FlexpageEventLayout();
    $meeting_comments = $FlexpageEventLayout
      ->getTextfieldQuestionComments(array($question_tid), $meeting_nodes);

    $this->generateCommentsForExcel($meeting_comments, $question_tid, $start_time, $end_time, 'Question');
  }

  /**
   * exportinfo/comments/program/2939/1486289600/1497153599
   * exportinfo/comments/therapeuticarea/5/1486289600/1497153599
   */
  public function exportVocabularyComments($vocabulary, $entity_id, $start_time, $end_time) {
    $ExportinfoCommentsGenerator = new ExportinfoCommentsGenerator();

    $program_tids = array();
    if ($vocabulary == 'program') {
      $program_tids = array($entity_id);
    }
    elseif ($vocabulary == 'therapeuticarea') {
      $program_tids = \Drupal::getContainer()
        ->get('flexinfo.queryterm.service')
        ->programTidsByTheraparea(array($entity_id));
    }


    $this->generateCommentsForExcel($meeting_comments, $entity_id, $start_time, $end_time);
  }

  /**
   * questions array
   */
  public function exportQuestionsCommentsByProgram($question_tids = array(), $program_tid = NULL, $start_time, $end_time) {
    $meeting_comments = $this->getMeetingCommentsByProgramTids($question_tid, array($program_tid), $start_time, $end_time);

    $this->generateCommentsForExcel($meeting_comments, $program_tid, $start_time, $end_time, 'Quetion');
  }

  /**
   * questions array
   */
  public function exportQuestionsCommentsByTheraparea($question_tids = array(), $theraparea_tid = NULL, $start_time, $end_time) {
    $program_tids = \Drupal::getContainer()
      ->get('flexinfo.queryterm.service')
      ->wrapperTermTidsByField('program', 'field_program_theraparea', $theraparea_tid);

    $meeting_comments = $this->getMeetingCommentsByProgramTids($question_tids, $program_tids, $start_time, $end_time);

    $this->generateCommentsForExcel($meeting_comments, $theraparea_tid, $start_time, $end_time, 'Quetion');
  }

  /**
   * questions array
   */
  public function getMeetingCommentsByProgramTids($question_tids = array(), $program_tids = array(), $start_time, $end_time) {
    $output = array();

    $ExportinfoCommentsGenerator = new ExportinfoCommentsGenerator();

    foreach ($question_tids as $question_tid) {
      $meeting_comment = $ExportinfoCommentsGenerator
        ->exportOneQuestionCommentsDataByProgramTids($question_tid, $program_tids, $start_time, $end_time);

      if ($meeting_comment && isset($meeting_comment[0])) {
        $output[] = $meeting_comment[0];
      }
    }

    return $output;
  }

  /**
   * questions array
   */
  public function generateCommentsForExcel($meeting_comments, $entity_id, $start_time, $end_time, $alert_keyword = 'Program') {
    if ($meeting_comments == 0) {
      $alert_text = "This " . $alert_keyword . " does not contain any comments. Please select another " . $alert_keyword;
      echo '<script type="text/javascript">
              alert("' . $alert_text . '");
              window.location.href= "' . base_path() . 'exportinfo/program/page/all/' . $start_time . '/' . $end_time . '";
            </script>';

      exit;
    }

    // collecting questions and comments
    $comments_question_titles = $this->commentsQuestionTitles($meeting_comments);
    $comments_answers_raw = $this->commentsResultAnswers($meeting_comments);
    $comments_answers = $this->commentsAnswerConvert($comments_answers_raw);

    $this->createPHPExcelObject($comments_question_titles, $comments_answers, $entity_id);
  }

}
