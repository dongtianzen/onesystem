<?php

/**
 * @file
 * Contains \Drupal\flexpage\Content\FlexpageJsonGenerator.
 */

namespace Drupal\flexpage\Content;

use Drupal\Core\Controller\ControllerBase;

use Drupal\flexpage\Content\FlexpageBaseJson;
use Drupal\flexpage\Content\FlexpageSampleDataGenerator;

/**
 *
 */
class FlexpageJsonGeneratorDemoSnippet extends FlexpageBaseJson {

  private $sampleData;

  public function __construct() {
    $this->sampleData = new FlexpageSampleDataGenerator();
  }

  /**
   * @return Array data
   */
  public function programFixedSection() {
    $getFixedValue = array(
      array('bg-2fa9e0 color-fff', 'Total Registrations', 128),
      array('bg-f34b99 color-fff', 'Total Referrals', 360),
      array('bg-99dc3b color-fff', 'Number of Sessions', 96),
      array('bg-f3c848 color-fff', 'Overall Satisfaction', 4.5)
    );
    $output = NULL;
    for($i = 0; $i < count($getFixedValue); $i ++) {
      $output[$i] = $this->getTileStyleOne(array('value' => array('header' => array('class' => $getFixedValue[$i][0]))), $getFixedValue[$i][1], $getFixedValue[$i][2]);
    }
    return $output;
  }

  /**
   * @return Array data
   */
  public function bottomValue() {
    $bottom_value = NULL;
    $bottom_value .= '<div class="col-md-12 padding-0 position-absolute bottom-0">';
      $bottom_value .= '<div class="col-md-6 height-66 border-1-eee text-align-center">';
        $bottom_value .= 'NTS';
      $bottom_value .= '</div>';
      $bottom_value .= '<div class="col-md-6 height-66 border-1-eee text-align-center">';
        $bottom_value .= 'RESPONSES';
      $bottom_value .= '</div>';
    $bottom_value .= '</div>';
    return $bottom_value;
  }

  /**
   * @return Array data
   */
  // public function legendsValue() {
  //   $legends = NULL;
  //   $legends .= '<div class="margin-top-150">';
  //   for($i = 5; $i > 0; $i --){
  //     $legends .= '<span class="legend-square bg-00a9e0"></span>' ;
  //       $legends .= $i ;
  //     $legends .= '<br>';
  //   }
  //   $legends .= '</div>';
  //   return $legends;
  // }

  /**
   * @return Array data
   */
  public function commonTable() {
    $output = NULL;
    $output = $this->getBlockOne(
      array(
        'class' => "col-md-12",
        'type' => "commonTable",
        'blockClasses' => "height-400 overflow-visible"
      ),
      $this->getCommonTable(NUll, $this->sampleData->generateTableData())
    );
    return $output;
  }

  /**
   * @return Array data
   */
  public function phpTable() {
    $output = NULL;
    $output = $this->getBlockOne(
      array(
        'class' => "col-md-12",
        'blockClasses' => "height-400 overflow-visible",
        'type' => "commonPhpTable",
        'top' => array(
          'value' => 'commonPhpTable'
        )
      ),
      $this->getCommonTable(NUll, NULL)
    );
    return $output;
  }

  /**
   * @return Array data
   * no need "$this->getBlockOne()"
   */
  public function htmlSnippet() {
    $output = $this->getBlockHtmlSnippet(array(), "<div>Multi-Chart-Middle-Bottom</div>");
    return $output;
  }

  /**
   * @return Array data
   */
  public function chartblockPara($col = "6") {
    $output = array(
      'class' => 'col-xs-12 ' . "col-md-". $col,
      'middle' => array(
        'middleBottom' => "",
      ),
      'bottom' => array(
        'value' => ""
      )
    );
    return $output;
  }

  /**
   * @return Array data
   */
  public function doughnutChartCol($blockPara, $chartPara) {
    $output = $this->getBlockOne(
      $blockPara,
      $this->getChartNewJsDoughnut($chartPara, $this->sampleData->generateDoughnutChartData())
    );
    return $output;
  }

  /**
   * @return Array data
   */
  public function doughnutChartWithoutChart($blockPara, $chartPara) {
    $output = $this->getBlockOne(
      $blockPara,
      $this->getChartNewJsDoughnut($chartPara, $this->sampleData->generateDoughnutChartDataEmptyValue())
    );
    return $output;
  }

  /**
   * @return Array data
   */
  public function doughnutChartWithoutFeature($col = "4") {
    $blockPara = $this->chartblockPara($col);
    $chartPara = NULL;
    $output = $this->doughnutChartCol($blockPara, $chartPara);
    return $output;
  }

  /**
   * @return Array data
   */
  public function doughnutChartWithAllFeatures($col = "6") {
    $legends = $this->sampleData->legendsValue($this->sampleData->legendsColors()['doughnutChart']['colorOfLegends']);

    $blockPara = $this->chartblockPara($col);

    $blockPara['top'] = array(
      'enable' => true,
      'value' => "How effective is the speaker?"
    );
    $blockPara['middle'] = array(
      'middleBottom' => $this->bottomValue()
    );
    $blockPara['middle']['middleMiddle'] = array(
      'middleMiddleMiddleClass' => "col-md-10",
      'middleMiddleRightClass' => "col-md-2",
      'middleMiddleRight' => $legends
    );

    $chartPara['chartOptions']['crossText'] = ["", "world", "hello"];

    $output = $this->doughnutChartCol($blockPara, $chartPara);
    return $output;
  }

