<?php

/**
 * @file
 * Contains \Drupal\flexpage\Content\FlexpageBaseJson.
 */

namespace Drupal\flexpage\Content;

use Drupal\Core\Controller\ControllerBase;

/**
 *
 */
class FlexpageBaseJson extends ControllerBase {

  private $post_url = NULL;

  public function getPostUrl() {
    return $this->post_url;
  }

  public function setPostUrl($value = NULL) {
    $this->post_url = $value;
  }

  /**
   *
   */
  public function generateUniqueId() {
    $output = hexdec(substr(uniqid(NULL, TRUE), 15, 8));
    return $output;
  }

  /** - - - - - field- - - - - - - - - - - - - - - */
  /**
   *
   */
  public function getClearfixLine() {
    $output = '<div class="clearfix"></div>';
    return $output;
  }

  /**
   *
   */
  public function getTileStyleOne($option = array(), $value = NULL, $value_one = NULL) {
    $output = array(
      'blockId' => $this->generateUniqueId(),
      'class' => "col-md-3 col-xs-6",
      'type'  => "widgetOne",
      'value' => array(
        'header' => array(
          'class' => "color-fff",
          'value' => array(
            'class' => "font-size-14",
            'value' => $value,
          ),
          'valueOne' => array(
            'class' => "font-size-12",
            'value' => $value_one,
          ),
        ),
      ),
    );

    $output = $this->setBlockProperty($output, $option);

    return $output;
  }

  /**
   * @return Array data
   */
  public function generateTileStyleOne($fixedValue = array()) {
    $output = NULL;
    for($i = 0; $i < count($fixedValue); $i ++) {
      $output[$i] = $this->getTileStyleOne(array('value' => array('header' => array('class' => $fixedValue[$i][0]))), $fixedValue[$i][1], $fixedValue[$i][2]);
    }
    return $output;
  }

  /**
   * @notice use on $output['contentSection'] not ['fixedSection']
   */
  public function getBlockHtmlSnippet($option = array(), $value = NULL, $class = "col-md-12") {
    $output = array(
      'blockId' => $this->generateUniqueId(),
      'class' => $class,
      'blockClasses' => "",
      'type' => "htmlSnippet",          // chart or multiContiner, commonTable, googleMap
      'top'  =>  array(
        'enable' => TRUE,
        'value' => NULL,          // block top title value
      ),
      'middle' =>  array(
        'enable' => TRUE,
        'value' => $value,     // <div>Multi-Chart-Middle-Top</div>
      )
    );

    $output = $this->setBlockProperty($output, $option);

    return $output;
  }

  /**
   *
   */
  public function getBlockHtmlSnippetWithoutTop($option = array(), $value = NULL, $class = "col-md-12") {
    $output = array(
      'blockId' => $this->generateUniqueId(),
      'class' => $class,
      'blockClasses' => "",
      'type' => "htmlSnippet",          // chart or multiContiner, commonTable, googleMap
      'top'  =>  array(
        'enable' => FALSE,
        'value'  => NULL,          // block top title value
      ),
      'middle' =>  array(
        'enable' => TRUE,
        'value'  => $value,     // <div>Multi-Chart-Middle-Top</div>
      )
    );

    $output = $this->setBlockProperty($output, $option);

    return $output;
  }

  /**
   *
   */
  public function getBlockOne($option = array(), $middle_middle_value = array()) {
    $output = array(
      'blockId' => $this->generateUniqueId(),
      'class' => "col-md-12",
      'blockClasses' => "height-400 overflow-hidden position-relative",
      'type' => "chart",          // chart or multiContiner, commonTable, googleMap
      'top'  =>  array(
        'enable' => TRUE,
        'value' => NULL,          // block top title value
        'class' => NULL,
      ),
      'middle' =>  array(
        'enable' => true,
        'middleTop' => NULL,      // block middleTop HTML value, "<div>Multi-Chart-Middle-Top</div>"
        'middleMiddle' =>  array(
          'middleMiddleLeftClass' => "",
          'middleMiddleLeft' => "",
          'middleMiddleMiddleClass' => "",
          'middleMiddleMiddle' => $middle_middle_value,
          'middleMiddleRightClass' => "",
          'middleMiddleRight' => "",
          'gridColumn' => ""
        ),
        'middleBottom' => "",   // block middleBottom HTML value, "<div>Multi-Chart-Middle-Bottom</div>"
      ),
      'bottom' => array(
        'enable' => TRUE,
        'value' => NULL,          // block Bottom HTML value, "<div>Multi-Chart-Bottom</div>"
      )
    );

    $output = $this->setBlockProperty($output, $option);
    $output = $this->setContentMaxHeight($output);

    return $output;
  }

