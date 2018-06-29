<?php

/**
 * @file
 * Contains \Drupal\batchinfo\Content\SyncJsonToNode.
 */

/**
 * An example controller.
   $SyncJsonToNode = new SyncJsonToNode();
   $SyncJsonToNode->_run_create_meeting_from_json();
 */

namespace Drupal\batchinfo\Content;

use Symfony\Component\HttpFoundation\JsonResponse;

trait GetEntityFromBIDash {

  /**
   *
   */
  public function createTermOnBidash($term_name, $term_field, $meeting_json) {
    $tid = NULL;

    if ($term_field['vocabulary'] == 'city') {
      $city_tid = $this->getEntityFieldFirstTargetIdFromJson($term_field['field_name'], $meeting_json, 'taxonomy/term');
      $city_json = $this->getLillyEntityJsonContent($city_tid, 'taxonomy/term');

      // Lilly and BI have same Province Tid
      $province_tid = $this->getEntityFieldFirstTargetIdFromJson('field_city_province', $city_json);

      $fields_value = array(
        array(
          'field_name' => 'field_city_province',
          'value' => array($province_tid),
          'vid' => 'province',
        )
      );
      $tid = \Drupal::getContainer()->get('flexinfo.term.service')->entityCreateTermWithFieldsValue($term_name, $term_field['vocabulary'], $fields_value);
    }
    else {
      $tid = \Drupal::getContainer()->get('flexinfo.term.service')->entityCreateTerm($term_name, $term_field['vocabulary']);
    }

    return $tid;
  }

  /**
   *
   */
  public function getTermAllTidsOnBidash($term_field = array(), $meeting_json) {
    $term_tids = array();

    $term_names = $this->getEntityFieldAllTargetIdsNameFromJson($term_field['field_name'], $meeting_json, 'taxonomy/term');
    if ($term_names) {
      foreach ($term_names as $term_name) {
        $term_tid = $this->getTermTidOnBidash($term_name, $term_field['field_name'], $term_field['vocabulary'], $meeting_json);

        // only when have result, push to output
        if ($term_tid) {
          $term_tids[] = $term_tid;
        }
        else {
          $term_tids[] = $this->createTermOnBidash($term_name, $term_field, $meeting_json);
        }
      }
    }

    return $term_tids;
  }

  /**
   *
   */
  public function getTermFirstTidOnBidash($term_field = array(), $meeting_json) {
    $term_name = $this->getEntityFieldFirstTargetIdNameFromJson($term_field['field_name'], $meeting_json, 'taxonomy/term');
    $term_tid = $this->getTermTidOnBidash($term_name, $term_field['field_name'], $term_field['vocabulary'], $meeting_json);

    return $term_tid;
  }

  /**
   *
   */
  public function getTermTidOnBidash($term_name = NULL, $field_name = NULL, $vocabulary = NULL, $meeting_json) {
    $term_tid = NULL;

    if ($term_name) {
      if ($vocabulary == 'city') {
        $city_tid = $this->getEntityFieldFirstTargetIdFromJson($field_name, $meeting_json, 'taxonomy/term');
        $city_json = $this->getLillyEntityJsonContent($city_tid, 'taxonomy/term');

        $province_tid = $this->getEntityFieldFirstTargetIdFromJson('field_city_province', $city_json);
        $bi_city_tid = \Drupal::getContainer()
          ->get('flexinfo.term.service')
          ->getTidByCityNameAndProvinceTid($term_name, $vocabulary, $province_tid);

        $term_tid = $bi_city_tid;
      }
      elseif ($vocabulary == 'questionlibrary') {
        $lilly_question_tid = $this->getEntityFieldFirstTargetIdFromJson($field_name, $meeting_json, 'taxonomy/term');
        $lilly_question_json = $this->getLillyEntityJsonContent($lilly_question_tid, 'taxonomy/term');
        $lilly_question_fieldtype_tid = $this->getEntityFieldFirstTargetIdFromJson('field_queslibr_fieldtype', $lilly_question_json);

        $bi_question_tid = \Drupal::getContainer()
          ->get('flexinfo.term.service')
          ->getTidByQuestionNameAndFieldTypeTid($term_name, $vocabulary, $lilly_question_fieldtype_tid);
        $term_tid = $bi_question_tid;
      }
      else {
        $term_tid = \Drupal::getContainer()->get('flexinfo.term.service')->getTidByTermName($term_name, $vocabulary);
      }
    }

    return $term_tid;
  }

}

/**
 *
 */
class GetEntityFromJson {

  /**
   *
   */
  public function getEntityFieldAllTargetIdsFromJson($field = NULL, $json = NULL) {
    $target_ids = array();
    if (isset($json[$field][0]['target_id'])) {
      foreach ($json[$field] as $row) {
        $target_ids[] = $row['target_id'];
      }
    }

    return $target_ids;
  }