  /**
   * @return Array data
   */
  public function doughnutChartWithAllFeaturesNoChart($col = "6") {
    $legends = $this->sampleData->legendsValue($this->sampleData->legendsColors()['doughnutChart']['colorOfLegends']);

    $blockPara = $this->chartblockPara($col);

    $blockPara['top'] = array(
      'enable' => true,
      'value' => "How effective is the speaker?"
    );
    $blockPara['middle'] = array(
      'middleBottom' => 'some middle bottom value'
    );

    $blockPara['middle']['middleMiddle'] = array(
      'middleMiddleMiddleClass' => "col-md-10",
      'middleMiddleRightClass' => "col-md-2",
      'middleMiddleRight' => $legends
    );

    $blockPara['bottom'] = array(
      'value' => 'some bottom value, it can not display twice'
    );

    $output = $this->doughnutChartWithoutChart($blockPara, NULL);
    return $output;
  }

  /**
   * @return Array data
   */
  public function pieChart($blockPara) {
    $output = $this->getBlockOne(
      $blockPara,
      $this->getChartNewJsPie(NUll, $this->sampleData->generatePieChartData())
    );
    return $output;
  }

  /**
   * @return Array data
   */
  public function pieChartWithoutFeatures($col = "6") {
    $blockPara = $this->chartblockPara($col);
    $output = $this->pieChart($blockPara);
    return $output;
  }

  /**
   * @return Array data
   */
  public function pieChartWithAllFeatures($col = "4") {
    $blockPara = $this->chartblockPara($col);
    $bottom_value = $this->bottomValue();
    $legends = $this->sampleData->legendsValue($this->sampleData->legendsColors()['pieChart']['colorOfLegends']);
    $blockPara['top'] = array(
      'enable' => true,
      'value' => "How effective is the speaker?"
    );

    $blockPara['middle'] = array(
      'middleBottom' => $bottom_value,
    );
    $blockPara['middle']['middleMiddle'] = array(
      'middleMiddleMiddleClass' => "col-md-10",
      'middleMiddleRightClass' => "col-md-2",
      'middleMiddleRight' => $legends
    );
    $output = $this->pieChart($blockPara);
    return $output;
  }

  /**
   * @return Array data
   */
  public function lineChartWithAllFeatures($col = "12") {
    $blockPara = $this->chartblockPara($col);
    // $bottom_value = $this->bottomValue();
    $legends = $this->sampleData->legendsValue($this->sampleData->legendsColors()['lineChart']['colorOfLegends']);
    $blockPara['top'] = array(
              'enable' => true,
              'value' => "How effective is the speaker?"
            );
    $blockPara['bottom'] = array(
      'value' => "this is the bottom legeds of multiple line chart" . '<span class="bg-009fe3 legend-square"></span>'. '<span class="bg-e72682 legend-square"></span>',
    );

        // $blockPara['middle'] = array(
        //   'middleBottom' => "this is the bottom legeds of multiple line chart",
        // );
    $blockPara['middle']['middleMiddle'] = array(
      'middleMiddleMiddleClass' => "col-md-8",
      'middleMiddleRightClass' => "col-md-4",
      'middleMiddleRight' => $legends
    );
    $chartPara["chartOptions"]["yAxisLabel"] = "with legends";
    $output = $this->lineChartMultiLines($blockPara, $chartPara);
    return $output;
  }

  /**
   * @return Array data
   */
  public function lineChartWithNoFeatures($col = "12") {
    $blockPara = $this->chartblockPara($col);
    $chartPara["chartOptions"]["yAxisLabel"] = "";
    $output = $this->lineChartSingleLine($blockPara, $chartPara);
    return $output;
  }

  /**
   * @return Array data
   */
  public function lineChartMultiLines($blockPara, $chartPara) {
    $output = $this->getBlockOne(
      $blockPara,
      $this->getChartNewJsLine($chartPara, $this->sampleData->generateLineChartData())
    );
    return $output;
  }

  /**
   * @return Array data
   */
  public function lineChartSingleLine($blockPara, $chartPara) {
    $output = $this->getBlockOne(
      $blockPara,
      $this->getChartNewJsLine($chartPara, $this->sampleData->generateOneLineChartData())
    );
    return $output;
  }

  /**
   * @return Array data
   */
  public function barChartWithSameColor($blockPara) {
    $output = $this->getBlockOne(
      $blockPara,
      $this->getChartNewJsBar(array("chartType" => "Bar"), $this->sampleData->generateBarChartDataWithSameColor())
    );
    return $output;
  }

  /**
   * @return Array data
   */
  public function barChartWithDifferentColor($blockPara, $ChartDisplayPara) {
    $output = $this->getBlockOne(
      $blockPara,
      $this->getChartNewJsBar($ChartDisplayPara, $this->sampleData->generateBarChartData())
    );
    return $output;
  }