  /**
   *
   */
  public function getBlockTabContainer($option = array(), $middle_middle = NULL) {
    $output = array(
      'blockId' => $this->generateUniqueId(),
      'class' => "col-md-12",
      'title' => "Tab22222",
      'type' => "multiContainer",  // or chart
      'top' =>  array(
        'enable' => true,
        'value' => NULL
      ),
      'middle' =>  array(
        'enable' => true,
        'middleTop' => NULL,
        'middleMiddle' =>  array(
          'middleMiddleLeftClass' => "",
          'middleMiddleLeft' => "",
          'middleMiddleMiddleClass' => "",
          'middleMiddleMiddle' => $middle_middle,
          'middleMiddleRightClass' => "",
          'middleMiddleRight' => ""
        ),
        'middleBottom' => NULL
      ),
      'bottom' => array(
        'enable' => true,
        'value' => NULL
      )
    );

    $output = $this->setBlockProperty($output, $option);

    return $output;
  }

  /**
   *
   */
  public function getBlockMultiContainer($option = array(), $middle_middle = NULL) {
    $output = array(
      'blockId' => $this->generateUniqueId(),
      'class' => "col-md-12",
      'blockClasses' => '',
      'type' => "multiContainer",          // chart or multiContiner, commonTable, googleMap
      'top'  =>  array(
        'enable' => true,
        'value' => NULL,          // block top title value
      ),
      'middle' =>  array(
        'enable' => true,
        'middleTop' => NULL,      // block middleTop HTML value, "<div>Multi-Chart-Middle-Top</div>"
        'middleMiddle' =>  array(
          'middleMiddleLeftClass' => "",
          'middleMiddleLeft' => "",
          'middleMiddleMiddleClass' => "",
          'middleMiddleMiddle' => $middle_middle,
          'middleMiddleRightClass' => "",
          'middleMiddleRight' => ""
        ),
        'middleBottom' => "",   // block middleBottom HTML value, "<div>Multi-Chart-Middle-Bottom</div>"
      ),
      'bottom' => array(
        'enable' => true,
        'value' => NULL,          // block Bottom HTML value, "<div>Multi-Chart-Bottom</div>"
      )
    );

    $output = $this->setBlockProperty($output, $option);

    return $output;
  }

  /**
   *
   */
  public function getBlockMultiTabs($option = array(), $tabs_value = array()) {
    $output = array(
      "blockId" => $this->generateUniqueId(),
      "class" => "col-md-12",
      "type" => "multiTabs",
      "blockClasses" => "",
      "top" => array(
        "enable" => true,
        "value" => "Multi Tabs"
      ),
      "middle" => array(
        "enable" => true,
        "value" => $tabs_value,
      ),
      "bottom" => array(
        "enable" => true,
        "value" => NULL
      )
    );

    $output = $this->setBlockProperty($output, $option);

    return $output;
  }

  /** - - - - - field- - - - - - - - - - - - - - - */
  /**
   * @return Array data
   */
  public function chartNewJsOptions() {
    $output = array(
      "animation" => true,
      'animationSteps'=> 50,
      "annotateClassName" => "my11001799tooltip",
      "annotateDisplay" => TRUE, //onhover value
      "annotateLabel" => "<%=v2%>",
      "datasetFill" => false,
      "datasetStrokeWidth" => 2,
      "inGraphDataBordersXSpace" => 12,
      "inGraphDataBordersYSpace" => 7,
      "inGraphDataFontColor" => "#000",
      "inGraphDataFontSize" => 15,
      "inGraphDataFontStyle" => "normal normal",
      "inGraphDataPaddingY" => 5,
      "inGraphDataShow" => true,
      "inGraphDataTmpl" => "<%=v3%>",
      "maxLegendCols" => 5, //maximum legend columns
      "responsive" => true,
      "responsiveMaxHeight" => 480,
      "responsiveMinHeight" => 280,
      "spaceBottom" => 10,
      "spaceTop" => 20,
      "legend" => false,
      "legendBlockSize" => 14,
      "legendBorders" => false,
      "legendFontColor" => "#000",
      "legendFontFamily" => "Roboto,'Helvetica Neue',sans-serif",
      "legendPosX" => 2,
      "legendPosY" => 0,
      "legendSpaceAfterText" => 0,
      "legendSpaceBeforeText" => 10,
      "legendSpaceBetweenBoxAndText" => 9,
      "legendSpaceBetweenTextHorizontal" => 15,
      "legendSpaceBetweenTextVertical" => 28,
      "legendSpaceLeftText" => 18,
      "legendBlockSize" => 14,
    );

    return $output;
  }