  /**
   *
   */
  public function getEntityFieldFirstTargetIdFromJson($field = NULL, $json = NULL) {
    $target_id = NULL;
    if (isset($json[$field][0]['target_id'])) {
      $target_id = $json[$field][0]['target_id'];
    }

    return $target_id;
  }

}

class SyncJsonToNode extends GetEntityFromJson {

  use GetEntityFromBIDash;

  public $json_meeting_filename;
  public $json_meeting_path;

  /**
   *
   */
  public function __construct() {
    $this->json_meeting_filename = 'import_record_node_day.json';
    $this->json_meeting_path = '/modules/custom/batchinfo/json/' . $this->json_meeting_filename;
  }

  /**
   *
   require_once(DRUPAL_ROOT . '/modules/custom/phpdebug/import_json/import_node_meeting.php');
   $SyncLillyMeeting = new SyncLillyMeeting();
   $SyncLillyMeeting->checkBiHaveSameMeetingAndSaveToSheet(5603);
   */
  public function runBatchinfoCreateNodeEntity($json_content_piece = NULL) {
    if (TRUE) {
      $json_content_piece = $this->getLillyEntityJsonContent($liily_meeting_nid);

      $node_nids = $this->queryNodeToCheckExist($json_content_piece);

      if (count($node_nids) > 1) {
        drupal_set_message('Node have - ' . count($node_nids) . ' - few same item', 'error');
        return;
      }
      elseif (count($node_nids) > 0) {
        drupal_set_message('Node have - ' . count($node_nids) . ' - same item');
        return;
      }
      else {
        $this->runCreateMeetingOnBidash($json_content_piece);
      }
    }

    return;
  }

  /**
   *
   */
  public function runCreateMeetingOnBidash($meeting_json = NULL) {
    $fields_value = $this->generateNodefieldsValue($meeting_json);

    \Drupal::getContainer()->get('flexinfo.node.service')->entityCreateNode($fields_value);

    return;
  }

  /**
   *
   */
  public function generateNodefieldsValue($meeting_json = NULL) {
    $entity_bundle = 'day';
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();

    $fields_value = array(
      'type' => $entity_bundle,
      'title' => 'Entity Create By Import From JSON ' . $entity_bundle,
      'langcode' => $language,
      'uid' => 1,
      'status' => 1,
    );

    // special fix value
    // $fields_value['field_day_code'] = array(
    //   520,  // sample tid
    // );

    $node_bundle_fields = $this->allNodeBundleFields();
    foreach ($node_bundle_fields as $row) {
      if (isset($row['vocabulary'])) {
        $term_tids = $this->getTermAllTidsOnBidash($row, $meeting_json);
        $fields_value[$row['field_name']] = $term_tids;
      }
      elseif (isset($row['userRole'])) {

      }
      else {
        $fields_value[$row['field_name']] = $this->getEntityFieldAllValueFromJson($row['field_name'], $meeting_json);
      }
    }

    return $fields_value;
  }

  /**
   *
   */
  public function queryNodeToCheckExist($json_content_piece = NULL) {
    $meeting_date = $this->getEntityFieldFirstValueFromJson('field_day_date', $meeting_json);

    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('day');

    $group = $query_container->groupStandardByFieldValue($query, 'field_day_date', $meeting_date);
    $query->condition($group);

    /* user */
    // $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_speaker.entity.name', $speaker_name);
    // $query->condition($group);

    /* term */
    $group = $query_container->groupStandardByFieldValue($query, 'field_day_code.entity.name', $city_name);
    $query->condition($group);

    /* value */
    if ($address) {
      $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_address', $address);
      $query->condition($group);
    }

    $nids = $query_container->runQueryWithGroup($query);

    return $nids;
  }

  /**
   *
   */
  public function getImportJsonContent() {
    $output = \Drupal::getContainer()
      ->get('flexinfo.json.service')
      ->fetchConvertJsonToArrayFromInternalPath($this->json_meeting_path);

    drupal_set_message('Total have - ' . count($output) . ' - records');

    return $output;
  }

  /**
   *
   */
  public function allNodeBundleFields() {
    $node_bundle_fields = array(
      // user
      // array(
      //   'field_name' => 'field_meeting_speaker',
      //   'userRole' => 'speaker',
      // ),

      // term
      array(
        'field_name' => 'field_day_code',
        'vocabulary' => 'code',
      ),

      // value
      array(
        'field_name' => 'field_day_date',
      ),
      array(
        'field_name' => 'field_day_open',
      ),

    );

    return $node_bundle_fields;
  }

}
