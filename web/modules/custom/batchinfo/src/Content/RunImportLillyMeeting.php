<?php

/**
 * @file
 * Contains \Drupal\batchinfo\Content\RunImportJsonToNode.php
 */

namespace Drupal\batchinfo\Content;

use Drupal\node\Entity\Node;

use Drupal\batchinfo\Content\SyncLillyMeeting;

class RunImportJsonToNode {

  /**
   *
   */
  public static function checkAllianceMeetingGroupExist($piece, &$context) {
    foreach ($piece as $row) {
      self::checkAllianceMeetingNidExist($row);
    }
    $result = count($piece);

    $message = 'Running Batch checkAllianceMeetingNidExist() function ...';
    $context['message'] = $message;
    $context['results'][] = $result;

    // \Drupal::logger('batchinfo')->error($message);
  }

  /**
   *
   */
  public static function finishedCallback($success, $results, $operations) {
    // The 'success' parameter means no fatal PHP errors were detected. All
    // other error management should be handled using 'results'.
    if ($success) {
      $message = \Drupal::translation()->formatPlural(
        array_sum($results),
        'One node processed.', '@count nodes processed.'
      );
    }
    else {
      $message = t('Finished with an error.');
    }
    drupal_set_message($message);
  }

  /**
   *
   */
  public static function checkAllianceMeetingNidExist($data = array()) {
    $SyncLillyMeeting = new SyncLillyMeeting();
    $SyncLillyMeeting->checkBiHaveSameMeetingAndSaveToSheet($data);
  }

}
