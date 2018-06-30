<?php

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/debug/field_debug.php');
  _run_batch_entity_create_fields();
 */

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;

function _run_batch_entity_create_fields() {
  $entity_info = array(
    'entity_type' => 'node',  // 'node', 'taxonomy_term', 'user'
    'bundle' => 'day',
  );

  $fields = _entity_fields_info();
  foreach ($fields as $field) {
    _entity_create_fields_save($entity_info, $field);
  }
}

/**
 *
  field type list:
  decimal
  float
  integer
 */
function _entity_fields_info() {
  /** field sample */
  // $fields[] = array(
  //   'field_name' => 'field_day_close',
  //   'type'       => 'float',
  //   'label'      => t('Close'),
  // );

  /**
   * array sample
   */
  // $json_content_piece = array(
  //   "high" => 12.8,
  //   "low" => 11.07,
  //   "volume" => 1472901.6200000001,
  //   "price_change" => 0.4,
  //   "p_change" => 3.25,
  //   "ma5" => 12.7,
  //   "ma10" => 12.7,
  //   "ma20" => 12.7,
  //   "v_ma5" => 1472901.6200000001,
  //   "v_ma10" => 1472901.6200000001,
  //   "v_ma20" => 1472901.6200000001
  // );

  // foreach ($json_content_piece as $key => $value) {
  //   $fields[] = array(
  //     'field_name' => 'field_day_' . $key,
  //     'type'       => 'float',
  //     'label'      => t(ucfirst($key)),
  //   );
  // }

  return $fields;
}

function _entity_create_fields_save($entity_info, $field) {
  $field_storage = FieldStorageConfig::create(array(
    'field_name'  => $field['field_name'],
    'entity_type' => $entity_info['entity_type'],
    'type'  => $field['type'],
    'settings' => array(
      'target_type' => 'node',
    ),
  ));
  $field_storage->save();

  $field_config = FieldConfig::create([
    'field_name'  => $field['field_name'],
    'label'       => $field['label'],
    'entity_type' => $entity_info['entity_type'],
    'bundle'      => $entity_info['bundle'],
  ]);
  $field_config->save();

  // manage form display
  entity_get_form_display($entity_info['entity_type'], $entity_info['bundle'], 'default')
    ->setComponent($field['field_name'], [
      'settings' => [
        'display' => TRUE,
      ],
    ])
    ->save();

  // manage display
  entity_get_display($entity_info['entity_type'], $entity_info['bundle'], 'default')
    ->setComponent($field['field_name'], [
      'settings' => [
        'display_summary' => TRUE,
      ],
    ])
    ->save();
}