  /**
   * @return Array data
   */
  public function barChartWithTop($col = "6") {
    $blockPara = $this->chartblockPara($col);
    $blockPara['top'] = array(
      'enable' => true,
      'value' => "Clinical Practice"
    );
    $ChartDisplayPara["chartOptions"]["barValueSpacing"] = 2;
    $ChartDisplayPara["chartType"] = "Bar";
    $output = $this->barChartWithDifferentColor($blockPara, $ChartDisplayPara);
    return $output;
  }

  /**
   * @return Array data
   */
  public function barChartWithBottom($col = "6") {
    $blockPara = $this->chartblockPara($col);
    $blockPara['type'] = "chart";
    $blockPara['bottom'] = array(
      'value' => 'this is the bottom legends22   of bar chart~~~~~~~~~~~~~~~~~~~~~~~~~~~'
    );
    $output = $this->barChartWithSameColor($blockPara);
    return $output;
  }

  /**
   * @return Array data
   */
  public function tabContainer($type = 'multiContainer' , $title, $middleBottom, $bottom) {
    $output = NULL;
    $output = array(
      'class' => "col-md-12",
      'title' => $title,
      'type' => $type,
      'middle' => array(
        'middleBottom' => $middleBottom,
      ),
      'bottom' => array(
        'value' => $bottom,
      )
    );
    return $output;
  }

  /**
   * @return Array data
   */
  public function multiTabs() {
    $output = NULL;
    $output = $this->getBlockMultiTabs(
      array(
        'class' => "col-md-12"
      ),
      array(
        $this->getBlockTabContainer(
          $this->tabContainer('multiContainer', '1111', '', 'this is the bottom legends of tabs container first page'),
          array(
            $this->barChartWithTop("6"),
            $this->barChartWithBottom("6")
          )
        ),
        $this->getBlockTabContainer(
          $this->tabContainer('multiContainer', '222', '', 'this is the bottom legends of tabs container second page'),
          array(
            $this->barChartWithBottom()
          )
        ),

        // $this->getBlockTabContainer( array('class' => "col-md-12", 'type' => "googleMap"), $this->getGoogleMap()),
        $this->getBlockTabContainer(
          $this->tabContainer(
            'multiContainer',
            '333',
            'middleBottom-Chart-Bottom',
            'Multi-Chart-Bottom Multi-Chart-Bottom Multi-Chart-Bottom'
          ),
          $this->getChartNewJsDoughnut(
            NUll,
            $this->sampleData->generateDoughnutChartData()
          )
        )
      )
    );
    return $output;
  }

  /**
   * @return Array data
   */
  public function multipleContainer() {
    $output = NULL;
    $output = $this->getBlockMultiContainer(
      $this->tabContainer('multiContainer', '555', 'getBlockTabContainer-Bottom', 'Multi-Chart-Bottomssssssssssss'),
      array(
        $this->barChartWithTop("6"),
      )
      // $this->getBlockOne(array('class' => "col-md-6"), $this->getChartNewJsDoughnut(NUll, $this->generateSampleData("doughnut_chart_data"))),
      // $this->getBlockOne(array('class' => "col-md-6"), $this->getChartNewJsDoughnut(NUll, $this->generateSampleData("doughnut_chart_data"))),

    );
    return $output;
  }

}

/**
 *
 */
class FlexpageJsonGeneratorDemo {

  private $parentObject;

  public function __construct() {
    $this->parentObject = new FlexpageJsonGeneratorDemoSnippet;
  }

  public function getDemo() {
    return $this->parentObject;
  }

}

/**
 * An example controller.
 */
class FlexpageJsonGenerator extends FlexpageJsonGeneratorDemo {

  /**
   *
   */
  public function angularJson() {
    // $this->setPostUrl('page/forms/preform/add');

    $output['fixedSection'] = $this->getDemo()->programFixedSection();

    $output['contentSection'][] = $this->getDemo()->commonTable();

    $output['contentSection'][] = $this->getDemo()->doughnutChartWithAllFeatures("6");
    $output['contentSection'][] = $this->getDemo()->doughnutChartWithoutFeature("6");

    $output['contentSection'][] = $this->getDemo()->doughnutChartWithAllFeaturesNoChart("6");
    $output['contentSection'][] = $this->getDemo()->pieChartWithoutFeatures("6");
    $output['contentSection'][] = $this->getDemo()->pieChartWithAllFeatures(8);

    $output['contentSection'][] = $this->getDemo()->lineChartWithAllFeatures();

    $output['contentSection'][] = $this->getDemo()->lineChartWithNoFeatures();

    $output['contentSection'][] = $this->getDemo()->barChartWithTop();
    $output['contentSection'][] = $this->getDemo()->barChartWithBottom();
    $output['contentSection'][] = $this->getDemo()->multiTabs();

    $output['contentSection'][] = $this->getDemo()->multipleContainer();
    $output['contentSection'][] = $this->getDemo()->multiTabs();


    return $output;
  }

}
