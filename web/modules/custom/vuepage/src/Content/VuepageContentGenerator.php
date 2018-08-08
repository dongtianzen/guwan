<?php

/**
 * @file
 */
namespace Drupal\vuepage\Content;

use Drupal\Core\Controller\ControllerBase;

/**
 * An example controller.
 $VuepageContentGenerator = new VuepageContentGenerator();
 $VuepageContentGenerator->demoPage();
 */
class VuepageContentGenerator extends ControllerBase {


  /**
   * {@inheritdoc}
   * @see https://vuejs.org/v2/examples/grid-component.html
   */
  public function gameListContent() {
    $content = NULL;
    $content .= $this->gameListContentScript();
    $content .= $this->gameListContentGrid();
    $content .= $this->gameListContentChartJs();

    return $content;
  }

  /**
   *
   */
  public function gameListContentChartJs() {
    $output = '
      <div class="appchartjs">
          {{ message }}
        <line-chart></line-chart>
      </div>
    ';

    return $output;
  }

  /**
   *
   */
  public function gameListContentGrid() {
    // <!-- <h3 class="vue-title">Grid Component Example - </h3> -->
    $output = '
      <div id="game-list-grid-wrapper">
        <div>
          <span class="float-right" style="float:right;">
            Total is {{totalRow}}
          </span>
          <form id="search">
            Search <input name="query" v-model="searchQuery">
          </form>
        </div>

        <game-list-grid-tag
          :data="gridData"
          :columns="gridColumns"
          :filter-key="searchQuery"
        >
        </game-list-grid-tag>
      </div>
    ';

    return $output;
  }

  /**
   */
  public function gameListContentScript() {
    $output = '
      <script type="text/x-template" id="grid-template">
        <table>
          <thead>
            <tr>
              {{filteredTotal}}
            </tr>
            <tr>
              <th v-for="key in columns"
                @click="sortBy(key)"
                :class="{ active: sortKey == key }">
                {{ key | capitalize }}
                <span class="arrow" :class="sortOrders[key] > 0 ? \'asc\' : \'dsc\'">
                </span>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="entry in filteredData">
              <td v-for="key in columns">
                {{entry[key]}}
              </td>
            </tr>
          </tbody>
        </table>
      </script>
    ';

    return $output;
  }

}
