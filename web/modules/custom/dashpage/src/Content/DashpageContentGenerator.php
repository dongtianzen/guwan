<?php

/**
 * @file
 */
namespace Drupal\dashpage\Content;

use Drupal\Core\Controller\ControllerBase;

/**
 *
 use Drupal\dashpage\Content\DashpageContentGenerator;
 $DashpageContentGenerator = new DashpageContentGenerator();
 $nids = $DashpageContentGenerator->queryDayNidsByCodeByDateGreater(2392);

 dpm($nids);
 $previous_entitys = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);

 foreach ($previous_entitys as $key => $row_entity) {
   $row_volume = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($row_entity, 'field_day_volume');
   $row_date = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($row_entity, 'field_day_date');

   dpm($row_date);
   dpm($row_volume);
   dpm(' - ');
 }
 */

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
  public function standardTrendPage() {
    $output = '';
    $output .= '<div class="row margin-0">';
      $output .= '<div id="standard-google-map-wrapper">';
        $output .= '<div id="map-canvas">';
          $output .= 'Trend Table';
          $output .= '<br />';
          $output .= $this->getTrendContent();
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function getTrendContent() {
    $output = '';

    $query_date = '2018-07-17';
    $day_nids = $this->queryDayByCodeByDate($code_tid = NULL, $query_date);
    $day_nodes = \Drupal::entityManager()->getStorage('node')->loadMultiple($day_nids);

    $fenbu = $this->calcPercentageByNode($day_nodes);

    $output .= '<table class="table table-striped">';
      $output .= '<thead>';
        $output .= '<tr>';
          $output .= '<th>';
            $output .= 'Date';
          $output .= '</th>';
          $output .= '<th>';
            $output .= 'Total';
          $output .= '</th>';
          foreach ($fenbu as $key => $value) {
            $output .= '<th>';
              $output .= $key;
            $output .= '</th>';
          }
        $output .= '</tr>';
      $output .= '</thead>';
      $output .= '<tbody>';
        $output .= '<tr>';
          $output .= '<td>';
            $output .= $query_date;
          $output .= '</td>';
          $output .= '<td>';
            $output .= array_sum($fenbu);
          $output .= '</td>';
          foreach ($fenbu as $key => $value) {
            $output .= '<td>';
              $output .= $value;
            $output .= '</td>';
          }
        $output .= '</tr>';
      $output .= '</tbody>';
    $output .= '</table>';

    return $output;
  }

  /**
   *
   */
  public function calcPercentageByNode($day_nodes) {
    $output = '';

    $fenbu = [
      'p9>' => 0,
      'p5>' => 0,
      'p0>' => 0,
      'p0<' => 0,
      'p5<' => 0,
      'p9<' => 0,
      'else' => 0,
    ];


    if ($day_nodes) {
      foreach ($day_nodes as $key => $row) {
        $day_volume = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($row, 'field_day_volume');

        if ($day_volume > 0) {
          $day_p_change = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($row, 'field_day_p_change');

          if ($day_p_change > 9) {
            $fenbu['p9>']++;
          }
          elseif ($day_p_change > 5) {
            $fenbu['p5>']++;
          }
          elseif ($day_p_change > 0) {
            $fenbu['p0>']++;
          }
          elseif ($day_p_change > -5) {
            $fenbu['p0<']++;
          }
          elseif ($day_p_change > -9) {
            $fenbu['p5<']++;
          }
          elseif ($day_p_change > -11) {
            $fenbu['p9<']++;
          }
          else {
            $fenbu['else']++;
          }
        }
      }
    }

    return $fenbu;
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

    $num = 1;
    $output = ' ';
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($tids_array);
    foreach ($terms as $key => $term) {
      $output .= $num . ' ';
      $output .= $term->getName();
      $output .= ' ';
      $output .= \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($term, 'field_code_name');
      $output .= '<br />';

      $num++;
    }

    return $output;
  }

  /**
   *
   */
  public function queryDayByCodeByDate($code_tid = NULL, $date = NULL) {
    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('day');

    if ($code_tid) {
      $group = $query_container->groupStandardByFieldValue($query, 'field_day_code', $code_tid);
      $query->condition($group);
    }

    if ($date) {
      $group = $query_container->groupStandardByFieldValue($query, 'field_day_date', $date);
      $query->condition($group);
    }

    $nids = $query_container->runQueryWithGroup($query);

    return $nids;
  }

  /**
   * @param $code_tid is tid, not code name like 600117
   */
  public function queryDayNidsByCodeByDateGreater($code_tid = NULL, $date = NULL) {
    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('day');

    $group = $query_container->groupStandardByFieldValue($query, 'field_day_code', $code_tid);
    $query->condition($group);

    $group = $query_container->groupStandardByFieldValue($query, 'field_day_date', '2018-07-08', '>');
    $query->condition($group);
    $group = $query_container->groupStandardByFieldValue($query, 'field_day_date', '2018-07-13', '<');
    $query->condition($group);

    $query->sort('field_day_date', 'DESC');
    $query->range(0, 2);
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
      $previous_nids = $this->queryDayNidsByCodeByDateGreater($code_tid);
      $previous_entitys = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($previous_nids);

      if ($previous_entitys) {
        $today_volume = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($today_entity, 'field_day_volume');

        if ($today_volume) {

          foreach ($previous_entitys as $key => $row_entity) {
            $row_volume = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($row_entity, 'field_day_volume');

            if ($today_volume > $row_volume) {
              $output = TRUE;
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
