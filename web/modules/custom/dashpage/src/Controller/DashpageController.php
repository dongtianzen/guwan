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
    $name = 'time_one';
    Timer::start($name);

    $DashpageContentGenerator = new DashpageContentGenerator();
    $markup = $DashpageContentGenerator->standardTrendPage($section);

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

  /**
   * {@inheritdoc}
   */
  public function standardQueryPage($section) {
    $name = 'time_query';
    Timer::start($name);

    $DashpageContentGenerator = new DashpageContentGenerator();
    $markup = $DashpageContentGenerator->standardQueryPage($section);

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
