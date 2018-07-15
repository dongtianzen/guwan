<?php

/**
 * @file
 */
namespace Drupal\dashpage\Content;

use Drupal\Core\Controller\ControllerBase;

/**
 * An example controller.
 $DashpageContentGenerator = new DashpageContentGenerator();
 $DashpageContentGenerator->angularPage();
 */
class DashpageContentGenerator extends ControllerBase {

  public function __construct() {
  }

  /**
   *
   */
  public function standardRankPage() {
    $output = '';
    $output .= '<div class="row">';
      $output .= '<div id="standard-google-map-wrapper">';
        $output .= '<div id="map-canvas">';
          $output .= 'Rank Page';
          $output .= '<br />';
          $output .= $this->getDayNids();
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function getDayNids() {
    $today_date = '2018-07-13';

    $code_tids = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getTidsFromVidName($vid = 'code');

    foreach ($code_tids as $key => $code_tid) {
      $nids = $this->queryDayByCodeByDate($code_tid, $today_date);

      if ($nids) {

        $checkPreviousDayResult = $this->checkPreviousDay($nids[0], $code_tid);

        if ($checkPreviousDayResult) {
          $nids_array[] = $nids[0];
        }
      }
    }

    $output = implode(" - ", $nids_array);

    return $output;
  }

  /**
   *
   */
  public function queryDayByCodeByDate($code_tid, $date) {
    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('day');

    $group = $query_container->groupStandardByFieldValue($query, 'field_day_code', $code_tid);
    $query->condition($group);

    $group = $query_container->groupStandardByFieldValue($query, 'field_day_date', $date);
    $query->condition($group);

    $nids = $query_container->runQueryWithGroup($query);

    return $nids;
  }

  /**
   *
   */
  public function checkPreviousDay($nid, $code_tid) {
    $output = FALSE;

    $today_entity = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
    if ($today_entity) {
      $previous_nids = $this->queryDayByCodeByDate($code_tid, '2018-07-12');
      if ($previous_nids) {

      }
    }

    return $output;
  }

}
