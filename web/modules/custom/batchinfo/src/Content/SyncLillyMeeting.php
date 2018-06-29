<?php

/**
 * @file
 * Contains \Drupal\batchinfo\Content\SyncLillyMeeting.
 */

/**
 * An example controller.
   $SyncLillyMeeting = new SyncLillyMeeting();
   $SyncLillyMeeting->_run_create_meeting_from_json();
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
  public function createUserNoNameOnBidash($entity_name, $meeting_json, $key = 0) {
    $meeting_nid = $meeting_json['nid'][0]['value'];

    $user_info = array(
      'name' => 'noname' . $meeting_nid . $key,
      'email' => $entity_name . '@noemail.ca',
      'roles' => array('speaker'),
    );

    \Drupal::getContainer()->get('flexinfo.user.service')->entityCreateUser($user_info);

    return;
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

class GetEntityFromJson {

  /**
   *
   */
  public function getJsonFromHttpUrl($url) {
    $response = \Drupal::httpClient()
      ->get(
        $url,
        array(
          'auth' => ['siteadmin', 'flexia123'],
        )
        // array('allow_redirects' => false)
      );

    $json_string = (string) $response->getBody();
    $json_array = json_decode($json_string, TRUE);

    return $json_array;
  }

  /**
   * @param, $entity_type = 'node', 'user', 'taxonomy/term'
   */
  public function getLillyEntityJsonUrl($liily_meeting_nid = NULL, $entity_type = 'node') {
    $url = 'http://lillymedical.education/' . $entity_type . '/' . $liily_meeting_nid . '?_format=json';

    return $url;
  }

  /**
   * @param, $entity_type = 'node', 'user', 'taxonomy/term'
   */
  public function getLillyEntityJsonContent($liily_meeting_nid = NULL, $entity_type = 'node') {
    $url = $this->getLillyEntityJsonUrl($liily_meeting_nid, $entity_type);
    $output = $this->getJsonFromHttpUrl($url);

    return $output;
  }

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
   * @param, $entity_type = 'user', 'taxonomy/term'
   */
  public function getEntityFieldAllTargetIdsNameFromJson($field = NULL, $json = NULL, $entity_type = 'user') {
    $entity_names = NULL;

    $entity_ids = $this->getEntityFieldAllTargetIdsFromJson($field, $json);
    if ($entity_ids && is_array($entity_ids)) {
      foreach ($entity_ids as $entity_id) {
        $entity_names[] = $this->getEntityTargetIdNameFromJson($entity_id, $json, $entity_type);
      }
    }

    return $entity_names;
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

  /**
   * @param, $entity_type = 'user', 'taxonomy/term'
   */
  public function getEntityFieldFirstTargetIdNameFromJson($field = NULL, $json = NULL, $entity_type = 'user') {
    $entity_id = $this->getEntityFieldFirstTargetIdFromJson($field, $json);
    $entity_name = $this->getEntityTargetIdNameFromJson($entity_id, $json, $entity_type);

    return $entity_name;
  }

  /**
   *
   */
  public function getEntityFieldAllValueFromJson($field = NULL, $json = NULL) {
    $output = array();
    if (isset($json[$field][0]['value'])) {
      foreach ($json[$field] as $row) {
        $output[] = $row['value'];
      }
    }

    return $output;
  }

  /**
   *
   */
  public function getEntityFieldFirstValueFromJson($field = NULL, $json = NULL) {
    $output = NULL;
    if (isset($json[$field][0]['value'])) {
      $output = $json[$field][0]['value'];
    }

    return $output;
  }

  /**
   * @param, $entity_type = 'user', 'taxonomy/term'
   */
  public function getEntityTargetIdNameFromJson($entity_id = NULL, $json = NULL, $entity_type = 'user') {
    $entity_name = NULL;

    if ($entity_id) {
      $entity_json = $this->getLillyEntityJsonContent($entity_id, $entity_type);

      $entity_name = $this->getEntityFieldFirstValueFromJson('name', $entity_json);
    }

    return $entity_name;
  }

  /**
   *
   */
  public function getUserFirstNameFromMeetingJson($field_name = NULL, $meeting_json = NULL) {
    $uer_name = $this->getEntityFieldFirstTargetIdNameFromJson($field_name, $meeting_json, 'user');
    return $uer_name;
  }

}

class SyncLillyMeeting extends GetEntityFromJson {

  use GetEntityFromBIDash;

  public $json_meeting_filename;
  public $json_meeting_path;

