<?php

/**
 * @file
 */

namespace Drupal\flexpage\Content;

use Drupal\Core\Controller\ControllerBase;

use Drupal\flexpage\Content\FlexpageObjectContent;

/**
 * An example controller.
 $FlexpageBlockGenerator = new FlexpageBlockGenerator();
 $FlexpageBlockGenerator->contentBlockCharts();
 */
class FlexpageBlockGenerator extends ControllerBase {

  /*
   *
   */
  public function contentRenderHeader() {
    $output = '';
    $output .= '<div class="panel-header block-header {{block.top.class}}">';
      $output .= '<span ng-bind-html="$sce.trustAsHtml(block.top.value)">';
        $output .= '{{block.top.value}}';
      $output .= '</span>';
      $output .= '<md-menu>';
        $output .= '<span id="save-charts-{{block.blockId}}" ng-click="openMenu($mdMenu.open, $event)"
          class="fa fa-angle-down float-right font-size-14 padding-12 cursor-pointer"></span>';
        $output .= '<md-menu-content width="3">';
          $output .= '<md-button ng-click="saveAsPng(\'charts\', block.blockId)"> ';
            $output .= 'Save PNG';
          $output .= '</md-button>';
        $output .= '</md-menu-content>';
      $output .= '</md-menu>';
    $output .= '</div>';

    return $output;
  }

  /*
   * Content Render Charts
   */
  public function contentRenderCharts() {
    $output = '';
    $output .= '<div class="content-render-chart-wrapper">';
      $output .= '<div ng-bind-html="$sce.trustAsHtml(block.middle.middleTop)">';
        $output .= '{{block.middle.middleTop}}';
      $output .= '</div>';

      // middleMiddleLeft
      $output .= '<div class="{{block.middle.middleMiddle.middleMiddleLeftClass}}">';
        $output .= '<span ng-bind-html="$sce.trustAsHtml(block.middle.middleMiddle.middleMiddleLeft)">';
          $output .= '{{block.middle.middleMiddle.middleMiddleLeft}}';
        $output .= '</span>';
      $output .= '</div>';

      // with middleMiddleRight
      $output .= '<div ng-if="block.middle.middleMiddle.middleMiddleRight.length">';
        // $output .= '<div ng-if="block.middle.middleMiddle.gridColumn.length">';
        $output .= '<div ng-switch="block.middle.middleMiddle.gridColumn">';

          // 75
          $output .= '<div ng-switch-when="75">';
            $output .= '<div class="col-md-7">';
              $output .= '<canvas width="600" height="700" flexxiachartjs
                options="{{block.middle.middleMiddle.middleMiddleMiddle.chartOptions}}"
                type="{{block.middle.middleMiddle.middleMiddleMiddle.chartType}}"
                id="{{block.middle.middleMiddle.middleMiddleMiddle.chartId}}"
                data="{{block.middle.middleMiddle.middleMiddleMiddle.chartData}}"></canvas>';
            $output .= '</div>';
            $output .= '<div class="col-md-5">';
              $output .= '<span ng-bind-html="$sce.trustAsHtml(block.middle.middleMiddle.middleMiddleRight)">';
                $output .= '{{block.middle.middleMiddle.middleMiddleRight}}';
              $output .= '</span>';
            $output .= '</div>';
          $output .= '</div>';

          // 84
          $output .= '<div ng-switch-when="84">';
            $output .= '<div class="col-md-8">';
              $output .= '<canvas width="600" height="700" flexxiachartjs
                options="{{block.middle.middleMiddle.middleMiddleMiddle.chartOptions}}"
                type="{{block.middle.middleMiddle.middleMiddleMiddle.chartType}}"
                id="{{block.middle.middleMiddle.middleMiddleMiddle.chartId}}"
                data="{{block.middle.middleMiddle.middleMiddleMiddle.chartData}}"></canvas>';
            $output .= '</div>';
            $output .= '<div class="col-md-4">';
              $output .= '<span ng-bind-html="$sce.trustAsHtml(block.middle.middleMiddle.middleMiddleRight)">';
                $output .= '{{block.middle.middleMiddle.middleMiddleRight}}';
              $output .= '</span>';
            $output .= '</div>';
          $output .= '</div>';

          // $output .= '<div ng-if="block.middle.middleMiddle.gridColumn.length < 1">';
          $output .= '<div ng-switch-default>';
            $output .= '<div class="col-md-9">';
              $output .= '<canvas width="600" height="700" flexxiachartjs
                options="{{block.middle.middleMiddle.middleMiddleMiddle.chartOptions}}"
                type="{{block.middle.middleMiddle.middleMiddleMiddle.chartType}}"
                id="{{block.middle.middleMiddle.middleMiddleMiddle.chartId}}"
                data="{{block.middle.middleMiddle.middleMiddleMiddle.chartData}}"></canvas>';
            $output .= '</div>';
            $output .= '<div class="col-md-3">';
              $output .= '<span ng-bind-html="$sce.trustAsHtml(block.middle.middleMiddle.middleMiddleRight)">';
                $output .= '{{block.middle.middleMiddle.middleMiddleRight}}';
              $output .= '</span>';
            $output .= '</div>';
          $output .= '</div>';

        $output .= '</div>';
      $output .= '</div>';

      // without middleMiddleRight
      $output .= '<div ng-if="block.middle.middleMiddle.middleMiddleRight.length < 1" class="{{block.middle.middleMiddle.middleMiddleMiddleClass}}">';
        $output .= '<canvas width="600" height="700" flexxiachartjs
          options="{{block.middle.middleMiddle.middleMiddleMiddle.chartOptions}}"
          type="{{block.middle.middleMiddle.middleMiddleMiddle.chartType}}"
          id="{{block.middle.middleMiddle.middleMiddleMiddle.chartId}}"
          data="{{block.middle.middleMiddle.middleMiddleMiddle.chartData}}">';
        $output .= '</canvas>';
      $output .= '</div>';

      // $output .= '</div>';
      // $output .= '<div ng-if="block.middle.middleMiddle.middleMiddleMiddle.chartData.length < 1">';
        // $output .= '<div class="no-data-template">N.A</div>';
      // $output .= '</div>';

      $output .= '<div ng-bind-html="$sce.trustAsHtml(block.middle.middleBottom)">';
        $output .= '{{block.middle.middleBottom}}';
      $output .= '</div>';
    $output .= '</div>';

    $output .= '<div ng-if="block.bottom.enable">';
      $output .= '<md-content>';
        $output .= $this->contentRenderBottom();
      $output .= '</md-content>';
    $output .= '</div>';

    return $output;
  }

