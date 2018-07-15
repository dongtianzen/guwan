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

    $tids_array = [];
    foreach ($code_tids as $key => $code_tid) {
      $nids = $this->queryDayByCodeByDate($code_tid, $today_date);

      if ($nids) {
        $checkPreviousDayResult = $this->checkPreviousDay($nids[0], $code_tid);

        if ($checkPreviousDayResult) {
          $tids_array[] = $code_tid;
        }
      }
    }

    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($tids_array);
    foreach ($terms as $key => $term) {
      $output = $term->getName();
      $output .= ' ';
      $output .= \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($term, 'field_code_name');
      $output .= '<br />';
    }

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
  public function queryDayByCodeByDateGreater($code_tid, $date = NULL) {
    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('day');

    $group = $query_container->groupStandardByFieldValue($query, 'field_day_code', $code_tid);
    $query->condition($group);

    $group = $query_container->groupStandardByFieldValue($query, 'field_day_date', '2018-07-08', '>');
    $query->condition($group);
    $group = $query_container->groupStandardByFieldValue($query, 'field_day_date', '2018-07-13', '<');
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
      $previous_nids = $this->queryDayByCodeByDateGreater($code_tid);
      $previous_entitys = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($previous_nids);

      if ($previous_entitys) {
        $today_volume = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($today_entity, 'field_day_volume');

        if ($today_volume) {

          foreach ($previous_entitys as $key => $row_entity) {
            $row_volume = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($row_entity, 'field_day_volume');

            if ($today_volume > $row_volume) {
              $output = TRUE;
              dpm($row_volume);
            }
            else {
              $output = FALSE;
              break;
            }
          }
        }
      }
    }

    return $output;
  }

}
