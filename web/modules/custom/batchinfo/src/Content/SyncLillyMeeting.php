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

  /**
   *
   */
  public function getUserAllTidsOnBidash($entity_field = array(), $meeting_json) {
    $entity_ids = array();

    $entity_names = $this->getEntityFieldAllTargetIdsNameFromJson($entity_field['field_name'], $meeting_json, 'user');
    if ($entity_names) {
      foreach ($entity_names as $key => $entity_name) {
        $user_uid = $this->getUserUidOnBidash($entity_name);

        if ($user_uid) {
          $entity_ids[] = $user_uid;
        }
        else {
          drupal_set_message($liily_meeting_nid . ' have not this speaker name - ' . $entity_name, 'error');
          $this->createUserNoNameOnBidash($entity_name, $meeting_json, $key);
        }
      }
    }

    return $entity_ids;
  }

  /**
   *
   */
  public function getUserUidOnBidash($entity_name = NULL) {
    $user_uid = \Drupal::getContainer()->get('flexinfo.user.service')->getUidByUserName($entity_name);

    if ($user_uid) {
    }
    else {
      $user_uid = \Drupal::getContainer()->get('flexinfo.user.service')->getUidByMail($entity_name . '@noemail.ca');
    }

    return $user_uid;
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

  public $json_pool_filename;
  public $json_pool_path;

  /**
   *
   */
  public function __construct() {
    $this->json_meeting_filename = 'bi_lilly_meeting_sheet.json';
    $this->json_pool_filename   = 'bi_lilly_pool_sheet.json';

    $this->json_meeting_path = '/sites/default/files/json/' . $this->json_meeting_filename;
    $this->json_pool_path   = '/sites/default/files/json/' . $this->json_pool_filename;
  }

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
   require_once(DRUPAL_ROOT . '/modules/custom/phpdebug/import_json/import_node_meeting.php');
   $SyncLillyMeeting = new SyncLillyMeeting();
   $SyncLillyMeeting->checkBiHaveSameMeetingAndSaveToSheet(5603);
   *
   * batch run pool start
   */
  public function checkBiHaveSamePoolAndSaveToSheet($liily_pool_nid = NULL) {
    if (TRUE) {
      $pool_json = $this->getLillyEntityJsonContent($liily_pool_nid);

      $this->checkTermExistBeforeRunCreatePool($pool_json);

      $this->runCreatePoolOnBidash($pool_json);
    }

    return;
  }

  /**
   *
   */
  public function runCreateMeetingOnBidash($meeting_json = NULL) {
    $fields_value = $this->generateMeetingfieldsValue($meeting_json);

    \Drupal::getContainer()->get('flexinfo.node.service')->entityCreateNode($fields_value);

    return;
  }

  /**
   *
   */
  public function runCreatePoolOnBidash($pool_json = NULL) {
    $fields_value = $this->generatePoolfieldsValue($pool_json);

    \Drupal::getContainer()->get('flexinfo.node.service')->entityCreateNode($fields_value);

    return;
  }

  /**
   *
   */
  public function generateMeetingfieldsValue($meeting_json = NULL) {
    $entity_bundle = 'meeting';
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();

    $fields_value = array(
      'type' => $entity_bundle,
      'title' => 'Entity Create Alliance ' . $entity_bundle,
      'langcode' => $language,
      'uid' => 1,
      'status' => 1,
    );

    $fields_value['field_meeting_representative'] = array(
      520,  // BI Representative
    );

    $meeting_fields = $this->allBidashMeetingFields();
    foreach ($meeting_fields as $row) {
      if (isset($row['vocabulary'])) {
        $term_tids = $this->getTermAllTidsOnBidash($row, $meeting_json);
        $fields_value[$row['field_name']] = $term_tids;
      }
      elseif (isset($row['userRole'])) {
        $user_uids = $this->getUserAllTidsOnBidash($row, $meeting_json);
        $fields_value[$row['field_name']] = $user_uids;
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
  public function generatePoolfieldsValue($pool_json = NULL) {
    $entity_bundle = 'pool';
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();

    $fields_value = array(
      'type' => $entity_bundle,
      'title' => 'Entity Create Alliance ' . $entity_bundle,
      'langcode' => $language,
      'uid' => 1,
      'status' => 1,
    );

    $json_file_content = \Drupal::getContainer()->get('flexinfo.json.service')->fetchConvertJsonToArrayFromInternalPath($this->json_meeting_path);

    $pool_fields = $this->allBidashPoolFields();
    foreach ($pool_fields as $row) {
      if (isset($row['field_name']) && $row['field_name'] == 'field_pool_meetingnid') {

        $lilly_meeting_nid = $this->getEntityFieldFirstTargetIdFromJson($row['field_name'], $pool_json);

        if (isset($json_file_content[$lilly_meeting_nid])) {
          $bi_meeting_nid = $json_file_content[$lilly_meeting_nid];
          $fields_value[$row['field_name']][] = $bi_meeting_nid;
        }
        else {
          drupal_set_message($liily_meeting_nid . ' have not found on BI During the migrate  - ', 'error');
        }
      }
      elseif (isset($row['vocabulary'])) {
        $term_tids = $this->getTermAllTidsOnBidash($row, $pool_json);
        $fields_value[$row['field_name']] = $term_tids;
      }
      elseif (isset($row['userRole'])) {
        $user_uids = $this->getUserAllTidsOnBidash($row, $pool_json);
        $fields_value[$row['field_name']] = $user_uids;
      }
      else {
        $fields_value[$row['field_name']] = $this->getEntityFieldAllValueFromJson($row['field_name'], $pool_json);
      }
    }

    return $fields_value;
  }

  /**
   *
   */
  public function checkTermExistBeforeRunCreateMeeting($meeting_json = NULL) {
    $this->checkBidashHaveSameTerm($meeting_json, $term_fields = $this->allBidashMeetingFields());
    $this->checkBidashHaveSameUser($meeting_json);

    return;
  }

  /**
   *
   */
  public function checkTermExistBeforeRunCreatePool($pool_json = NULL) {
    $this->checkBidashHaveSameTerm($pool_json, $term_fields = $this->allBidashPoolFields());

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
  public function checkBidashHaveSameUser($meeting_json = NULL) {
    $meeting_fields = $this->allBidashMeetingFields();

    $user_uids = array();

    foreach ($meeting_fields as $row) {
      if (isset($row['userRole'])) {
        $user_uids = $this->getUserAllTidsOnBidash($row, $meeting_json);
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
    $lilly_meeting_nids = $this->lillyAllianceMeetingNids();
    // $lilly_meeting_nids = $this->lillyAllianceEntityNidsFromFeedUrl('meeting');

    drupal_set_message('Total have - ' . count($lilly_meeting_nids) . ' - meetings');

    $json_file_content = \Drupal::getContainer()->get('flexinfo.json.service')->fetchConvertJsonToArrayFromInternalPath($this->json_meeting_path);

    $output = $this->filterLillyAllianceNids($lilly_meeting_nids, $json_file_content);

    return $output;
  }

  /**
   *
   */
  public function lillyAllianceEntityNidsFromFeedUrl($entity_bundle = 'meeting') {
    $output = array();

    $feed_url = 'http://lillymedical.education/superexport/' . $entity_bundle . '/entitylist';

    $json_file_content = \Drupal::getContainer()->get('flexinfo.json.service')->fetchConvertJsonToArrayFromUrl($feed_url);

    $alliance_bundle_list = 'alliance' . ucfirst($entity_bundle) . 'List';

    if (isset($json_file_content[$alliance_bundle_list])) {
      $output = $json_file_content[$alliance_bundle_list];
    }

    return $output;
  }

  /**
   *
   */
  public function filterLillyAlliancePoolNids() {
    $lilly_pool_nids = $this->lillyAlliancePoolNids();
    // $lilly_pool_nids = $this->lillyAllianceEntityNidsFromFeedUrl('pool');

    drupal_set_message('Total have - ' . count($lilly_pool_nids) . ' - pools');

    $json_file_content = \Drupal::getContainer()->get('flexinfo.json.service')->fetchConvertJsonToArrayFromInternalPath($this->json_pool_path);

    $output = $this->filterLillyAllianceNids($lilly_pool_nids, $json_file_content);
    $output = $this->queryFilterLillyAlliancePoolNids($output);

    return $output;
  }

  /**
   *
   */
  public function filterLillyAllianceNids($lilly_entity_nids, $json_file_content) {
    $output = array();

    if ($lilly_entity_nids) {
      foreach ($lilly_entity_nids as $lilly_entity_nid) {
        if (!isset($json_file_content[$lilly_entity_nid])) {
          $output[] = $lilly_entity_nid;
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function queryFilterLillyAlliancePoolNids($lilly_entity_nids) {
    $output = array();

    if ($lilly_entity_nids) {

      $json_file_meeting_content = \Drupal::getContainer()->get('flexinfo.json.service')->fetchConvertJsonToArrayFromInternalPath($this->json_meeting_path);
      $json_file_pool_content = \Drupal::getContainer()->get('flexinfo.json.service')->fetchConvertJsonToArrayFromInternalPath($this->json_pool_path);

      foreach ($lilly_entity_nids as $lilly_entity_nid) {
        $bi_entity_nids = $this->queryFilterAllianceBiPoolNids($lilly_entity_nid, $json_file_meeting_content);

        if (count($bi_entity_nids) > 0) {
          if (count($bi_entity_nids) > 1) {
            drupal_set_message($lilly_entity_nid . ' have - ' . count($bi_entity_nids) . ' - same item - ' . implode(',', $bi_entity_nids), 'error');
          }
          else {
            $json_file_pool_content = $this->cacheEntityNidsToData(reset($bi_entity_nids), $lilly_entity_nid, $json_file_pool_content, 'pool');
          }
        }
        else {
          $output[] = $lilly_entity_nid;
        }
      }

      if ($json_file_pool_content) {
        ksort($json_file_pool_content);
      }

      $json_data = json_encode($json_file_pool_content, JSON_PRETTY_PRINT);
      $this->saveJsonDataToFile($json_data, $entity_bundle = 'pool');
    }

    return $output;
  }

  /**
   *
   */
  public function queryFilterLillyAlliancePoolNid($lilly_entity_nid) {
    $output = array();

    if ($lilly_entity_nid) {

      $json_file_meeting_content = \Drupal::getContainer()->get('flexinfo.json.service')->fetchConvertJsonToArrayFromInternalPath($this->json_meeting_path);
      $json_file_pool_content = \Drupal::getContainer()->get('flexinfo.json.service')->fetchConvertJsonToArrayFromInternalPath($this->json_pool_path);

      $bi_entity_nids = $this->queryFilterAllianceBiPoolNids($lilly_entity_nid, $json_file_meeting_content);

      if (count($bi_entity_nids) > 0) {
        if (count($bi_entity_nids) > 1) {
          drupal_set_message($lilly_entity_nid . ' have - ' . count($bi_entity_nids) . ' - same item - ' . implode(',', $bi_entity_nids), 'error');
        }
        else {
          $json_file_pool_content = $this->cacheEntityNidsToData(reset($bi_entity_nids), $lilly_entity_nid, $json_file_pool_content, 'pool');
        }
      }
      else {
        $output = $lilly_entity_nid;
      }

    }

    return $output;
  }

  /**
   * return BI Pool Nids
   */
  public function queryFilterAllianceBiPoolNids($lilly_pool_nid, $json_file_meeting_content) {
    $pool_nids = array();

    $lilly_pool_json = $this->getLillyEntityJsonContent($lilly_pool_nid, 'node');
    $lilly_meeting_nid = $this->getEntityFieldFirstTargetIdFromJson('field_pool_meetingnid', $lilly_pool_json);

    if (isset($json_file_meeting_content[$lilly_meeting_nid])) {
      $bi_meeting_nid = $json_file_meeting_content[$lilly_meeting_nid];

      $question_term_name = $this->getEntityFieldFirstTargetIdNameFromJson('field_pool_questiontid', $lilly_pool_json, 'taxonomy/term');
      $bi_question_tid = $this->getTermTidOnBidash($question_term_name, 'field_pool_questiontid', 'questionlibrary', $lilly_pool_json);

      $referuser_name = $this->getEntityFieldFirstTargetIdNameFromJson('field_pool_referuser', $lilly_pool_json, 'user');
      $referuser_uid  = NULL;
      if ($referuser_name) {
        $referuser_uid  = $this->getUserUidOnBidash($referuser_name);
      }

      $referterm_name = $this->getEntityFieldFirstTargetIdNameFromJson('field_pool_referterm', $lilly_pool_json, 'taxonomy/term');
      $referterm_tid  = NULL;
      if ($referterm_name) {
        $referterm_tid = $this->getTermTidOnBidash($referterm_name, 'field_pool_referterm', 'module', $lilly_pool_json);
      }

      $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
      $query = $query_container->queryNidsByBundle('pool');

      $group = $query_container->groupStandardByFieldValue($query, 'field_pool_meetingnid', $bi_meeting_nid);
      $query->condition($group);

      $group = $query_container->groupStandardByFieldValue($query, 'field_pool_questiontid', $bi_question_tid);
      $query->condition($group);

      if ($referuser_uid) {
        // drupal_set_message('referuser_uid ' . $referuser_uid . ' on lilly_pool_nid ' . $lilly_pool_nid);
        $group = $query_container->groupStandardByFieldValue($query, 'field_pool_referuser', $referuser_uid);
        $query->condition($group);
      }

      if ($referterm_tid) {
        $group = $query_container->groupStandardByFieldValue($query, 'field_pool_referterm', $referterm_tid);
        $query->condition($group);
      }

      $pool_nids = $query_container->runQueryWithGroup($query);
    }

    return $pool_nids;
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
   * @param,
   */
  public function cacheEntityNidsToData($bi_entity_nid, $liily_entity_nid, $json_file_content, $entity_bundle = 'pool') {
    if (!isset($json_file_content[$liily_entity_nid])) {
      $json_file_content[$liily_entity_nid] = $bi_entity_nid;
    }

    return $json_file_content;
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
  public function allBidashMeetingFields() {
    $meeting_fields = array(
      // user
      array(
        'field_name' => 'field_meeting_speaker',
        'userRole' => 'speaker',
      ),

      // term
      array(
        'field_name' => 'field_meeting_city',
        'vocabulary' => 'city',
      ),
      array(
        'field_name' => 'field_meeting_evaluationform',
        'vocabulary' => 'evaluationform',
      ),
      array(
        'field_name' => 'field_meeting_location',
        'vocabulary' => 'meetinglocation',
      ),
      array(
        'field_name' => 'field_meeting_meetingformat',
        'vocabulary' => 'meetingformat',
      ),
      array(
        'field_name' => 'field_meeting_module',
        'vocabulary' => 'module',
      ),
      array(
        'field_name' => 'field_meeting_program',
        'vocabulary' => 'program',
      ),
      array(
        'field_name' => 'field_meeting_programclass',
        'vocabulary' => 'programclass',
      ),
      array(
        'field_name' => 'field_meeting_province',
        'vocabulary' => 'province',
      ),
      array(
        'field_name' => 'field_meeting_received',
        'vocabulary' => 'meetingreceived',
      ),
      array(
        'field_name' => 'field_meeting_usergroup',
        'vocabulary' => 'usergroup',
      ),

      // value
      array(
        'field_name' => 'field_meeting_address',
      ),
      array(
        'field_name' => 'field_meeting_catering',
      ),
      array(
        'field_name' => 'field_meeting_date',
      ),
      array(
        'field_name' => 'field_meeting_evaluationnum',
      ),
      array(
        'field_name' => 'field_meeting_eventme',
      ),
      array(
        'field_name' => 'field_meeting_foodcost',
      ),
      array(
        'field_name' => 'field_meeting_honorarium',
      ),
      array(
        'field_name' => 'field_meeting_latitude',
      ),
      array(
        'field_name' => 'field_meeting_longitude',
      ),
      array(
        'field_name' => 'field_meeting_longitude',
      ),
      array(
        'field_name' => 'field_meeting_multitherapeutic',  // list
      ),
      array(
        'field_name' => 'field_meeting_postalcode',
      ),
      array(
        'field_name' => 'field_meeting_signature',
      ),
      array(
        'field_name' => 'field_meeting_summaryevaluation',  // boolean
      ),
      array(
        'field_name' => 'field_meeting_venuename',  // boolean
      ),
    );

    return $meeting_fields;
  }

  /**
   *
   */
  public function allBidashPoolFields() {
    $pool_fields = array(
      // term
      array(
        'field_name' => 'field_pool_questiontid',
        'vocabulary' => 'questionlibrary',
      ),
      array(
        'field_name' => 'field_pool_answerterm',
        'vocabulary' => 'selectkeyanswer',
      ),
      array(
        'field_name' => 'field_pool_referterm',
        'vocabulary' => 'module',
      ),

      // node
      array(
        'field_name' => 'field_pool_meetingnid',
        'node' => 'meeting',
      ),

      // user
      array(
        'field_name' => 'field_pool_referuser',
        'userRole' => 'speaker',
      ),

      // value
      array(
        'field_name' => 'field_pool_answerint',
      ),
      array(
        'field_name' => 'field_pool_answertext',
      ),
      array(
        'field_name' => 'field_pool_referother',
      ),

    );

    return $pool_fields;
  }

  /**
   * https://lillymedical.education/superexport/meeting/entitylist
   */
  public function lillyAllianceMeetingNids() {
    $nids = array(
      "60532",
      "60778",
      "60799",
      "60823",
      "60845",
      "60871",
      "61107",
      "61178",
      "61222",
      "61244",
      "61289",
      "61555",
      "61582",
      "61695",
      "61722",
      "61746",
      "61780",
      "61814",
      "61838"
    );

    return $nids;
  }

  /**
   * https://lillymedical.education/superexport/pool/entitylist
   */
  public function lillyAlliancePoolNids() {
    $nids = array(
      "60560",
      "60780",
      "60781",
      "60782",
      "60783",
      "60784",
      "60785",
      "60786",
      "60787",
      "60788",
      "60789",
      "60790",
      "60791",
      "60792",
      "60795",
      "60801",
      "60802",
      "60803",
      "60804",
      "60805",
      "60806",
      "60807",
      "60808",
      "60809",
      "60810",
      "60811",
      "60812",
      "60813",
      "60814",
      "60818",
      "60825",
      "60826",
      "60827",
      "60828",
      "60829",
      "60830",
      "60831",
      "60832",
      "60833",
      "60834",
      "60835",
      "60836",
      "60837",
      "60847",
      "60848",
      "60849",
      "60850",
      "60851",
      "60852",
      "60853",
      "60854",
      "60855",
      "60856",
      "60857",
      "60858",
      "60859",
      "60860",
      "60873",
      "60874",
      "60875",
      "60876",
      "60877",
      "60878",
      "60879",
      "60880",
      "60881",
      "60882",
      "60883",
      "60884",
      "60885",
      "60888",
      "60889",
      "60890",
      "60899",
      "60906",
      "60927",
      "60947",
      "61109",
      "61110",
      "61111",
      "61112",
      "61113",
      "61114",
      "61115",
      "61116",
      "61117",
      "61118",
      "61120",
      "61121",
      "61122",
      "61123",
      "61129",
      "61133",
      "61134",
      "61135",
      "61136",
      "61137",
      "61138",
      "61139",
      "61140",
      "61141",
      "61143",
      "61144",
      "61146",
      "61148",
      "61149",
      "61153",
      "61159",
      "61162",
      "61224",
      "61225",
      "61226",
      "61227",
      "61228",
      "61229",
      "61230",
      "61231",
      "61232",
      "61233",
      "61235",
      "61236",
      "61237",
      "61242",
      "61246",
      "61247",
      "61248",
      "61249",
      "61250",
      "61251",
      "61252",
      "61253",
      "61254",
      "61255",
      "61256",
      "61257",
      "61258",
      "61261",
      "61264",
      "61291",
      "61292",
      "61293",
      "61294",
      "61295",
      "61296",
      "61297",
      "61298",
      "61300",
      "61301",
      "61302",
      "61307",
      "61310",
      "61318",
      "61362",
      "61363",
      "61364",
      "61365",
      "61366",
      "61367",
      "61368",
      "61369",
      "61370",
      "61371",
      "61372",
      "61373",
      "61375",
      "61376",
      "61383",
      "61466",
      "61467",
      "61468",
      "61469",
      "61470",
      "61471",
      "61472",
      "61473",
      "61474",
      "61475",
      "61476",
      "61477",
      "61480",
      "61484",
      "61491",
      "61557",
      "61558",
      "61559",
      "61560",
      "61561",
      "61562",
      "61563",
      "61564",
      "61565",
      "61566",
      "61567",
      "61568",
      "61569",
      "61570",
      "61580",
      "61581",
      "61584",
      "61585",
      "61586",
      "61587",
      "61588",
      "61589",
      "61590",
      "61591",
      "61592",
      "61593",
      "61594",
      "61595",
      "61596",
      "61597",
      "61599",
      "61600",
      "61611",
      "61616",
      "61697",
      "61698",
      "61699",
      "61700",
      "61701",
      "61702",
      "61703",
      "61704",
      "61705",
      "61706",
      "61707",
      "61708",
      "61709",
      "61710",
      "61713",
      "61717",
      "61724",
      "61725",
      "61726",
      "61727",
      "61728",
      "61729",
      "61730",
      "61731",
      "61732",
      "61733",
      "61734",
      "61735",
      "61737",
      "61741",
      "61745",
      "61748",
      "61749",
      "61750",
      "61751",
      "61752",
      "61753",
      "61754",
      "61755",
      "61756",
      "61757",
      "61758",
      "61759",
      "61760",
      "61761",
      "61763",
      "61765",
      "61766",
      "61767",
      "61782",
      "61783",
      "61784",
      "61785",
      "61786",
      "61787",
      "61788",
      "61789",
      "61790",
      "61791",
      "61792",
      "61793",
      "61795",
      "61800",
      "61801",
      "61802",
      "61805",
      "61816",
      "61817",
      "61818",
      "61819",
      "61820",
      "61821",
      "61822",
      "61823",
      "61824",
      "61825",
      "61826",
      "61827",
      "61828",
      "61831",
      "61836",
      "61840",
      "61841",
      "61842",
      "61843",
      "61844",
      "61845",
      "61846",
      "61847",
      "61848",
      "61849",
      "61850",
      "61851",
      "61852",
      "61855",
      "61857",
      "61863",
      "61869",
      "61870"
    );

    return $nids;
  }

}
