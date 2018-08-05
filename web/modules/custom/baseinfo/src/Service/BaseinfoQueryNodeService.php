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
  public function queryDayNodesByCodeByDate($code_tid = NULL, $date = NULL) {
    $nids = $this->queryDayNidsByCodeByDate($code_tid, $date);
    $nodes = \Drupal::entityManager()->getStorage('node')->loadMultiple($nids);

    return $nodes;
  }

  /**
   *
   */
  public function queryDayNidsByCodeByDate($code_tid = NULL, $date = NULL) {
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
  public function queryDayNodesByCodeByDateGreater($code_tid = NULL, $date = NULL) {
    $nids = $this->queryDayNidsByCodeByDateGreater($code_tid, $date);
    $nodes = \Drupal::entityManager()->getStorage('node')->loadMultiple($nids);

    return $nodes;
  }

  /**
   *
   */
  public function queryDayNidsByCodeByDateGreater($code_tid = NULL, $start_date = NULL, $end_date = NULL) {
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

}
