<?php

/**
 * @file
 * Contains \Drupal\batchinfo\Controller\BatchinfoController.
 */

namespace Drupal\batchinfo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

use Drupal\batchinfo\Content\SyncLillyMeeting;
use Drupal\dashpage\Content\DashpageCacheContent;

/**
 * Controller routines for theme example routes.
 */
class BatchinfoController extends ControllerBase {

  /**
   *
   */
  public function guideImportLillyMeeting() {
    $import_meeting_internal_url = Url::fromRoute('batchinfo.importLillyMeeting.run');
    $import_pool_internal_url = Url::fromRoute('batchinfo.importLillyPool.run');

    $markup = '<div class="panel panel-default">';
      $markup .= '<div class="panel-body">';
        $markup .= $this->t('Well Done Run Import Lilly Meeting Task');
        $markup .= '<hr />';
      $markup .= '</div>';

      $markup .= '<div class="panel-body">';
        $markup .= '<div class="btn btn-default">';
          $markup .= \Drupal::l(t('Run Batch Import Lilly Meeting'), $import_meeting_internal_url);
        $markup .= '</div>';
      $markup .= '</div>';

      $markup .= '<div class="panel-body">';
        $markup .= '<div class="btn btn-default">';
          $markup .= \Drupal::l(t('Run Batch Import Lilly Pool'), $import_pool_internal_url);
        $markup .= '</div>';
      $markup .= '</div>';
    $markup .= '</div>';

    $build = array(
      '#type' => 'markup',
      '#markup' => $markup,
      '#allowed_tags' => \Drupal::getContainer()->get('flexinfo.setting.service')->adminTag(),
    );

    return $build;
  }

  /**
   *
   */
  public function runImportLillyMeeting() {
    $SyncLillyMeeting = new SyncLillyMeeting();
    $lilly_meeting_nids = $SyncLillyMeeting->filterLillyAllianceMeetingNids();

    if ($lilly_meeting_nids && is_array($lilly_meeting_nids)) {
    }
    else {
      drupal_set_message('All of meeting had sync, Please check Lilly meeting nids', 'warning');
    }

    $every_time_excute_max_number = 1;
    $chunk = array_chunk($lilly_meeting_nids, $every_time_excute_max_number, TRUE);

    dpm('after filter total have - ' . count($lilly_meeting_nids) . ' - meetings');
    dpm('every time only excute - ' . $every_time_excute_max_number . ' - meetings');

    $operations = [];
    foreach ($chunk as $piece) {
      $operations[] = array(
        '\Drupal\batchinfo\Content\RunImportLillyMeeting::checkAllianceMeetingGroupExist',   // function name
        array($piece)
      );
    }

    $batch = array(
      'title' => t('Running batch...'),
      'operations' => $operations,
      'finished' => '\Drupal\batchinfo\Content\RunImportLillyMeeting::finishedCallback',
    );

    batch_set($batch);

    $message = 'Run batch on runImportLillyMeeting()';
    \Drupal::logger('batchinfo')->notice($message);


    return batch_process('batchinfo/importlillymeeting/guide');        // You have to return batch_process('url') - set redirect page path,
  }

}