  /*
   * Content Elements comments
   */
  public function contentRenderComments() {
    $output = '';
    $output .= '<div class="panel-body bg-ffffff">';
      $output .= '<md-content class="padding-bottom-12">';
        $output .= '<span ng-bind-html="$sce.trustAsHtml(block.middle.value)">';
          $output .= '{{block.middle.value}}';
        $output .= '</span>';
      $output .= '</md-content>';
    $output .= '</div>';

    return $output;
  }

  /*
   * Content Elements comments
   */
  public function contentRenderGoogleMap() {
    $output = '<div id="map" class="google-map-wrapper"></div>';
    return $output;
  }

  /*
   * Content Render Table
   */
  public function contentRenderTable() {
    $output = '';
    $output .= '<div id="popUpParent" class="content-render-table-wrapper">';

      // Middle Top
      $output .= '<div ng-bind-html="$sce.trustAsHtml(block.middle.middleTop)">';
        $output .= '{{block.middle.middleTop}}';
      $output .= '</div>';

      // Middle Middle Left
      $output .= '<div class="{{block.middle.middleMiddle.middleMiddleLeftClass}}">';
        $output .= '<span ng-bind-html="$sce.trustAsHtml(block.middle.middleMiddle.middleMiddleLeft)"></span>';
      $output .= '</div>';

      // Middle Middle content
      $output .= '<div class="{{block.middle.middleMiddle.middleMiddleMiddleClass}}">';
        $output .= '<div data-ng-controller="AngularDataTables" class="row margin-0 margin-top-16">';
            $output .= '<table datatable="ng" dt-options="dtOptionsCommonTables" class="stripe responsive no-wrap">';
              $output .= '<thead>';
              $output .= '<tr>';
                $output .= '<th data-ng-repeat="tablehead in block.middle.middleMiddle.middleMiddleMiddle.value.thead[0]">';
                  $output .= '{{tablehead}}';
                $output .= '</th>';
              $output .= '</tr>';
              $output .= '</thead>';
              $output .= '<tbody>';
                $output .= '<tr data-ng-repeat="tableRow in block.middle.middleMiddle.middleMiddleMiddle.value.tbody">';
                  $output .= '<td data-ng-repeat="tableCell in tableRow track by $index">';
                    $output .= '<span compile="tableCell">';
                      $output .= '{{tableCell}}';
                    $output .= '</span>';
                  $output .= '</td>';
                $output .= '</tr>';
              $output .= '</tbody>';
            $output .= '</table>';
        $output .= '</div>';
      $output .= '</div>';

      // Middle Middle Right
      $output .= '<div class="{{block.middle.middleMiddle.middleMiddleRightClass}}">';
        $output .= '<span ng-bind-html="$sce.trustAsHtml(block.middle.middleMiddle.middleMiddleRight)">';
          $output .= '{{block.middle.middleMiddle.middleMiddleRight}}';
        $output .= '</span>';
      $output .= '</div>';

      // Middle Bottom
      $output .= '<div class="col-sm-12" ng-bind-html="$sce.trustAsHtml(block.middle.middleBottom)">';
        $output .= '{{block.middle.middleBottom}}';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   * Content Render Table
   */
  public function contentRenderMildderTable() {
    $output = '';
    $output .= '<div class="content-render-table-wrapper">';

      // Middle Top
      $output .= '<div ng-bind-html="$sce.trustAsHtml(block.middle.middleTop)">';
        $output .= '{{block.middle.middleTop}}';
      $output .= '</div>';

      // Middle Middle Left
      $output .= '<div class="{{block.middle.middleMiddle.middleMiddleLeftClass}}">';
        $output .= '<span ng-bind-html="$sce.trustAsHtml(block.middle.middleMiddle.middleMiddleLeft)"></span>';
      $output .= '</div>';

      // Middle Middle content
      $output .= '<div class="{{block.middle.middleMiddle.middleMiddleMiddleClass}}">';
        $output .= '<div data-ng-controller="AngularDataTables" class="row margin-0 margin-top-16">';
          $output .= '<table id="mildder-table" datatable="ng" dt-options="dtOptionsMildderTable" dt-instance="dtIntanceCallback" class="stripe responsive no-wrap" width="100%">';
            $output .= '<thead>';
            $output .= '<tr>';
              $output .= '<th data-ng-repeat="tablehead in block.middle.middleMiddle.middleMiddleMiddle.value.thead[0]">';
                $output .= '{{tablehead}}';
              $output .= '</th>';
            $output .= '</tr>';
            $output .= '</thead>';
            $output .= '<tbody>';
              $output .= '<tr data-ng-repeat="tableRow in block.middle.middleMiddle.middleMiddleMiddle.value.tbody">';
                $output .= '<td data-ng-repeat="tableCell in tableRow track by $index">';
                  $output .= '<span compile="tableCell">';
                    $output .= '{{tableCell}}';
                  $output .= '</span>';
                $output .= '</td>';
              $output .= '</tr>';
            $output .= '</tbody>';
          $output .= '</table>';
        $output .= '</div>';
      $output .= '</div>';

      // Middle Middle Right
      $output .= '<div class="{{block.middle.middleMiddle.middleMiddleRightClass}}">';
        $output .= '<span ng-bind-html="$sce.trustAsHtml(block.middle.middleMiddle.middleMiddleRight)">';
          $output .= '{{block.middle.middleMiddle.middleMiddleRight}}';
        $output .= '</span>';
      $output .= '</div>';

      // Middle Bottom
      $output .= '<div class="col-sm-12" ng-bind-html="$sce.trustAsHtml(block.middle.middleBottom)">';
        $output .= '{{block.middle.middleBottom}}';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   * Content Render Table
   */
  public function phpTableSortSettings() {
    $path_args = \Drupal::getContainer()->get('flexinfo.setting.service')->getCurrentPathArgs();

    $table_sort_column = 0;
    $table_sort_type = 'asc'; // asc or desc

    if (strtolower($path_args[2]) == 'meeting') {
      $table_sort_column = 2;
      $table_sort_type = 'desc';
    }
    elseif (strtolower($path_args[2]) == 'meetingsummary') {
      $table_sort_column = 1;
      $table_sort_type = 'desc';
    }

    $sort_settings['column'] = $table_sort_column;
    $sort_settings['type'] = $table_sort_type;

    return $sort_settings;
  }

  /**
   * Content Render Table
   */
  public function contentRenderPhpTable() {
    $table_content = '';

    $path_args = \Drupal::getContainer()->get('flexinfo.setting.service')->getCurrentPathArgs();

    if (isset($path_args[4])) {
      if ($path_args[1] == 'flexpage' && $path_args[2] == 'webinar' && strlen($path_args[4]) > 0) {
        if (2 > 1) {
          $FlexpageObjectContent = new FlexpageObjectContent();
          $table_content = $FlexpageObjectContent->tableWebinar();
        }
      }
    }

    // disable for debug
    // $output = $this->contentRenderPhpTableTemplate($table_content);

    $output = '';
    return $output;
  }

  /**
   * Content Render Table
   */
  public function contentRenderPhpTableTemplate($table_content) {
    $table_sort_settings = $this->phpTableSortSettings();

    $output = '';
    $output .= '<div id="content-render-php-table-wrapper" data-table-sort-column="' . $table_sort_settings['column'] . '"
      data-table-sort-type="' . $table_sort_settings['type'] . '">';

      // Middle Top
      $output .= '<div ng-bind-html="$sce.trustAsHtml(block.middle.middleTop)">';
        $output .= '{{block.middle.middleTop}}';
      $output .= '</div>';

      // Middle Middle Left
      $output .= '<div class="{{block.middle.middleMiddle.middleMiddleLeftClass}}">';
        $output .= '<span ng-bind-html="$sce.trustAsHtml(block.middle.middleMiddle.middleMiddleLeft)"></span>';
      $output .= '</div>';

      // Middle Middle content
      $output .= '<div class="{{block.middle.middleMiddle.middleMiddleMiddleClass}}">';
        $output .= '<div data-ng-controller="AngularDataTables" class="row margin-0 margin-top-16">';
          $output .= '<table id="php-table-list" class="stripe responsive no-wrap display-none">';
            $output .= '<thead>';
              $output .= '<tr>';
              foreach ($table_content as $array_key => $array_content) {
                foreach ($array_content as $key => $value) {
                  $output .= '<th>' . $key . '</th>';
                }
                break;
              }
              $output .= '</tr>';
            $output .= '</thead>';

            $output .= '<tbody>';
            foreach ($table_content as $array_key => $array_content) {
              $output .= '<tr>';
                foreach ($array_content as $key => $value) {
                  $output .= '<td>' . $value . '</td>';
                }
              $output .= '</tr>';
            }
            $output .= '</tbody>';
          $output .= '</table>';
        $output .= '</div>';
      $output .= '</div>';

      // Middle Middle Right
      $output .= '<div class="{{block.middle.middleMiddle.middleMiddleRightClass}}">';
        $output .= '<span ng-bind-html="$sce.trustAsHtml(block.middle.middleMiddle.middleMiddleRight)">';
          $output .= '{{block.middle.middleMiddle.middleMiddleRight}}';
        $output .= '</span>';
      $output .= '</div>';

      // Middle Bottom
      $output .= '<div class="col-sm-12" ng-bind-html="$sce.trustAsHtml(block.middle.middleBottom)">';
        $output .= '{{block.middle.middleBottom}}';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   * Content Render MultiContainer
   */
  public function contentRenderMultiContainer() {
    $output = '';
    $output .= '<div class="content-render-multicontainer-wrapper">';
      $output .= '<div data-ng-repeat="block in block.middle.middleMiddle.middleMiddleMiddle">';
        $output .= '<div ng-switch="block.type">';
          $output .= '<div ng-switch-when="chart">';
            $output .= '<div class="{{block.class}}">';
              $output .= $this->contentRenderCharts();
            $output .= '</div>';
          $output .= '</div>';
          $output .= '<div ng-switch-when="commonTable" class="{{block.class}}">';
            $output .= $this->contentRenderTable();
          $output .= '</div>';
          $output .= '<div ng-switch-when="htmlSnippet">';
            $output .= $this->contentRenderHtmlSnippet();
          $output .= '</div>';
        $output .= '</div>';
      $output .= '</div>';
      $output .= '<div ng-if="block.bottom.enable">';
        $output .= $this->contentRenderBottom();
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   * Content Elements contains multiple tabs
   */
  public function contentRenderMultiTabs() {
    $output = '';
    $output .= '<md-tabs md-selected="selectedIndex" md-dynamic-height="" md-border-bottom="">';
      $output .= '<md-tab ng-repeat="block in block.middle.value" label="{{block.title}}">';

        $output .= '<div ng-switch="block.type">';
          $output .= '<div ng-switch-when="chart" class="{{block.class}}">';
            $output .= $this->contentRenderCharts();
          $output .= '</div>';
          $output .= '<div ng-switch-when="commonTable" class="{{block.class}}">';
            $output .= $this->contentRenderTable();
          $output .= '</div>';
          $output .= '<div ng-switch-when="multiContainer" class="{{block.class}} padding-0 multicontainer-in-multitabs">';
            $output .= $this->contentRenderMultiContainer();
          $output .= '</div>';
          $output .= '<div ng-switch-when="googleMap" class="{{block.class}}">';
            $output .= $this->contentRenderGoogleMap();
          $output .= '</div>';
        $output .= '</div>';

      $output .= '</md-tab>';
    $output .= '</md-tabs>';

    return $output;
  }

  /**
   * Content Render Bottom
   */
  public function contentRenderBottom() {
    $output = '';
    $output .= '<div class="col-md-12 bg-ffffff" ng-bind-html="$sce.trustAsHtml(block.bottom.value)">';
      $output .= '{{block.bottom.value}}';
    $output .= '</div>';

    return $output;
  }

  /**
   * Content Render Top
   */
  public function contentRenderTop() {
    $output = '';
    $output .= '<div class="col-md-12 bg-ffffff" ng-bind-html="$sce.trustAsHtml(block.top.value)">';
      $output .= '{{block.top.value}}';
    $output .= '</div>';

    return $output;
  }

  /*----------------------------------------------------------------------------*/

  /**
   * Top fixed widgets
   */
  public function topWidgetsFixed($nid = NULL) {
    $output = '';
    $output .= '<div data-ng-repeat="widget in pageData.fixedSection">';
      $output .= '<div ng-switch="widget.type">';
        /**
         * Widget type "widgetOne"
         */
        $output .= '<div ng-switch-when="widgetOne">';
          $output .= '<div class="{{widget.class}} margin-top-6 margin-bottom-20">';
            $output .=  '<md-content>';
              $output .= '<div id="block-topWidgets-{{$index}}" class="flexpage-square-number-wrapper border-radius-3 {{widget.value.header.class}}">';
                $output .= '<md-tooltip md-direction="bottom" class="tt-multiline" ng-if="widget.value.header.widgetTooltip">';
                  $output .= '<span class="font-size-14">{{widget.value.header.widgetTooltip.title}}</span>';
                  $output .= '<span class="font-size-12" ng-bind-html="$sce.trustAsHtml(widget.value.header.widgetTooltip.content)">';
                    $output .= '{{widget.value.header.widgetTooltip.content}}';
                  $output .= '</span>';
                $output .= '</md-tooltip>';
                $output .= '<div class="padding-12">';
                  $output .= '<div class="padding-top-12 font-size-16">';
                    $output .= '<span>';
                      $output .= '{{widget.value.header.valueOne.value}}';
                    $output .= '</span>';
                    $output .= '<md-menu>';
                      $output .= '<span id="save-topWidgets-{{$index}}" ng-click="openMenu($mdMenu.open, $event)" class="fa fa-angle-down float-right padding-12 padding-top-0 cursor-pointer"></span>';
                      $output .= '<md-menu-content width="3">';
                        $output .= '<md-button ng-click="saveAsPng(\'topWidgets\',$index)"> ';
                          $output .= 'Save PNG';
                        $output .= '</md-button>';
                      $output .= '</md-menu-content>';
                    $output .= '</md-menu>';
                  $output .= '</div>';
                  $output .= '<div class="line-height-1-5 margin-top-12 margin-bottom-6 font-size-12 program-snapshot-speaker-evaluations-link" ng-bind-html="$sce.trustAsHtml(widget.value.header.value.value)">';
                    $output .= '{{widget.value.header.value.value}}';
                  $output .= '</div>';
                $output .= '</div>';
              $output .= '</div>';
            $output .= '</md-content>';
          $output .= '</div>';
        $output .= '</div>';

        /**
         * Widget type "htmlSnippet"
         */
        $output .= '<div ng-switch-when="htmlSnippet">';
          $output .= '<div ng-bind-html="$sce.trustAsHtml(widget.value)">';
            $output .= "{{widget.value}}";
          $output .= '</div>';
        $output .= '</div>';

      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function contentRenderHtmlSnippet() {
    $output = '';
    $output .= '<div ng-bind-html="$sce.trustAsHtml(block.middle.value)">';
      $output .= "{{block.middle.value}}";
    $output .= '</div>';

    return $output;
  }

  /*
   * Content Master contain multiple elements
   */
  public function contentBlockMaster() {

    $output = '';
    $output .= '<div id="block-charts-{{block.blockId}}" class="panel block">';
      $output .= '<div ng-if="block.top.enable">';
        $output .= '<md-content>';
          $output .= $this->contentRenderHeader();
        $output .= '</md-content>';
      $output .= '</div>';

      $output .= '<div class="panel-body bg-ffffff padding-0 padding-bottom-12">';
        $output .= '<div ng-if="block.middle.enable">';
          $output .= '<div class="{{block.blockClasses}}">';
            $output .= '<div ng-switch="block.type">';
              $output .= '<div ng-switch-when="multiContainer" class="padding-0">';
                $output .= $this->contentRenderMultiContainer();
              $output .= '</div>';
              $output .= '<div ng-switch-when="chart">';
                $output .= $this->contentRenderCharts();
              $output .= '</div>';
              $output .= '<div ng-switch-when="comments">';
                $output .= $this->contentRenderComments();
              $output .= '</div>';
              $output .= '<div ng-switch-when="commonTable">';
                $output .= $this->contentRenderTable();
              $output .= '</div>';
              $output .= '<div ng-switch-when="mildderTable">';
                $output .= $this->contentRenderMildderTable();
              $output .= '</div>';
              $output .= '<div ng-switch-when="commonPhpTable">';
                $output .= $this->contentRenderPhpTable();
              $output .= '</div>';
              $output .= '<div ng-switch-when="googleMap">';
                $output .= $this->contentRenderGoogleMap();
              $output .= '</div>';
              $output .= '<div ng-switch-when="multiTabs">';
                $output .= $this->contentRenderMultiTabs();
              $output .= '</div>';
              $output .= '<div ng-switch-when="htmlSnippet">';
                $output .= $this->contentRenderHtmlSnippet();
              $output .= '</div>';
            $output .= '</div>';
          $output .= '</div>';
        $output .= '</div>';
        $output .= '<div ng-if="block.bottom.enable">';
          $output .= '<md-content>';
            $output .= $this->contentRenderBottom();
          $output .= '</md-content>';
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }
}
