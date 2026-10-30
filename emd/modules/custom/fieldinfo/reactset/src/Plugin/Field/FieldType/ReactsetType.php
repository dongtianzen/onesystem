<?php

/**
 * @file
 * Contains \Drupal\reactset\Plugin\Field\FieldType\ReactsetType.
 */

namespace Drupal\reactset\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'reactset_type' field type.
 *
 * @FieldType(
 *   id = "reactset_type",
 *   label = @Translation("Reactset Type"),
 *   description = @Translation("This Compound field type stores Reactset answer Field in the database."),
 *   category = @Translation("ZenSet"),
 *   default_widget = "reactset_standard_widget",
 *   default_formatter = "reactset_default_format"
 * )
 */
class ReactsetType extends FieldItemBase {

  /**
   * {@inheritdoc}
   * @return an array using the same format as hook_schema() implementations.
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'question_tid' => array(
          'description' => 'The question library tid.',
          'type' => 'int',
          'not full' => TRUE,
          'default' => 0,
        ),
        'question_answer' => array(
          'description' => 'The question answer set.',
          'type' => 'varchar',
          'length' => 1024,
          'not full' => TRUE,
          'default' => '',
        ),
        'refer_uid' => array(
          'description' => 'The refer user uid.',
          'type' => 'int',
          'not full' => FALSE,
        ),
        'refer_tid' => array(
          'description' => 'The refer taxonomy term.',
          'type' => 'int',
          'not full' => FALSE,
        ),
        'refer_other' => array(
          'description' => 'The refer other.',
          'type' => 'varchar',
          'length' => 1024,
          'not full' => TRUE,
          'default' => '',
        ),
      ),
    );
  }

  /**
   * {@inheritdoc}
   * @todo set column property values such as label and description
   * @todo provide meta data for field properties
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['question_tid'] = DataDefinition::create('string')
      ->setLabel(t('Question Tid'))
      ->setDescription(t('Question Library Tid'))
      ->setRequired(FALSE);

    $properties['question_answer'] = DataDefinition::create('string')
      ->setLabel(t('Answer Set'))
      ->setDescription(t('Question Answer Set'));

    $properties['refer_uid'] = DataDefinition::create('string')
      ->setLabel(t('Refer User'))
      ->setDescription(t('Question Refer User'));

    $properties['refer_tid'] = DataDefinition::create('string')
      ->setLabel(t('Refer Term'))
      ->setDescription(t('Question Refer Term'));

    $properties['refer_other'] = DataDefinition::create('string')
      ->setLabel(t('Refer Other'))
      ->setDescription(t('Question Refer Other'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   * @todo determine whether the list contains any nonempty items
   * @todo Tell Drupal when a value should be considered empty
   * help Drupal understand whether a field item should be considered empty
   * If this method returns FALSE, Drupal knows that the field has some value which needs to be validated and saved
   */
  public function isEmpty() {
    $question_tid    = $this->get('question_tid')->getValue();
    $question_answer = $this->get('question_answer')->getValue();
    $refer_uid = $this->get('refer_uid')->getValue();
    $refer_tid = $this->get('refer_tid')->getValue();
    $refer_other = $this->get('refer_other')->getValue();

    // question_tid can't be empty
    return empty($question_tid) && empty($question_answer);
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();
    $constraints = parent::getConstraints();

    $constraints[] = $constraint_manager->create('ComplexData', array(
      'question_tid' => array(
        'Range' => array(
          'min' => 0,
          'minMessage' => t('%name: The Question Tid must be larger or equal to %min.', array(
            '%name' => $this->getFieldDefinition()->getLabel(),
            '%min' => 0,
          )),
        ),
      ),
    ));

    $max_length = 1024;
    $constraints[] = $constraint_manager->create('ComplexData', array(
      'question_answer' => array(
        'Length' => array(
          'max' => $max_length,
          'maxMessage' => t('%name: The Question Answer may not be longer than @max characters.', array('%name' => $this->getFieldDefinition()->getLabel(), '@max' => $max_length)),
        )
      ),
    ));

    // This value should be a valid number.
    // $constraints[] = $constraint_manager->create('ComplexData', array(
    //   'refer_uid' => array(
    //     'Range' => array(
    //       'min' => 0,
    //       'minMessage' => t('%name: The Refer User Uid must be larger or equal to %min.', array(
    //         '%name' => $this->getFieldDefinition()->getLabel(),
    //         '%min' => 0,
    //       )),
    //     ),
    //   ),
    // ));

    return $constraints;
  }

  /**
   * {@inheritdoc}
   * generateSampleValue(), Devel generate automatically use the values returned
   * by this method during the generate process for generate placeholder field values.
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $min = 0;
    $max = 999;
    $values['question_tid'] = mt_rand($min, $max);

    $random = new Random();
    $values['question_answer'] = $random->word(mt_rand(1, $field_definition->getSetting('max_length')));
    return $values;
  }

}