  /**
   * @return Array data, single bar
   * @see this->getChartNewJsStackedBar() for HorizontalStackedBar or StackedBar
   */
  public function getChartNewJsBar($option = array(), $chart_data = array()) {
    $output = array(
      "chartId" => $this->generateUniqueId(),
      "chartType" => "Bar", // Bar or HorizontalBar or HorizontalStackedBar or StackedBar
      "chartClass" => "",  // only render on getBlockMultiContainer
      "chartTitle" => "Identify and address only render on getBlockMultiContainer",         // do we need this one
      "chartData" => $chart_data
    );

    $number_of_labels = 0;
    if (isset($chart_data['labels'])) {
      $number_of_labels = count($chart_data['labels']);
    }
    // dynamic bar value spacing according to labels in the chart.

    $bar_value_spacing = 0;
    if ($number_of_labels > 0) {
      $bar_value_spacing = 160 / $number_of_labels;
    }

    $output["chartOptions"] = $this->chartNewJsOptions();

    // increase chart height on oncreased labels in chart
    if (isset($option["chartType"]) && $option["chartType"] == ('HorizontalStackedBar' || 'HorizontalBar')) {

      if ($number_of_labels > 0) {
        $bar_value_spacing = 120 / $number_of_labels;
        if ($number_of_labels > 4) {
          $output["chartOptions"]["responsiveMaxHeight"] = 600;
        }
      }
      if ($option["chartType"] == "HorizontalStackedBar") {
        $output["chartOptions"]["inGraphDataFontColor"] = "#fff";
      }
    }

    $output["chartOptions"]["annotateLabel"] = "<%=Math.round(100*(v3/grandtotal)) + \"%\"%>";
    // $output["chartOptions"]["annotateLabel"] = "<%=Math.round(v6) + \"%\" %>";

    $output["chartOptions"]["barValueSpacing"] = $bar_value_spacing;
    $output["chartOptions"]["barBorderRadius"] = 5;
    $output["chartOptions"]["barStrokeWidth"] = 2;
    $output["chartOptions"]["graphMin"] = 0;
    // $output["chartOptions"]["graphMax"] = 100;
    $output["chartOptions"]["inGraphDataTmpl"] = "<%=(v3 < 1) ? \"\"  : v3%>";
    $output["chartOptions"]["pointDotRadius"] = 6;
    $output["chartOptions"]["scaleFontSize"] = 14;
    $output["chartOptions"]["spaceTop"] = 30;
    $output["chartOptions"]["yAxisMinimumInterval"] = 10;
    $output["chartOptions"]["yScaleLabelsMinimumWidth"] = 40;

    $output = $this->setBlockProperty($output, $option);

    return $output;
  }

  /**
   * @return Array data
   */
  public function getChartNewJsStackedBar($option = array(), $chart_data = array()) {
    $output = array(
      "chartId" => $this->generateUniqueId(),
      "chartType" => "StackedBar", // HorizontalStackedBar or StackedBar
      "chartClass" => "",  // only render on getBlockMultiContainer
      "chartTitle" => "Identify and address only render on getBlockMultiContainer",         // do we need this one
      "chartData" => $chart_data
    );

    $number_of_labels = 0;
    if (isset($chart_data['labels'])) {
      $number_of_labels = count($chart_data['labels']);
    }
    // dynamic bar value spacing according to labels in the chart.

    $bar_value_spacing = 0;
    if ($number_of_labels > 0) {
      $bar_value_spacing = 160 / $number_of_labels;
    }

    $output["chartOptions"] = $this->chartNewJsOptions();
    $output["chartOptions"]["annotateLabel"] = "<%=Math.round(100*(v3/grandtotal)) + \"%\"%>";
    // $output["chartOptions"]["annotateLabel"] = "<%=Math.round(v6) + \"%\" %>";

    $output["chartOptions"]["barValueSpacing"] = $bar_value_spacing;
    $output["chartOptions"]["barBorderRadius"] = 5;
    $output["chartOptions"]["barStrokeWidth"] = 2;
    $output["chartOptions"]["graphMin"] = 0;
    // $output["chartOptions"]["graphMax"] = 100;
    $output["chartOptions"]["inGraphDataTmpl"] = "<%=(v3 < 1) ? \"\"  : v3%>";
    $output["chartOptions"]["pointDotRadius"] = 6;
    $output["chartOptions"]["scaleFontSize"] = 14;
    $output["chartOptions"]["spaceTop"] = 30;
    $output["chartOptions"]["yAxisMinimumInterval"] = 10;
    $output["chartOptions"]["yScaleLabelsMinimumWidth"] = 40;

    $output = $this->setBlockProperty($output, $option);

    return $output;
  }

