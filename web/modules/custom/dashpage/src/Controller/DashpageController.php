<?php

/**
 * @file
 * Contains \Drupal\dashpage\Controller\DashpageController.
 */

namespace Drupal\dashpage\Controller;

use Drupal\Component\Utility\Timer;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormState;
use Drupal\Core\Url;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Drupal\dashpage\Content\DashpageContentGenerator;

/**
 * An example controller.
 */
class DashpageController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function standardRankPage($section) {
    $DashpageContentGenerator = new DashpageContentGenerator();
    $markup = $DashpageContentGenerator->standardRankPage($section);

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $markup,
      '#allowed_tags' => \Drupal::getContainer()->get('flexinfo.setting.service')->adminTag(),
    );

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function standardTrendPage($section) {
    $DashpageContentGenerator = new DashpageContentGenerator();
    $markup = $DashpageContentGenerator->standardTrendPage($section);

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $markup,
      '#allowed_tags' => \Drupal::getContainer()->get('flexinfo.setting.service')->adminTag(),
    );

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function standardQueryPage($section) {
    $DashpageContentGenerator = new DashpageContentGenerator();
    $markup = $DashpageContentGenerator->standardQueryPage($section);

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $markup,
      '#allowed_tags' => \Drupal::getContainer()->get('flexinfo.setting.service')->adminTag(),
    );

    return $build;
  }

  /**
   * {@inheritdoc}
   * redirect page to today date like /dashpage/volumeratio/2018-10-19
   */
  public function standardVolumeRatioRedirect() {
    $date_timestamp = \Drupal::time()->getCurrentTime() - (0 * 60 * 60 * 24);
    $date = \Drupal::service('date.formatter')->format($date_timestamp, 'html_date');

    $uri = '/dashpage/volumeratio/' . $date;
    $url = Url::fromUserInput($uri)->toString();

    return new RedirectResponse($url);
  }

  /**
   * {@inheritdoc}
   */
  public function standardVolumeRatioPage($section) {
    $name = 'time_query';
    Timer::start($name);

    $DashpageContentGenerator = new DashpageContentGenerator();
    $markup = $DashpageContentGenerator->standardVolumeRatioPage($section);

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $markup,
      '#allowed_tags' => \Drupal::getContainer()->get('flexinfo.setting.service')->adminTag(),
    );

    Timer::stop($name);
    dpm(Timer::read($name) . 'ms');

    return $build;
  }

}
