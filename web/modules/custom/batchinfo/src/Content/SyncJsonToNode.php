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

class SyncJsonToNode {

  public $json_filename;
  public $json_file_path;

  /**
   *
   */
  public function __construct() {
    $this->json_filename = 'historyDataByCodeList.json';

    $this->json_file_path = '/sites/default/files/json/tushare/' . $this->json_filename;
  }

  /**
   *
   */
  public function getImportJsonContent() {
    // $output = $this->getSingleJsonContent();
    $output = $this->getAllPiecesJsonContent();

    drupal_set_message('Total have - ' . count($output) . ' - records');

    return $output;
  }

  /**
   *
   */
  public function getSingleJsonContent() {
    $output = \Drupal::getContainer()
      ->get('flexinfo.json.service')
      ->fetchConvertJsonToArrayFromInternalPath($this->json_file_path);

    return $output;
  }

  /**
   *
   */
  public function getPiecesJsonFileName($pageNum = 0) {
    $json_filename = 'historyDataByCodeListPiece_' . $pageNum .  '.json';

    $output = '/sites/default/files/json/tushare/' . $json_filename;

    return $output;
  }

  /**
   *
   */
  public function getAllPiecesJsonContent() {
    $app_root = \Drupal::hasService('app.root') ? \Drupal::root() : DRUPAL_ROOT;

    $output = [];

    for ($i = 0; $i < 60; $i++) {
      $piece_file_name = $this->getPiecesJsonFileName($i);
      $piece_file_url = $app_root . $piece_file_name;

      if (file_exists($piece_file_url)) {
        $json_pieces = \Drupal::getContainer()
          ->get('flexinfo.json.service')
          ->fetchConvertJsonToArrayFromInternalPath($piece_file_name);

        $output = array_merge($output, $json_pieces);
      }
    }

    return $output;
  }

  /**
   *
   * @param, @key is code and date
   */
  public function runBatchinfoCreateNodeEntity($key, $json_content_piece = NULL) {
    $explodeKeyArray = $this->explodeKeyByCodeAndDate($key);

    $code = $explodeKeyArray['code'];
    $date = $explodeKeyArray['date'];

    if (TRUE) {
      $node_nids = $this->queryNodeToCheckExist($code, $date, $json_content_piece);

      if (count($node_nids) > 1) {
        drupal_set_message('Node have - ' . count($node_nids) . ' - few same item', 'error');
        return;
      }
      elseif (count($node_nids) > 0) {
        drupal_set_message('Node have - ' . count($node_nids) . ' - same item');
        return;
      }
      else {
        $this->runCreateNodeEntity($code, $date, $json_content_piece);
      }
    }

    sleep(0.01);

    return;
  }

  /**
   *
   */
  public function runCreateNodeEntity($code, $date, $json_content_piece = NULL) {
    $fields_value = $this->generateNodefieldsValue($code, $date, $json_content_piece);

    \Drupal::getContainer()->get('flexinfo.node.service')->entityCreateNode($fields_value);

    return;
  }

  /**
   *
   */
  public function generateNodefieldsValue($code, $date, $json_content_piece = NULL) {
    $entity_bundle = 'day';
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();

    $fields_value = array(
      'type' => $entity_bundle,
      'title' =>  $entity_bundle . ' From JSON ' . $code . ' - ' . $date,
      'langcode' => $language,
      'uid' => 1,
      'status' => 1,
    );

    // special fix value
    $code_tid = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getTidByTermName($code, $vocabulary_name = 'code');

    $fields_value['field_day_code'] = array(
      'target_id' => $code_tid,  // term tid
    );

    $fields_value['field_day_date'] = array(
      $date,
    );

    $node_bundle_fields = $this->allNodeBundleFields($json_content_piece);
    foreach ($node_bundle_fields as $row) {
      if (isset($row['vocabulary'])) {

      }
      elseif (isset($row['userRole'])) {

      }
      else {
        $fields_value[$row['field_name']] = $json_content_piece[$row['json_key']];
      }
    }

    return $fields_value;
  }

  /**
   *
   */
  public function queryNodeToCheckExist($code, $date, $json_content_piece = NULL) {
    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('day');

    if (isset($date)) {
      $group = $query_container->groupStandardByFieldValue($query, 'field_day_date', $date);
      $query->condition($group);
    }

    /* term */
    if (isset($code)) {
      $group = $query_container->groupStandardByFieldValue($query, 'field_day_code.entity.name', $code);
      $query->condition($group);
    }

    /* value */
    if (isset($json_content_piece['open'])) {
      $group = $query_container->groupStandardByFieldValue($query, 'field_day_open', NULL, 'IS NOT NULL');
      $query->condition($group);
    }

    /* user */
    // $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_speaker.entity.name', $speaker_name);
    // $query->condition($group);

    $nids = $query_container->runQueryWithGroup($query);

    return $nids;
  }

  /**
   *
   */
  public function explodeKeyByCodeAndDate($key) {
    $output['code'] = NULL;
    $output['date'] = NULL;

    $pieces = explode("_", $key);

    if (isset($pieces[0])) {
      $output['code'] = $pieces[0];
    }
    if (isset($pieces[1])) {
      $output['date'] = $pieces[1];
    }

    return $output;
  }

  /**
   *
   */
  public function allNodeBundleFields($json_content_piece = array()) {
    /** user sample */
    // $node_bundle_fields[] = array(
    //   'field_name' => 'field_meeting_speaker',
    //   'userRole' => 'speaker',
    // );

    /** term sample */
    // $node_bundle_fields[] = array(
    //   'field_name' => 'field_day_code',
    //   'vocabulary' => 'code',
    // );

    /** value sample */
    // $node_bundle_fields[] = array(
    //   'field_name' => 'field_day_open',
    //   'json_key' => 'open',
    // );

    $json_content_piece = array(
      "open" => 12.0,
      "high" => 12.8,
      "close" => 12.7,
      "low" => 11.07,
      "volume" => 1472901.6200000001,
      "price_change" => 0.4,
      "p_change" => 3.25,
      "ma5" => 12.7,
      "ma10" => 12.7,
      "ma20" => 12.7,
      "v_ma5" => 1472901.6200000001,
      "v_ma10" => 1472901.6200000001,
      "v_ma20" => 1472901.6200000001
    );

    foreach ($json_content_piece as $key => $value) {
      $node_bundle_fields[] = array(
        'field_name' => 'field_day_' . $key,
        'json_key' => $key,
      );
    }

    return $node_bundle_fields;
  }

}
