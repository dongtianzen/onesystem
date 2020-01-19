<?php

namespace Drupal\ngdata\Atomic\Page;

use Drupal\Core\Url;

use Drupal\ngdata\Atomic\NgdataAtomic;

use Drupal\ngjson\Content\EventStandardLayoutContent;

/**
 * Class NgdataAtomicPage.
  \Drupal::service('ngdata.atomic.page')->demo();
 */
class NgdataAtomicPage extends NgdataAtomic {

  private $atom;
  private $molecule;
  private $organism;
  private $template;
  private $block;
  private $blockgroup;

  /**
   * Constructs a new NgdataAtomicPage object.
   */
  public function __construct() {
    $this->atom     = \Drupal::service('ngdata.atomic.atom');
    $this->molecule = \Drupal::service('ngdata.atomic.molecule');
    $this->organism = \Drupal::service('ngdata.atomic.organism');
    $this->template = \Drupal::service('ngdata.atomic.template');
    $this->block    = \Drupal::service('ngdata.atomic.block');
    $this->blockgroup = \Drupal::service('ngdata.atomic.blockgroup');
  }

  /**
   *
   */
  public function meetingPageContent($section, $entity_id, $start, $end) {
    $meeting_entity = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->load($entity_id);

    $evaluationform_tid = \Drupal::getContainer()
      ->get('flexinfo.node.service')
      ->getMeetingEvaluationformTid($meeting_entity);

    $EventStandardLayoutContent = new EventStandardLayoutContent();
    $MeetingQuestionBlockGroup = $EventStandardLayoutContent->blockEventsSnapshot(array($meeting_entity), $evaluationform_tid, 'meeting_view');

    $output = [];
    $output[] = $this->template->blockHtmlTileMeetingHeader($meeting_entity);
    $output[] = $this->template->blockHtmlClearBoth();
    $output = array_merge($output, $MeetingQuestionBlockGroup);

    return $output;
  }

  /**
   *
   */
  public function programPageContent($meeting_nodes, $entity_id, $start, $end) {
    $output = [];
    $output[] = $this->template->blockHtmlProgramNameHeader($entity_id);
    $output = array_merge($output, $this->organism->tileSectionGroup($meeting_nodes, FALSE));
    $output[] = $this->template->blockHtmlClearBoth();
    $output = array_merge($output, $this->blockgroup->blockGroupForProgramSnapshot($entity_id, $meeting_nodes));

    return $output;
  }

  /**
   *
   */
  public function eventlistPageContent($meeting_nodes, $entity_id, $start, $end, $color_box_palette = FALSE, $bg_color_class = 'bg-0f69af', $table_data_template_name = 'tableDataByEventList') {
    $output = $this->organism->tileSectionGroup($meeting_nodes);
    $output[] = $this->template->blockTableTemplate("View Events", $this->organism->tableContentEventList($meeting_nodes, $table_data_template_name), $color_box_palette, $bg_color_class);

    return $output;
  }

  /**
   *
   */
  public function programlistPageContent($meeting_nodes, $entity_id, $start, $end, $color_box_palette = FALSE, $bg_color_class = 'bg-0f69af', $table_data_template_name = 'tableDataByTopProgram') {
    $output = $this->organism->tileSectionGroup($meeting_nodes);
    $output[] = $this->template->blockTableTemplate("View Programs", $this->organism->tableContentProgramList($meeting_nodes, $table_data_template_name), $color_box_palette, $bg_color_class);

    return $output;
  }

  /**
   *
   */
  public function speakerlistPageContent($meeting_nodes, $entity_id, $start, $end, $color_box_palette = FALSE, $bg_color_class = 'bg-0f69af', $limit_row = NULL, $question_tid = NULL, $table_data_template_name = 'tableDataByTopSpeaker') {
    $output = [];
    $output[] = $this->template->blockTableTemplate(
      "Speaker List",
      $this->organism->tableContentSpeakerList(
        $meeting_nodes, $limit_row, $question_tid, $table_data_template_name
      ),
      $color_box_palette,
      $bg_color_class
    );

    return $output;
  }

  /**
   *
   */
  public function questionlistPageContent($meeting_nodes, $entity_id, $start, $end, $color_box_palette = FALSE, $bg_color_class = 'bg-0f69af', $table_data_template_name = 'tableDataByTermQuestion') {
    $output = [];
    $output[] = $this->template->blockTableTemplate(
      "Question",
      $this->organism->tableContentQuestionList($meeting_nodes, $table_data_template_name),
      $color_box_palette,
      $bg_color_class
    );

    return $output;
  }

  /**
   *
   */
  public function standardnodePageContent($entity_id, $start, $end) {
    $output = [];
    // $output[] = 'Add new node link';
    $output[] = $this->template->blockTableTemplate($entity_id, $this->organism->tableContentStandardnode($entity_id, $start, $end));

    return $output;
  }

