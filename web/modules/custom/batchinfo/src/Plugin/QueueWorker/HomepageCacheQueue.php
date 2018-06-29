<?php
/**
 * @file
 * Contains \Drupal\batchinfo\Plugin\QueueWorker\HomepageCacheQueue.
 */

namespace Drupal\batchinfo\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;

use Drupal\batchinfo\Content\RunGenerateCache;

/**
 * Processes Tasks for batchinfo.
 *
 * @QueueWorker(
 *   id = "homepagecache_queue",
 *   title = @Translation("batchinfo task worker: Homepage Cache Queue"),
 *   cron = {"time" = 360}
 * )
 */
class HomepageCacheQueue extends QueueWorkerBase {
  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    $RunGenerateCache = new RunGenerateCache();
    RunGenerateCache::getPageCacheContent($data);
  }
}
