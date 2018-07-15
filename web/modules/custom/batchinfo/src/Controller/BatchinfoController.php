<?php

/**
 * @file
 * Contains \Drupal\batchinfo\Controller\BatchinfoController.
 */

namespace Drupal\batchinfo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

use Drupal\batchinfo\Content\SyncJsonToNode;
use Drupal\batchinfo\Content\SyncJsonToTerm;

/**
 * Controller routines for theme example routes.
 */
class BatchinfoController extends ControllerBase {

  /**
   *
   */
  public function guideImportJson() {
    $markup = '<div class="panel panel-default">';
      $markup .= '<div class="panel-body">';
        $markup .= $this->t('Well Done Run Import JSON Task');
        $markup .= '<hr />';
      $markup .= '</div>';

      $markup .= '<div class="panel-body">';
        $markup .= '<div class="btn btn-default">';
          $markup .= \Drupal::l(t('Run Batch Import JSON to System'), Url::fromRoute('batchinfo.importJson.run'));
        $markup .= '</div>';
      $markup .= '</div>';

      $markup .= '<div class="panel-body">';
        $markup .= '<br />';
      $markup .= '</div>';

      $markup .= '<div class="panel-body">';
        $markup .= '<div class="btn btn-default">';
          $markup .= \Drupal::l(t('Run Batch Create Term to System'), Url::fromRoute('batchinfo.createTerm.run'));
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
  public function runImportJson() {
    $SyncJsonToNode = new SyncJsonToNode();
    $json_content = $SyncJsonToNode->getImportJsonContent();

    if ($json_content && is_array($json_content)) {
    }
    else {
      drupal_set_message('All of JSON had sync, Please check JSON file', 'warning');
    }

    $every_time_excute_max_number = 2;
    $chunk = array_chunk($json_content, $every_time_excute_max_number, TRUE);

    dpm('every time only excute - ' . $every_time_excute_max_number . ' - save node');

    $operations = [];
    foreach ($chunk as $piece) {
      $operations[] = array(
        '\Drupal\batchinfo\Content\RunImportJsonToNode::checkJsonAndCreateEntityNode',   // function name
        array($piece)
      );
    }

    $batch = array(
      'title' => t('Running batch...'),
      'operations' => $operations,
      'finished' => '\Drupal\batchinfo\Content\RunImportJsonToNode::finishedCallback',
    );

    batch_set($batch);

    $message = 'Run batch on RunImportJsonToNode()';
    \Drupal::logger('batchinfo')->notice($message);

    // You have to return batch_process('url') - set redirect page path,
    return batch_process('batchinfo/importjson/guide');
  }

  /**
   *
   */
  public function runCreateTerm() {
    $SyncJsonToTerm = new SyncJsonToTerm();
    $json_content = $SyncJsonToTerm->getTermJsonContent();

    if ($json_content && is_array($json_content)) {
    }
    else {
      drupal_set_message('All of JSON had sync, Please check JSON file', 'warning');
    }

    $every_time_excute_max_number = 5;
    $chunk = array_chunk($json_content, $every_time_excute_max_number, TRUE);

    dpm('every time only excute - ' . $every_time_excute_max_number . ' - save term');

    $operations = [];
    foreach ($chunk as $piece) {
      $operations[] = array(
        '\Drupal\batchinfo\Content\RunImportJsonToTerm::checkJsonAndCreateEntityTerm',   // function name
        array($piece)
      );
    }

    $batch = array(
      'title' => t('Running batch...'),
      'operations' => $operations,
      'finished' => '\Drupal\batchinfo\Content\RunImportJsonToTerm::finishedCallback',
    );

    batch_set($batch);

    $message = 'Run batch on RunImportJsonToTerm()';
    \Drupal::logger('batchinfo')->notice($message);

    // You have to return batch_process('url') - set redirect page path,
    return batch_process('batchinfo/importjson/guide');
  }

}
