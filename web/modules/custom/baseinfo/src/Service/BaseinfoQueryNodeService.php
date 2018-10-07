<?php

/**
 * @file
 * Contains Drupal\baseinfo\Service\BaseinfoQueryNodeService.php.
 */

namespace Drupal\baseinfo\Service;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\Query\QueryFactory;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\flexinfo\Service\FlexinfoQueryNodeService;

/**
 * An example Service container.
   \Drupal::getContainer()->get('baseinfo.querynode.service')->demo();
 *
 */
class BaseinfoQueryNodeService extends FlexinfoQueryNodeService {

  /**
   *
   */
  public function queryDayNidsByNidsByFieldValue($nids = NULL, $field = 'field_day_p_change', $value = NULL, $operator = NULL) {
    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('day');

    if ($nids) {
      $group = $query_container->groupStandardByFieldValue($query, 'nid', $nids, 'IN');
      $query->condition($group);
    }

    if ($value) {
      $group = $query_container->groupStandardByFieldValue($query, $field, $value, $operator);
      $query->condition($group);
    }

    $nids = $query_container->runQueryWithGroup($query);

    return $nids;
  }

  /**
   *
   */
  public function queryDayNodesByCodeByDate($code_tid = NULL, $date = NULL, $operator = NULL) {
    $nids = $this->queryDayNidsByCodeByDate($code_tid, $date, $operator);
    $nodes = \Drupal::entityManager()->getStorage('node')->loadMultiple($nids);

    return $nodes;
  }

  /**
   *
   */
  public function queryDayNidsByCodeByDate($code_tid = NULL, $date = NULL, $operator = NULL) {
    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('day');

    if ($code_tid) {
      $group = $query_container->groupStandardByFieldValue($query, 'field_day_code', $code_tid);
      $query->condition($group);
    }

    if ($date) {
      $group = $query_container->groupStandardByFieldValue($query, 'field_day_date', $date, $operator);
      $query->condition($group);
    }

    $nids = $query_container->runQueryWithGroup($query);

    return $nids;
  }

  /**
   * @param $code_tid is tid, not code name like 600117
   *        $start_date = '2018-07-08'
   */
  public function queryDayNodesByCodeByDateRange($code_tid = NULL, $start_date = NULL, $end_date = NULL) {
    $nids = $this->queryDayNidsByCodeByDateRange($code_tid, $start_date, $end_date);
    $nodes = \Drupal::entityManager()->getStorage('node')->loadMultiple($nids);

    return $nodes;
  }

  /**
   * @param $code_tid is tid, not code name like 600117
   *        $start_date = '2018-07-08'
   */
  public function queryDayNidsByCodeByDateRange($code_tid = NULL, $start_date = NULL, $end_date = NULL) {
    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('day');

    $group = $query_container->groupStandardByFieldValue($query, 'field_day_code', $code_tid);
    $query->condition($group);

    $group = $query_container->groupStandardByFieldValue($query, 'field_day_date', $start_date, '>');
    $query->condition($group);
    $group = $query_container->groupStandardByFieldValue($query, 'field_day_date', $end_date, '<');
    $query->condition($group);

    $query->sort('field_day_date', 'DESC');
    // $query->range(0, 2);
    $nids = $query_container->runQueryWithGroup($query);

    return $nids;
  }

  /**
   * @param $code_tid is tid, not code name like 600117
   *        $start_date = '2018-07-08'
   */
  public function queryDayNodesByCodeByQueryRange($code_tid = NULL, $end_date = NULL, $range_num = 42) {
    $nids = $this->queryDayNidsByCodeByQueryRange($code_tid, $end_date, $range_num);
    $nodes = \Drupal::entityManager()->getStorage('node')->loadMultiple($nids);

    return $nodes;
  }

  /**
   * @param $code_tid is tid, not code name like 600117
   *        $start_date = '2018-07-08'
   */
  public function queryDayNidsByCodeByQueryRange($code_tid = NULL, $end_date = NULL, $range_num = 42) {
    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('day');

    $group = $query_container->groupStandardByFieldValue($query, 'field_day_code', $code_tid);
    $query->condition($group);

    $group = $query_container->groupStandardByFieldValue($query, 'field_day_date', $end_date, '<');
    $query->condition($group);

    $query->sort('field_day_date', 'DESC');
    $query->range(0, $range_num);
    $nids = $query_container->runQueryWithGroup($query);

    return $nids;
  }

}
