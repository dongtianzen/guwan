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
   * @param $query_date like '2018-08-06';
   */
  public function standardVolumeRatioPage($query_date) {
    $day_nodes = \Drupal::getContainer()
      ->get('baseinfo.querynode.service')
      ->queryDayNodesByCodeByDate($code_tid = NULL, $query_date);

    $output = '';
    $output .= '<div class="row margin-0">';
      $output .= '<div id="standard-volume-ratio-page-wrapper">';
        $output .= '<div id="map-canvas">';
          $output .= 'Volume Ratio Table Date - ' . $query_date . ' Total - ' . count($day_nodes);
          $output .= '<br />';
          $output .= $this->getVolumeRatioContent($day_nodes);
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function getVolumeRatioContent($day_nodes) {
    $output = '';
    $output .= '<table class="table table-striped">';
      $output .= '<thead>';
        $output .= '<tr>';
          $output .= '<th>';
            $output .= 'Num';
          $output .= '</th>';
          $output .= '<th>';
            $output .= 'Code';
          $output .= '</th>';
          $output .= '<th>';
            $output .= 'Name';
          $output .= '</th>';
        $output .= '</tr>';
      $output .= '</thead>';
      $output .= '<tbody>';
        $output .= $this->getVolumeRatioTbodyRow($day_nodes);
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
  public function getTidsByCheckPriceRatio($day_nodes) {
    $tids_array = [];
    foreach ($day_nodes as $key => $day_node) {
      $checkPriceRation = $this->comparePriceRatio($day_node, 97, 100);

      if ($checkPriceRation) {

        $checkVolumeRation = $this->compareVolumeRatio($day_node, 97, 100);
        if ($checkVolumeRation) {
          $tids_array[] = \Drupal::getContainer()
            ->get('flexinfo.field.service')
            ->getFieldFirstTargetId($day_node, 'field_day_code');
        }
      }
    }

    return $tids_array;
  }

  /**
   * 统计5~7个交易日macd dif（白线）数值 从小于macd（黄线）到趋近或重合macd（黄线）
   * @param $start_date = '2018-07-08', $end_date = '2018-07-13'
   */
  public function compareMacd($tids_array = array(), $fastPeriod = 12, $slowPeriod = 26, $signalPeriod = 9, $dayLength = 42) {
    $output = array();
dpm($tids_array);
    foreach ($tids_array as $tid) {
      for ($i = 0; $i < 7; $i++) {
        $end_date_timestamp = \Drupal::time()->getCurrentTime() - ($i * 60 * 60 * 24);
        $end_date = \Drupal::service('date.formatter')->format($end_date_timestamp, 'html_date');

        $start_date_timestamp = $end_date_timestamp - ($dayLength * 60 * 60 * 24);
        $start_date = \Drupal::service('date.formatter')->format($start_date_timestamp, 'html_date');

        $close_prices = $this->getClosePriceByDateRange($tid, $start_date, $end_date);
        $traderMacdValue = $this->getTraderMacdValue($close_prices, $fastPeriod, $slowPeriod, $signalPeriod);

        if (isset($traderMacdValue[2]) && $traderMacdValue[2] <= 0.8) {

        }
        else {
          break 2;
        }
      }

      $output[] = $tid;
    }

    return $output;
  }

  /**
   * @param $start_date = '2018-07-08', $end_date = '2018-07-13'
   * @return array
   */
  public function getClosePriceByDateRange($tid = NULL, $start_date = NULL, $end_date = NULL) {
    $previous_entitys = \Drupal::getContainer()
      ->get('baseinfo.querynode.service')
      ->queryDayNodesByCodeByDateRange($tid, $start_date, $end_date);

    $output = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldFirstValueCollection($previous_entitys, 'field_day_close');

    return $output;
  }

  /**
   * @param $close_prices = array(12.33, 15.21, 14.54, .....) count($close_prices) > 33;
   * @return
      index [0]: MACD values
      index [1]: Signal values
      index [2]: Divergence values
   */
  public function getTraderMacdValue($close_prices = array(), $fastPeriod = 12, $slowPeriod = 26, $signalPeriod = 9) {
    $output = FALSE;

    if ($close_prices && count($close_prices) > 33) {
      $output = trader_macd($close_prices, $fastPeriod, $slowPeriod, $signalPeriod);
    }

    return $output;
  }

  /**
   *
   */
  public function getVolumeRatioTbodyRow($day_nodes) {
    $output = '';

    $tids_array = $this->getTidsByCheckPriceRatio($day_nodes);
    $tids_array = $this->compareMacd($tids_array);

    $num = 1;
    if ($tids_array) {
      $terms = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadMultiple($tids_array);

      foreach ($terms as $key => $term) {
        $output .= '<tr>';
          $output .= '<td>';
            $output .= $num;
          $output .= '</td>';
          $output .= '<td>';
            $output .= $term->getName();
          $output .= '</td>';
          $output .= '<td>';
            $output .= \Drupal::getContainer()
              ->get('flexinfo.field.service')
              ->getFieldFirstValue($term, 'field_code_name');
          $output .= '</td>';
        $output .= '</tr>';

        $num++;
      }
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

    for ($i = 0; $i < $max_day; $i++) {
      $query_timestamp = \Drupal::time()->getCurrentTime() - ($i * 60 * 60 * 24);
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

  /**
   *
   */
  public function getTrendContentTbodyRow($section) {
    $output = '';

    $max_day = 2;
    if ($section) {
      $section_int = (int)$section;
      if (is_int($section_int) && $section_int  > 0 ) {
        $max_day = $section_int;
      }
    }

    for ($i = 0; $i < $max_day; $i++) {
      $query_timestamp = \Drupal::time()->getCurrentTime() - ($i * 60 * 60 * 24);
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
            $output .= count($day_nodes);
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
  public function comparePriceRatio($entity, $min = 90, $max = 110) {
    $output = FALSE;

    if ($entity) {
      $price_ma5 = \Drupal::getContainer()
        ->get('flexinfo.field.service')
        ->getFieldFirstValue($entity, 'field_day_ma5');

      $price_ma10 = \Drupal::getContainer()
        ->get('flexinfo.field.service')
        ->getFieldFirstValue($entity, 'field_day_ma10');

      if ($price_ma10) {
        $price_ratio = \Drupal::getContainer()
          ->get('flexinfo.calc.service')
          ->getPercentage($price_ma5, $price_ma10);

        if ($min < $price_ratio && $price_ratio < $max) {
          $output = TRUE;
        }
        else {
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function compareVolumeRatio($entity, $min = 90, $max = 110) {
    $output = FALSE;

    if ($entity) {
      $volume_ma5 = \Drupal::getContainer()
        ->get('flexinfo.field.service')
        ->getFieldFirstValue($entity, 'field_day_v_ma5');

      $volume_ma10 = \Drupal::getContainer()
        ->get('flexinfo.field.service')
        ->getFieldFirstValue($entity, 'field_day_v_ma10');

      if ($volume_ma10) {
        $volume_ratio = \Drupal::getContainer()
          ->get('flexinfo.calc.service')
          ->getPercentage($volume_ma5, $volume_ma10);

        if ($min < $volume_ratio && $volume_ratio < $max) {
          $output = TRUE;
        }
        else {
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