  /**
   *
   */
  public function customNodeMeetingPageContent($entity_id, $start, $end) {
    $output = [];

    $create_new_meeting_link = \Drupal::l(
      'Add New Meeting',
      Url::fromUserInput('/node/add/meeting')
    );

    $svg = '<span style="width:28px; float:left; margin-right:4px;">
      <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="plus-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-plus-circle fa-w-16 fa-lg"><path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm144 276c0 6.6-5.4 12-12 12h-92v92c0 6.6-5.4 12-12 12h-56c-6.6 0-12-5.4-12-12v-92h-92c-6.6 0-12-5.4-12-12v-56c0-6.6 5.4-12 12-12h92v-92c0-6.6 5.4-12 12-12h56c6.6 0 12 5.4 12 12v92h92c6.6 0 12 5.4 12 12v56z" class=""></path></svg>
    </span>';

    $output[] = $this->block->getBlockHtmlSnippet($svg . $create_new_meeting_link);
    $output[] = $this->template->blockTableTemplate(ucfirst($entity_id), $this->organism->tableContentCustomNodeMeeting($entity_id, $start, $end));

    return $output;
  }

  /**
   *
   */
  public function customTermProgramPageContent($entity_id, $start, $end) {
    $output = [];

    $create_new_term_link = \Drupal::l(
      'Add New Program',
      Url::fromUserInput('/admin/structure/taxonomy/manage/program/add')
    );

    $svg = '<span style="width:28px; float:left; margin-right:4px;">
      <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="plus-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-plus-circle fa-w-16 fa-lg"><path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm144 276c0 6.6-5.4 12-12 12h-92v92c0 6.6-5.4 12-12 12h-56c-6.6 0-12-5.4-12-12v-92h-92c-6.6 0-12-5.4-12-12v-56c0-6.6 5.4-12 12-12h92v-92c0-6.6 5.4-12 12-12h56c6.6 0 12 5.4 12 12v92h92c6.6 0 12 5.4 12 12v56z" class=""></path></svg>
    </span>';

    $output[] = $this->block->getBlockHtmlSnippet($svg . $create_new_term_link);
    $output[] = $this->template->blockTableTemplate('Program', $this->organism->tableContentCustomTermProgram());

    return $output;
  }

  /**
   *
   */
  public function customTermQuestionPageContent($entity_id, $start, $end) {
    $output = [];

    $create_new_term_link = \Drupal::l(
      'Add New Question',
      Url::fromUserInput('/admin/structure/taxonomy/manage/questionlibrary/add')
    );

    $svg = '<span style="width:28px; float:left; margin-right:4px;">
      <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="plus-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-plus-circle fa-w-16 fa-lg"><path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm144 276c0 6.6-5.4 12-12 12h-92v92c0 6.6-5.4 12-12 12h-56c-6.6 0-12-5.4-12-12v-92h-92c-6.6 0-12-5.4-12-12v-56c0-6.6 5.4-12 12-12h92v-92c0-6.6 5.4-12 12-12h56c6.6 0 12 5.4 12 12v92h92c6.6 0 12 5.4 12 12v56z" class=""></path></svg>
    </span>';

    $output[] = $this->block->getBlockHtmlSnippet($svg . $create_new_term_link);
    $output[] = $this->template->blockTableTemplate('Question', $this->organism->tableContentCustomTermQuestion());

    return $output;
  }

  /**
   *
   */
  public function customTermEvaluationFormPageContent($entity_id, $start, $end) {
    $output = [];

    $create_new_term_link = \Drupal::l(
      'Add New Evaluation Form',
      Url::fromUserInput('/admin/structure/taxonomy/manage/evaluationform/add')
    );

    $svg = '<span style="width:28px; float:left; margin-right:4px;">
      <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="plus-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-plus-circle fa-w-16 fa-lg"><path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm144 276c0 6.6-5.4 12-12 12h-92v92c0 6.6-5.4 12-12 12h-56c-6.6 0-12-5.4-12-12v-92h-92c-6.6 0-12-5.4-12-12v-56c0-6.6 5.4-12 12-12h92v-92c0-6.6 5.4-12 12-12h56c6.6 0 12 5.4 12 12v92h92c6.6 0 12 5.4 12 12v56z" class=""></path></svg>
    </span>';

    $output[] = $this->block->getBlockHtmlSnippet($svg . $create_new_term_link);
    $output[] = $this->template->blockTableTemplate('Evaluation Form', $this->organism->tableContentCustomTermEvaluationForm());

    return $output;
  }

  /**
   *
   */
  public function customTermBusinessunitPageContent($entity_id, $start, $end) {
    $output = [];

    $create_new_term_link = \Drupal::l(
      'Add New Business Unit',
      Url::fromUserInput('/admin/structure/taxonomy/manage/businessunit/add')
    );

    $svg = '<span style="width:28px; float:left; margin-right:4px;">
      <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="plus-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-plus-circle fa-w-16 fa-lg"><path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm144 276c0 6.6-5.4 12-12 12h-92v92c0 6.6-5.4 12-12 12h-56c-6.6 0-12-5.4-12-12v-92h-92c-6.6 0-12-5.4-12-12v-56c0-6.6 5.4-12 12-12h92v-92c0-6.6 5.4-12 12-12h56c6.6 0 12 5.4 12 12v92h92c6.6 0 12 5.4 12 12v56z" class=""></path></svg>
    </span>';

    $output[] = $this->block->getBlockHtmlSnippet($svg . $create_new_term_link);
    $output[] = $this->template->blockTableTemplate('Business Unit', $this->organism->tableContentCustomTermBusinessunit());

    return $output;
  }