  /**
   * @return Array data
   */
  public function getChartNewJsLine($option = array(), $chart_data = array()) {
    $output = array(
      "chartId" => $this->generateUniqueId(),
      "chartClass" => "col-md-6 opacity-05",
      "chartType" => "Line",
      "chartTitle" => "Line Chart",
      "chartData" => $chart_data
    );

    $output["chartOptions"] = $this->chartNewJsOptions();
    $output["chartOptions"]["annotateLabel"] = "<%=v3%>";
    $output["chartOptions"]["barValueSpacing"] = 20;
    $output["chartOptions"]["bezierCurveTension"] = 0.2;
    // $output["chartOptions"]["graphMax"] = 100;
    $output["chartOptions"]["graphMin"] = 0;
    $output["chartOptions"]["inGraphDataShow"] = true;
    $output["chartOptions"]["inGraphDataTmpl"] = "<%=Math.round(v3)%>";
    $output["chartOptions"]["maxLegendCols"] = 1;
    $output["chartOptions"]["pointDotRadius"] = 6;
    $output["chartOptions"]["percentageInnerCutout"] = 99;
    $output["chartOptions"]["scaleFontSize"] = 14;
    $output["chartOptions"]["legendPosX"] = 4;
    $output["chartOptions"]["legendPosY"] = -2;
    $output["chartOptions"]["legendSpaceLeftText"] = 18;
    $output["chartOptions"]["yAxisLabel"] = "Number of Events";
    $output["chartOptions"]["yAxisMinimumInterval"] = 20;
    $output["chartOptions"]["yScaleLabelsMinimumWidth"] = 40;

    $output = $this->setBlockProperty($output, $option);

    return $output;
  }

  /**
   *
   */
  public function getChartNewJsDoughnut($option = array(), $chart_data = array()) {
    $output = array(
      'chartId' => $this->generateUniqueId(),
      'chartType' => "Doughnut",
      'chartData' => $chart_data
    );

    $output["chartOptions"] = $this->chartNewJsOptions();
    $output["chartOptions"]["annotateDisplay"] = true;
    $output["chartOptions"]["annotateLabel"] = "<%=v3%>";
    $output["chartOptions"]["barValueSpacing"] = 20;
    $output["chartOptions"]["bezierCurveTension"] = 0.1;
    $output["chartOptions"]["crossText"] = ["", "", ""];
    $output["chartOptions"]["crossTextAlign"] = ["center"];
    $output["chartOptions"]["crossTextBaseline"] = ["middle"];
    $output["chartOptions"]["crossTextFontColor"] = ["black"];
    $output["chartOptions"]["crossTextFontSize"] = [0,30,30];
    $output["chartOptions"]["crossTextIter"] = ["last"];
    $output["chartOptions"]["crossTextOverlay"] = [true];
    $output["chartOptions"]["crossTextPosX"] = [0,0,0];
    $output["chartOptions"]["crossTextPosY"] = [0,20,-30];
    $output["chartOptions"]["crossTextRelativePosX"] = [0,2,2];
    $output["chartOptions"]["crossTextRelativePosY"] = [0,2,2];
    $output["chartOptions"]["inGraphDataShow"] = false;
    $output["chartOptions"]["percentageInnerCutout"] = 90;  // thickness of doughnut chart size
    $output["chartOptions"]["pointDotRadius"] = 6;
    $output["chartOptions"]["yAxisMinimumInterval"] = 20;

    $output = $this->setBlockProperty($output, $option);

    return $output;
  }