  /**
   *
   */
  public function __construct() {
    $this->json_meeting_filename = 'import_record_node_day.json';
    $this->json_meeting_path = '/modules/custom/batchinfo/json/' . $this->json_meeting_filename;

  /**
   *
   require_once(DRUPAL_ROOT . '/modules/custom/phpdebug/import_json/import_node_meeting.php');
   $SyncLillyMeeting = new SyncLillyMeeting();
   $SyncLillyMeeting->checkBiHaveSameMeetingAndSaveToSheet(5603);
   */
  public function checkBiHaveSameMeetingAndSaveToSheet($liily_meeting_nid = NULL) {
    if (TRUE) {
      $meeting_json = $this->getLillyEntityJsonContent($liily_meeting_nid);

      $meeting_nids = $this->queryBiMeetingFiveCondition($meeting_json);

      if (count($meeting_nids) > 1) {
        drupal_set_message($liily_meeting_nid . ' have - ' . count($meeting_nids) . ' - same item', 'error');

        return;
      }
      elseif (count($meeting_nids) == 1) {
        $bi_meeting_nid = $meeting_nids[0];

        drupal_set_message($liily_meeting_nid . ' have - ' . count($meeting_nids) . ' - same item');
        $this->saveEntityNidsToData($bi_meeting_nid, $liily_meeting_nid, $this->json_meeting_path);
      }
      else {
        $this->checkTermExistBeforeRunCreateMeeting($meeting_json);

        $this->runCreateMeetingOnBidash($meeting_json);
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

    $fields_value['field_meeting_representative'] = array(
      520,  // BI Representative
    );

    $meeting_fields = $this->allNodeBundleFields();
    foreach ($meeting_fields as $row) {
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
  public function checkTermExistBeforeRunCreateMeeting($meeting_json = NULL) {
    $this->checkBidashHaveSameTerm($meeting_json, $term_fields = $this->allNodeBundleFields());

    return;
  }

  /**
   *
   */
  public function checkBidashHaveSameTerm($entity_json = NULL, $term_fields) {
    if ($term_fields) {
      foreach ($term_fields as $key => $row) {
        if (isset($row['vocabulary'])) {
          $term_tids = $this->getTermAllTidsOnBidash($row, $entity_json);
        }
      }
    }

    return;
  }

  /**
   *
   */
  public function queryBiMeetingFiveCondition($meeting_json = NULL) {
    $meeting_date = $this->getEntityFieldFirstValueFromJson('field_meeting_date', $meeting_json);

    $speaker_name = $this->getEntityFieldFirstTargetIdNameFromJson('field_meeting_speaker', $meeting_json, 'user');
    // $rep_name     = $this->getEntityFieldFirstTargetIdNameFromJson('field_meeting_representative', $meeting_json, 'user');

    $city_name = $this->getEntityFieldFirstTargetIdNameFromJson('field_meeting_city', $meeting_json, 'taxonomy/term');
    $program_name = $this->getEntityFieldFirstTargetIdNameFromJson('field_meeting_program', $meeting_json, 'taxonomy/term');
    $province_name = $this->getEntityFieldFirstTargetIdNameFromJson('field_meeting_province', $meeting_json, 'taxonomy/term');

    $address = $this->getEntityFieldFirstValueFromJson('field_meeting_address', $meeting_json);
    $evaluationnum = $this->getEntityFieldFirstValueFromJson('field_meeting_evaluationnum', $meeting_json);
    $signature  = $this->getEntityFieldFirstValueFromJson('field_meeting_signature', $meeting_json);
    $venue_name = $this->getEntityFieldFirstValueFromJson('field_meeting_venuename', $meeting_json);

    // query
    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('meeting');

    $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_date', $meeting_date);
    $query->condition($group);

    /* user */
    // $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_speaker.entity.name', $speaker_name);
    // $query->condition($group);

    /* term */
    $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_city.entity.name', $city_name);
    $query->condition($group);

    $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_program.entity.name', $program_name);
    $query->condition($group);

    // $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_province.entity.name', $province_name);
    // $query->condition($group);

    // $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_representative.entity.name', $rep_name);
    // $query->condition($group);

    /* value */
    if ($address) {
      $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_address', $address);
      $query->condition($group);
    }

    if ($evaluationnum) {
      $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_evaluationnum', $evaluationnum);
      $query->condition($group);
    }

    if ($signature) {
      $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_signature', $signature);
      $query->condition($group);
    }

    if ($venue_name) {
      $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_venuename', $venue_name);
      $query->condition($group);
    }

    $meeting_nids = $query_container->runQueryWithGroup($query);

    return $meeting_nids;
  }

  /**
   *
   */
  public function filterLillyAllianceMeetingNids() {
    $json_file_content = \Drupal::getContainer()->get('flexinfo.json.service')->fetchConvertJsonToArrayFromInternalPath($this->json_meeting_path);

    drupal_set_message('Total have - ' . count($json_file_content) . ' - records');

    $output = $this->filterLillyAllianceNids($lilly_meeting_nids, $json_file_content);

    return $output;
  }

  /**
   *
   */
  public function saveJsonDataToFile($json_data, $entity_bundle = 'meeting') {
    $destination = 'public://json/' . $this->json_meeting_filename;

    if ($entity_bundle == 'pool') {
      $destination = 'public://json/' . $this->json_pool_filename;
    }

    $file = file_save_data($json_data, $destination, FILE_EXISTS_REPLACE);
  }

  /**
   * @param, $json_meeting_path = $this->json_meeting_path
   */
  public function saveEntityNidsToData($bi_entity_nid, $liily_entity_nid, $json_entity_url, $entity_bundle = 'meeting') {
    $json_file_content = \Drupal::getContainer()->get('flexinfo.json.service')->fetchConvertJsonToArrayFromInternalPath($json_entity_url);

    if (!isset($json_file_content[$liily_entity_nid])) {
      $json_file_content[$liily_entity_nid] = $bi_entity_nid;
    }

    ksort($json_file_content);

    $json_data = json_encode($json_file_content, JSON_PRETTY_PRINT);
    $this->saveJsonDataToFile($json_data, $entity_bundle);
  }

  /**
   *
   */
  public function allNodeBundleFields() {
    $meeting_fields = array(
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

    return $meeting_fields;
  }

}
