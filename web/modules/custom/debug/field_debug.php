<?php
_run_batch_entity_create_fields();
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
  $fields[] = array(
    'field_name' => 'field_day_close',
    'type'       => 'float',
    'label'      => t('Close'),
  );
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
