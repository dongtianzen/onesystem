<?php

/**
 * @file
 * Contains Drupal\htmlasset\Service\HtmlassetMeetingService.php.
 */
namespace Drupal\htmlasset\Service;

use Drupal\Component\Utility\Unicode;


/**
 * An example Service container.
 * \Drupal::service('htmlasset.meeting.service')->demo();
 */
class HtmlassetMeetingService {

  public function demo() {

  }

  /**
   * @todo
   * Total 9 elements for meeting header
   */
  public function blockTileMeetingHeaderValue($meeting_node = NULL) {
    $speaker_names_array = \Drupal::service('flexinfo.field.service')
      ->getFieldAllTargetIdsUserNames($meeting_node, 'field_meeting_speaker');
    $speaker_names = implode(", ", $speaker_names_array);
    $speaker_names = Unicode::truncate($speaker_names, 300, $wordsafe = TRUE, $add_ellipsis = TRUE);

    $output = array(
      array(
        'value' => t('Date'),
        'value_one' => \Drupal::service('flexinfo.field.service')
          ->getFieldFirstValueDateFormat($meeting_node, 'field_meeting_date'),
      ),
      array(
        'value' => t('City'),
        'value_one' => \Drupal::service('flexinfo.field.service')
          ->getFieldFirstTargetIdTermName($meeting_node, 'field_meeting_city'),
      ),
      array(
        'value' => t('Venue'),
        'value_one' => \Drupal::service('flexinfo.field.service')
          ->getFieldFirstValue($meeting_node, 'field_meeting_venuename'),
      ),
      array(
        'value' => t('Evaluation Received'),
        'value_one' => \Drupal::service('flexinfo.field.service')
          ->getFieldFirstValue($meeting_node, 'field_meeting_evaluationnum'),
      ),
      array(
        'value' => t('Moderator'),
        'value_one' => \Drupal::service('flexinfo.field.service')
          ->getFieldFirstTargetIdUserName($meeting_node, 'field_meeting_moderator'),
      ),
      array(
        'value' => t('Participants'),
        'value_one' => \Drupal::service('flexinfo.field.service')
          ->getFieldFirstValue($meeting_node, 'field_meeting_signature'),
      ),
      array(
        'value' => '%' . t('Watched') . ' 75%',
        'value_one' => \Drupal::service('flexinfo.field.service')
          ->getFieldFirstValue($meeting_node, 'field_meeting_viewed75') . '%',
      ),
      array(
        'value' => '%' . t('Watched') . ' 100%',
        'value_one' => \Drupal::service('flexinfo.field.service')
          ->getFieldFirstValue($meeting_node, 'field_meeting_viewed100') . '%',
      ),
      array(
        'value' => t('Speaker'),
        'value_one' => $speaker_names,
        'pdf_col_length' => 192,
      ),
    );

    return $output;
  }

}