  /**
   *
   */
  public function getChartNewJsPie($option = array(), $chart_data = array()) {
    $output = array(
      "chartId" => $this->generateUniqueId(),
      "chartType" => "Pie",
      "chartClass" => "col-md-6 opacity-05",  // only render on getBlockMultiContainer
      "chartTitle" => "Identify and address only render on getBlockMultiContainer",
      "chartData" => $chart_data
    );

    $output["chartOptions"] = $this->chartNewJsOptions();

    $output["chartOptions"]["annotateLabel"] = "<%=v3%>";
    $output["chartOptions"]["barValueSpacing"] = 0;
    $output["chartOptions"]["barValueSpacing"] = 0;
    $output["chartOptions"]["bezierCurveTension"] = 0.1;
    $output["chartOptions"]["inGraphDataTmpl"] = "<%=Math.round(v6 < 5) ? \"\" : Math.round(v6) + \"%\"%>";
    $output["chartOptions"]["inGraphDataAlign"] = "center";
    $output["chartOptions"]["inGraphDataAnglePosition"] = 2;
    $output["chartOptions"]["inGraphDataFontColor"] = "#ffffff";
    $output["chartOptions"]["inGraphDataPaddingRadius"] = 25;
    $output["chartOptions"]["inGraphDataRadiusPosition"] = 2;
    $output["chartOptions"]["percentageInnerCutout"] = 99; //inner cut area
    $output["chartOptions"]["pointDotRadius"] = 6;
    $output["chartOptions"]["title"] = "";
    $output["chartOptions"]["yAxisMinimumInterval"] = 20;


    // default inGraphDataType is percentage
    // if (isset($option["inGraphDataType"])) {
    //   if ($option["inGraphDataType"] == 'value') {
    //     $output["chartOptions"]["inGraphDataTmpl"] = "<%=v2%>";
    //     $output["chartOptions"]["annotateLabel"] = "<%=(Math.round(v6))+'%'%>";
    //   }
    // }

    $output = $this->setBlockProperty($output, $option);

    return $output;
  }

  /**
   *
   */
  public function getCommonTable($option = array(), $table_data = array()) {
    $output = array(
      "class" => "font-size-12",
      "tableSettings" => array(
        "pagination" => true,
        "searchFilter" => true,
        "paginationType" => "full_numbers",
        "searchPlaceholder" => "SEARCH"
      ),
      "value" => $table_data,
    );

    $output = $this->setBlockProperty($output, $option);

    return $output;
  }

  /**
   *
   */
  public function getGoogleMap($option = array(), $value = NULL) {
    $output = array(
      "class" => "col-md-12",
      'type' => "googleMap",
    );

    $output = $this->setBlockProperty($output, $option);

    return $output;
  }

  /** - - - - - set - - - - - - - - - - - - - - */
  /**
   *
   */
  public function setBlockProperty($output = array(), $option = array()) {
    if (is_array($option)) {
      foreach ($option as $key => $value) {
        if (array_key_exists($key, $output)) {
          if (is_array($value)) {
            $output[$key] = $this->setBlockProperty($output[$key], $value);
          }
          else {
            $output[$key] = $value;
          }
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function setContentMaxHeight($output = array()) {
    $pattern = '/height\-(\d+)/';
    if ($output['middle']['middleBottom']) {
    }

    if (isset($output['blockClasses'])) {
      preg_match($pattern, $output['blockClasses'], $matches);
      if (isset($matches[1])) {
        if (isset($output['middle']['middleMiddle']['middleMiddleMiddle']['chartOptions']['responsiveMaxHeight'])) {
          if ($output['middle']['middleMiddle']['middleMiddleMiddle']['chartOptions']['responsiveMaxHeight'] >= $matches[1]) {

            // if bottom exists
            if ($output['middle']['middleBottom']) {
              $output['middle']['middleMiddle']['middleMiddleMiddle']['chartOptions']['responsiveMaxHeight'] = $matches[1] - 66;
            }
            else {
              $output['middle']['middleMiddle']['middleMiddleMiddle']['chartOptions']['responsiveMaxHeight'] = $matches[1] - 30;
            }

          }
        }
      }
    }

    return $output;
  }

}
