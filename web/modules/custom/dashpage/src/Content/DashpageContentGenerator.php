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
 $nids = $DashpageContentGenerator->demo(2392);

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
  public function standardTrendPage($section) {
    $output = '';
    $output .= '<div class="row margin-0">';
      $output .= '<div id="standard-google-map-wrapper">';
        $output .= '<div id="map-canvas">';
          $output .= 'Trend Table';
          $output .= '<br />';
          $output .= $this->getTrendContent($section);
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function standardQueryPage($section) {
    $output = '';
    $output .= '<div class="row margin-0">';
      $output .= '<div id="standard-google-map-wrapper">';
        $output .= '<div id="map-canvas">';
          $output .= 'Query Table';
          $output .= '<br />';
          $output .= $this->getQueryContent($section);
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function standardVolumeRatioPage($section) {
    $output = '';
    $output .= '<div class="row margin-0">';
      $output .= '<div id="standard-volume-ratio-page-wrapper">';
        $output .= '<div id="map-canvas">';
          $output .= 'Volume Ratio Table';
          $output .= '<br />';
          $output .= $this->getVolumeRatioContent($section);
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function getVolumeRatioContent($section) {
    $thead = range(0, 8);

    $output = '';
    $output .= '<table class="table table-striped">';
      $output .= '<thead>';
        $output .= '<tr>';
          $output .= '<th>';
            $output .= 'Date';
          $output .= '</th>';
          $output .= '<th>';
            $output .= 'Name';
          $output .= '</th>';
          $output .= '<th>';
            $output .= 'Code';
          $output .= '</th>';
          foreach ($thead as $key => $value) {
            $output .= '<th>';
              $output .= $value;
            $output .= '</th>';
          }
        $output .= '</tr>';
      $output .= '</thead>';
      $output .= '<tbody>';
        $output .= $this->getVolumeRatioTbodyRow($section);
      $output .= '</tbody>';
    $output .= '</table>';

    return $output;
  }

  /**
   *
   */
  public function getQueryContent($section) {
    $fenbu = $this->getFenbuHeads();

    $output = '';
    $output .= '<table class="table table-striped">';
      $output .= '<thead>';
        $output .= '<tr>';
          $output .= '<th>';
            $output .= 'Date';
          $output .= '</th>';
          $output .= '<th>';
            $output .= '上证指数';
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
        $output .= $this->getQueryContentTbodyRow($section);
      $output .= '</tbody>';
    $output .= '</table>';

    return $output;
  }

  /**
   *
   */
  public function getTrendContent($section) {
    $fenbu = $this->getFenbuHeads();

    $output = '';
    $output .= '<table class="table table-striped">';
      $output .= '<thead>';
        $output .= '<tr>';
          $output .= '<th>';
            $output .= 'Date';
          $output .= '</th>';
          $output .= '<th>';
            $output .= '上证指数';
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
        $output .= $this->getTrendContentTbodyRow($section);
      $output .= '</tbody>';
    $output .= '</table>';

    return $output;
  }

  /**
   *
   */
  public function getVolumeRatioTbodyRow($section) {
    $output = '';

    $query_date = '2018-08-03';

    $code_tids = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getTidsFromVidName($vid = 'code');

    $tids_array = [];
    foreach ($code_tids as $key => $code_tid) {
      $nids = \Drupal::getContainer()
        ->get('baseinfo.querynode.service')
        ->queryDayNidsByCodeByDate($code_tid, $today_date);

      if ($nids) {
        $checkPreviousDayResult = $this->compareVolumeRatio($nids[0], $code_tid, $start_date = '2018-08-03', $end_date = '2018-07-27');

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
  public function getQueryContentTbodyRow($section) {
    $output = '';

    $max_day = 2;
    if ($section) {
      $section_int = (int)$section;
      if (is_int($section_int) && $section_int  > 0 ) {
        $max_day = $section_int;
      }
    }

    $current_timestamp = \Drupal::time()->getCurrentTime();
    for ($i = 0; $i < $max_day; $i++) {
      $query_timestamp = $current_timestamp - ($i * 60 * 60 * 24);
      $query_date = \Drupal::service('date.formatter')->format($query_timestamp, 'html_date');

      $day_nids = \Drupal::getContainer()
        ->get('baseinfo.querynode.service')
        ->queryDayNidsByCodeByDate($code_tid = NULL, $query_date);

      if ($day_nids) {
        $fenbu = $this->queryCountPercentageByNode($day_nids);

        $output .= '<tr>';
          $output .= '<td>';
            $output .= $query_date;
          $output .= '</td>';
          $output .= '<td>';
            $output .= $this->getDayPercentChangeByCodeByDay($code_tid = 3610, $query_date) . '%';
          $output .= '</td>';
          $output .= '<td>';
            $output .= count($day_nids);
          $output .= '</td>';
          foreach ($fenbu as $key => $value) {
            $output .= '<td>';
              $output .= $value;
            $output .= '</td>';
          }
        $output .= '</tr>';
      }
    }

    return $output;
  }

  /**
   * 3610 is 'sz' shangzhengzhishu
   */
  public function getDayPercentChangeByCodeByDay($code_tid = 3610, $query_date = NULL) {
    $output = NULL;
    $nids = \Drupal::getContainer()
      ->get('baseinfo.querynode.service')
      ->queryDayNidsByCodeByDate($code_tid = 3610, $query_date);

    if ($nids) {
      $node = \Drupal::entityManager()->getStorage('node')->load($nids[0]);

      $output = \Drupal::getContainer()
        ->get('flexinfo.field.service')
        ->getFieldFirstValue($node, 'field_day_p_change');
    }

    return $output;
  }

  public function getTrendContentTbodyRow($section) {
    $output = '';

    $max_day = 2;
    if ($section) {
      $section_int = (int)$section;
      if (is_int($section_int) && $section_int  > 0 ) {
        $max_day = $section_int;
      }
    }

    $current_timestamp = \Drupal::time()->getCurrentTime();
    for ($i = 0; $i < $max_day; $i++) {
      $query_timestamp = $current_timestamp - ($i * 60 * 60 * 24);
      $query_date = \Drupal::service('date.formatter')->format($query_timestamp, 'html_date');

      $day_nodes = \Drupal::getContainer()
        ->get('baseinfo.querynode.service')
        ->queryDayNodesByCodeByDate($code_tid = NULL, $query_date);
      if ($day_nodes) {
        $fenbu = $this->calcPercentageByNode($day_nodes);

        $output .= '<tr>';
          $output .= '<td>';
            $output .= $query_date;
          $output .= '</td>';
          $output .= '<td>';
            $output .= $this->getDayPercentChangeByCodeByDay($code_tid = 3610, $query_date) . '%';
          $output .= '</td>';
          $output .= '<td>';
            $output .= count($day_nids);
          $output .= '</td>';
          foreach ($fenbu as $key => $value) {
            $output .= '<td>';
              $output .= $value;
            $output .= '</td>';
          }
        $output .= '</tr>';
      }
    }

    return $output;
  }

  /**
   *
   */
  public function getFenbuHeads() {
    $output = [
      'p9>' => 0,
      'p5>' => 0,
      'p0>' => 0,
      'p0<' => 0,
      'p5<' => 0,
      'p9<' => 0,
      'else' => 0,
    ];

    return $output;
  }

  /**
   *
   */
  public function queryCountPercentageByNode($day_nids) {
    $output = array();

    $fenbuRaw['z9'] = count(\Drupal::getContainer()->get('baseinfo.querynode.service')->queryDayNidsByNidsByFieldValue($day_nids, 'field_day_p_change', 9, '>'));
    $fenbuRaw['z5'] = count(\Drupal::getContainer()->get('baseinfo.querynode.service')->queryDayNidsByNidsByFieldValue($day_nids, 'field_day_p_change', 5, '>'));
    $fenbuRaw['z0'] = count(\Drupal::getContainer()->get('baseinfo.querynode.service')->queryDayNidsByNidsByFieldValue($day_nids, 'field_day_p_change', 0.000001, '>'));
    $fenbuRaw['f0'] = count(\Drupal::getContainer()->get('baseinfo.querynode.service')->queryDayNidsByNidsByFieldValue($day_nids, 'field_day_p_change', '-5', '>'));
    $fenbuRaw['f5'] = count(\Drupal::getContainer()->get('baseinfo.querynode.service')->queryDayNidsByNidsByFieldValue($day_nids, 'field_day_p_change', '-9', '>'));
    $fenbuRaw['f9'] = count(\Drupal::getContainer()->get('baseinfo.querynode.service')->queryDayNidsByNidsByFieldValue($day_nids, 'field_day_p_change', '-11', '>'));

    if ($day_nids) {
      $output['p9>'] = $fenbuRaw['z9'];
      $output['p5>'] = $fenbuRaw['z5'] - $fenbuRaw['z9'];
      $output['p0>'] = $fenbuRaw['z0'] - $fenbuRaw['z5'];
      $output['p0<'] = $fenbuRaw['f0'] - $fenbuRaw['z0'];
      $output['p5<'] = $fenbuRaw['f5'] - $fenbuRaw['f0'];
      $output['p9<'] = $fenbuRaw['f9'] - $fenbuRaw['f5'];
      $output['else'] = count($day_nids) - $fenbuRaw['f9'];
    }

    return $output;
  }

  /**
   *
   */
  public function calcPercentageByNode($day_nodes = array()) {
    $output = '';

    $fenbu = $this->getFenbuHeads();

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
      $nids = \Drupal::getContainer()
        ->get('baseinfo.querynode.service')
        ->queryDayNidsByCodeByDate($code_tid, $today_date);

      if ($nids) {
        $checkPreviousDayResult = $this->checkPreviousDay($nids[0], $code_tid, $start_date = '2018-07-08', $end_date = '2018-07-13');

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
  public function compareVolumeRatio($nid, $code_tid, $start_date = NULL, $end_date = NULL) {
    $output = FALSE;

    $today_entity = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

    if ($today_entity) {
      $ma5_volume = \Drupal::getContainer()
        ->get('flexinfo.field.service')
        ->getFieldFirstValue($today_entity, ' field_day_ma5');

      $ma10_volume = \Drupal::getContainer()
        ->get('flexinfo.field.service')
        ->getFieldFirstValue($today_entity, ' field_day_ma10');

      if ($today_volume) {

        foreach ($previous_entitys as $key => $row_entity) {
          $row_volume = \Drupal::getContainer()
            ->get('flexinfo.field.service')
            ->getFieldFirstValue($row_entity, ' field_day_ma5');

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

    return $output;
  }

  /**
   *
   */
  public function checkPreviousDay($nid, $code_tid, $start_date = NULL, $end_date = NULL) {
    $output = FALSE;

    $today_entity = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

    if ($today_entity) {

      $previous_entitys = \Drupal::getContainer()
        ->get('baseinfo.querynode.service')
        ->queryDayNodesByCodeByDateRange($code_tid, $start_date, $end_date);

      if ($previous_entitys) {
        $today_volume = \Drupal::getContainer()
          ->get('flexinfo.field.service')
          ->getFieldFirstValue($today_entity, 'field_day_volume');

        if ($today_volume) {

          foreach ($previous_entitys as $key => $row_entity) {
            $row_volume = \Drupal::getContainer()
              ->get('flexinfo.field.service')
              ->getFieldFirstValue($row_entity, 'field_day_volume');

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
