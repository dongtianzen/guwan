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
   \Drupal::getContainer()->get('baseinfo.querynode.service')->wrapperCmeNidsByUser();
 *
 */
class BaseinfoQueryNodeService extends FlexinfoQueryNodeService {

  /**
   *
   */
  public function queryWinNidsByCondition($ave_win = NULL, $ave_draw = NULL, $ave_loss = NULL, $diff_win = NULL, $diff_draw = NULL, $diff_loss = NULL, $tags = NULL) {
    $request_array = \Drupal::request()->query->all();

    if (isset($request_array['ave_win'])) {
      $ave_win = $request_array['ave_win'];
    }
    if (isset($request_array['ave_draw'])) {
      $ave_draw = $request_array['ave_draw'];
    }
    if (isset($request_array['ave_loss'])) {
      $ave_loss = $request_array['ave_loss'];
    }

    //
    if (isset($request_array['diff_win'])) {
      $diff_win = $request_array['diff_win'];
    }
    if (isset($request_array['diff_draw'])) {
      $diff_draw = $request_array['diff_draw'];
    }
    if (isset($request_array['diff_loss'])) {
      $diff_loss = $request_array['diff_loss'];
    }

    if (!$diff_win) {
      $diff_win = 0.001;
    }
    if (!$diff_draw) {
      $diff_draw = 2;
    }
    if (!$diff_loss) {
      $diff_loss = 20;
    }

    //
    if (isset($request_array['tags'])) {
      $tags = $request_array['tags'];
    }

    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('win');

    if ($ave_win) {
      $group = $query_container->groupStandardByFieldValue($query, 'field_win_ave_win', $ave_win - $diff_win, '>');
      $query->condition($group);

      $group = $query_container->groupStandardByFieldValue($query, 'field_win_ave_win', $ave_win + $diff_win, '<');
      $query->condition($group);
    }

    if ($ave_draw) {
      $group = $query_container->groupStandardByFieldValue($query, 'field_win_ave_draw', $ave_draw - $diff_draw, '>');
      $query->condition($group);

      $group = $query_container->groupStandardByFieldValue($query, 'field_win_ave_draw', $ave_draw + $diff_draw, '<');
      $query->condition($group);
    }

    if ($ave_loss) {
      $group = $query_container->groupStandardByFieldValue($query, 'field_win_ave_loss', $ave_loss - $diff_loss, '>');
      $query->condition($group);

      $group = $query_container->groupStandardByFieldValue($query, 'field_win_ave_loss', $ave_loss + $diff_loss, '<');
      $query->condition($group);
    }

    if ($tags) {
      $group = $query_container->groupStandardByFieldValue($query, 'field_win_tags.entity.name', $tags, 'IN');
      $query->condition($group);
    }

    // $query->sort('field_win_date', 'DESC');
    // $query->range(0, 2);
    $nids = $query_container->runQueryWithGroup($query);

    return $nids;
  }

  /**
   *
   */
  public function queryWinNodesByCondition($ave_win = NULL, $ave_draw = NULL, $ave_loss = NULL, $diff_win = NULL, $diff_draw = NULL, $diff_loss = NULL, $tags = NULL) {
    $nids = $this->queryWinNidsByCondition($ave_win, $ave_draw, $ave_loss, $diff_win, $diff_draw, $diff_loss, $tags);
    $nodes = \Drupal::entityManager()->getStorage('node')->loadMultiple($nids);

    return $nodes;
  }

}
