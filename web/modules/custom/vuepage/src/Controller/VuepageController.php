<?php

/**
 * @file
 * Contains \Drupal\vuepage\Controller\VuepageController.
 */

namespace Drupal\vuepage\Controller;

use Drupal\Component\Utility\Timer;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Drupal\vuepage\Content\VuepageContentGenerator;



/**
 * An example controller.
 */
class VuepageController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function basicContent() {
    $content = '
      <div class="container bootstrap-vue-example-wrapper">
        <div id="appPage">
          <h5>Bootstrap Vue Example</h5>
          <span> Hello {{ name }}! </span>
          <br />
          <b-alert show> Hello {{ name }}! </b-alert>
        </div>
      </div>

      <hr />
      <div class="container vue-example-wrapper">
        <div id="vueAppPage" class="container">
          <h5>Vue Example</h5>
          <div class="text-primary">
            <span class="text-primary">show vue message - {{message}}</span>
          </div>
        </div>
      </div>
    ';

    return $content;
  }

  /**
   * {@inheritdoc}
   */
  public function vuetable() {
    $content = '
      <h3 class="vue-title">Vuetable-2-bootstrap - https://codepen.io/ratiw/pen/GmJayw</h3>
      <div id="app">
        <div id="table-wrapper" class="ui container">
          <h2>
            <strong>Vuetable-2</strong>
            <span>with Bootstrap 3</span>
          </h2>

          <vuetable ref="vuetable"
            api-url="https://vuetable.ratiw.net/api/users"
            :fields="fields"
            :sort-order="sortOrder"
            :css="css.table"
            pagination-path=""
            :per-page="10"
            @vuetable:pagination-data="onPaginationData"
            @vuetable:loading="onLoading"
            @vuetable:loaded="onLoaded"
          >
          </vuetable>
          <vuetable-pagination ref="pagination"
            :css="css.pagination"
            @vuetable-pagination:change-page="onChangePage"
          >
          </vuetable-pagination>
        </div>
      </div>
    ';

    return $content;
  }

  /**
   * {@inheritdoc}
   */
  public function vuePage() {
    $content = $this->basicContent();
    $content = $this->vuetable();

    $build = array(
      '#children' => $content,
      '#attached' => array(
        'library' => array(
          'vuepage/vue',
          'vuepage/babel-polyfill',
          'vuepage/bootstrap',
          'vuepage/bootstrap-vue',
          'vuepage/axios',
          'vuepage/vuetable-2',
          'vuepage/vue_report',
          'vuepage/vue_table_js',
        )
      ),
    );

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function gameList() {
    $name = 'time_one';
    Timer::start($name);

    $VuepageContentGenerator = new VuepageContentGenerator();

    $build = array(
      '#children' => $VuepageContentGenerator->gameListContent(),
      '#attached' => array(
        'library' => array(
          'vuepage/vue',
          'vuepage/babel-polyfill',
          'vuepage/bootstrap',
          'vuepage/bootstrap-vue',
          'vuepage/chart.js',
          'vuepage/axios',
          'vuepage/vue-chartjs',
          'vuepage/vue_game_list',
        )
      ),
    );

    Timer::stop($name);
    dpm(Timer::read($name) . 'ms');

    return $build;
  }

}