  /**
   *
   */
  public function customTermTherapeuticareaPageContent($entity_id, $start, $end) {
    $output = [];

    $create_new_term_link = \Drupal::l(
      'Add New Therapeutic Area',
      Url::fromUserInput('/admin/structure/taxonomy/manage/therapeuticarea/add')
    );

    $svg = '<span style="width:28px; float:left; margin-right:4px;">
      <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="plus-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-plus-circle fa-w-16 fa-lg"><path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm144 276c0 6.6-5.4 12-12 12h-92v92c0 6.6-5.4 12-12 12h-56c-6.6 0-12-5.4-12-12v-92h-92c-6.6 0-12-5.4-12-12v-56c0-6.6 5.4-12 12-12h92v-92c0-6.6 5.4-12 12-12h56c6.6 0 12 5.4 12 12v92h92c6.6 0 12 5.4 12 12v56z" class=""></path></svg>
    </span>';

    $output[] = $this->block->getBlockHtmlSnippet($svg . $create_new_term_link);
    $output[] = $this->template->blockTableTemplate('Therapeutic Area', $this->organism->tableContentCustomTermTherapeuticarea());

    return $output;
  }

  /**
   *
   */
  public function customTermQuestionEvaluationFormPageContent($entity_id, $start, $end) {
    $output = [];

    // $create_new_term_link = \Drupal::l(
    //   'Add New Question / Evaluation',
    //   Url::fromUserInput('/admin/structure/taxonomy/manage/evaluationform/add')
    // );

    // $svg = '<span style="width:28px; float:left; margin-right:4px;">
    //   <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="plus-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-plus-circle fa-w-16 fa-lg"><path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm144 276c0 6.6-5.4 12-12 12h-92v92c0 6.6-5.4 12-12 12h-56c-6.6 0-12-5.4-12-12v-92h-92c-6.6 0-12-5.4-12-12v-56c0-6.6 5.4-12 12-12h92v-92c0-6.6 5.4-12 12-12h56c6.6 0 12 5.4 12 12v92h92c6.6 0 12 5.4 12 12v56z" class=""></path></svg>
    // </span>';

    // $output[] = $this->block->getBlockHtmlSnippet($svg . $create_new_term_link);
    $output[] = $this->template->blockTableTemplate('Question / Evaluation', $this->organism->tableContentCustomTermQuestionEvaluationForm());

    return $output;
  }

  /**
   *
   */
  public function standardtermPageContent($entity_id, $start, $end) {
    $output = [];

    $vocabulary_entity = taxonomy_vocabulary_load($entity_id);

    if ($vocabulary_entity && $vocabulary_entity->getDescription()) {
      $headerText = $vocabulary_entity->getDescription();
    }
    else {
      $headerText = $entity_id;
    }

    $create_new_term_link = \Drupal::l(
      'Add New ' . ucfirst($headerText),
      Url::fromUserInput('/admin/structure/taxonomy/manage/' . $entity_id . '/add')
    );

    $svg = '<span style="width:28px; float:left; margin-right:4px;">
      <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="plus-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-plus-circle fa-w-16 fa-lg"><path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm144 276c0 6.6-5.4 12-12 12h-92v92c0 6.6-5.4 12-12 12h-56c-6.6 0-12-5.4-12-12v-92h-92c-6.6 0-12-5.4-12-12v-56c0-6.6 5.4-12 12-12h92v-92c0-6.6 5.4-12 12-12h56c6.6 0 12 5.4 12 12v92h92c6.6 0 12 5.4 12 12v56z" class=""></path></svg>
    </span>';

    $output[] = $this->block->getBlockHtmlSnippet($svg . $create_new_term_link);
    $output[] = $this->template->blockTableTemplate(ucfirst($headerText), $this->organism->tableContentStandardterm($entity_id, $start, $end));

    return $output;
  }

  /**
   *
   */
  public function standarduserPageContent($entity_id, $start, $end) {
    $output = [];

    $create_new_term_link = \Drupal::l(
      'Add New User',
      Url::fromUserInput('/admin/people/create')
    );

    $svg = '<span style="width:28px; float:left; margin-right:4px;">
      <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="plus-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-plus-circle fa-w-16 fa-lg"><path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm144 276c0 6.6-5.4 12-12 12h-92v92c0 6.6-5.4 12-12 12h-56c-6.6 0-12-5.4-12-12v-92h-92c-6.6 0-12-5.4-12-12v-56c0-6.6 5.4-12 12-12h92v-92c0-6.6 5.4-12 12-12h56c6.6 0 12 5.4 12 12v92h92c6.6 0 12 5.4 12 12v56z" class=""></path></svg>
    </span>';

    $output[] = $this->block->getBlockHtmlSnippet($svg .$create_new_term_link);
    $output[] = $this->template->blockTableTemplate(ucfirst($entity_id), $this->organism->tableContentCustomUserForAdminSection());

    return $output;
  }

}
