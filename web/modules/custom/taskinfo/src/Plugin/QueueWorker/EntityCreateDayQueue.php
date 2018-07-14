<?php

/**
 * @file
 * Contains Drupal\taskinfo\Plugin\QueueWorker\EntityCreateDayQueue
 */

namespace Drupal\taskinfo\Plugin\QueueWorker;
use Drupal\Core\Queue\QueueWorkerBase;

/**
 * Processes Tasks for custom.
 *
 * @QueueWorker(
 *   id = "entity_create_day_queue",
 *   title = @Translation("entity create day queue"),
 *   cron = {"time" = 10}
 * )
 */
class EntityCreateDayQueue extends QueueWorkerBase {
  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    $message = '222 ProcessItem ' . $data;
    \Drupal::logger('taskinfo')->notice($message);
  }
}
