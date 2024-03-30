<?php

namespace App\Service;

use App\Data\ChartItem;
use App\Data\ChartOptions;
use App\Data\Statisitc\SubTypeModel\AbstractSubTypeModel;
use App\Data\Statisitc\TypeModel\AbstractTypeModel;
use App\Entity\Widget;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

//TODO : create two children classes, one for user statistics and one for native statistics

class StatisticsService
{

  private EntityManagerInterface $em;
  private Security $security;

  private ?AbstractTypeModel $typeModel;
  private ?AbstractSubTypeModel $subTypeModel;
  private ChartItem $chartItem;
  private ?Widget $widget;

  public function __construct(EntityManagerInterface $em, Security $security)
  {
    $this->em = $em;
    $this->security = $security;

    $this->typeModel = null;
    $this->subTypeModel = null;
    $this->chartItem = new ChartItem();
    $this->widget = null;
  }

  /**
   * Set the widget used for the service
   * Must be used before other methods
   * @param Widget $widget
   * @return void
   */
  public function setWidget(Widget $widget): void
  {
    $this->widget = $widget;
  }

  /**
   * Set the models attributes (typeModel and subTypeModel) by using the widgetType and widgetSubType
   * Necessary for use other methods of the service
   * @return void
   * @throws \Exception
   */
  public function setModels(){

    if ($this->widget === null){
      throw new \Exception('The widget attribute of StatisticsService is null, use setWidget() method before');
    }

    $this->typeModel = $this->widget->getTypeModel();
    $this->subTypeModel = $this->widget->getSubTypeModel();

    if ($this->typeModel === null || $this->subTypeModel === null){
      throw new \Exception('TypeModel or subTypeModel can\'t be found for the widget');
    }
  }

  public function generateContent(?Widget $widget, array $parameters): string
  {

    //TODO : use model for get the content (even if it's always the same for now)

    $content = '';

    switch ($widget->getSubTypeWidget()) {

      case Widget::SUB_TYPE__BAR :
      case Widget::SUB_TYPE__PIE :
      case Widget::SUB_TYPE__DONUT :

        $spinner = '
          <div class="text-center grow flex items-center justify-center">
            <div role="status">
              <svg aria-hidden="true" class="inline w-16 h-16 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
              </svg>
              <span class="sr-only">Loading...</span>
            </div>
          </div>
        ';

        $subContent = '<canvas id="canvas-' . $widget->getId() . '" class="widget-canvas"></canvas>';
        $content = $widget->getDeleteButton() . $widget->getModifyButton() . $widget->getInfoButton() . '
          <div id="widget-chart-' . $widget->getId() . '" class="widget-chart flex flex-col vstack m-0" style="background-color: ' . $widget->getBackgroundColor() . '; color: ' . $widget->getFontColor() . ';">
            <div class="flex-non text-xl font-bold">' . $widget->getWording() . '</div>' .
            $spinner .
            '<div class="col p-2 flex justify-center" style="height: 0;">' . $subContent . '</div>
          </div>
        ';
        break;

    }

    return $content;
  }


  /**
   * Return the data attribute of the JSON chart definition
   * @param Widget $widget
   * @return array[]
   * @throws \Exception
   */
  public function getDataAttributeForChart(): array
  {
    if ($this->widget === null){
      throw new \Exception('The widget attribute of StatisticsService is null, use setWidget() method before');
    }

    //the returned value
    $dataAttribute = [
      'labels' => [], //labels of the chart
      'datasets' => [], //This attribute is an array of datasets, set after the switch with $dataSets variable
    ];

    $backgroundColor = [
      'rgba(255, 99, 132, 0.2)',
      'rgba(255, 159, 64, 0.2)',
      'rgba(255, 205, 86, 0.2)',
      'rgba(75, 192, 192, 0.2)',
      'rgba(54, 162, 235, 0.2)',
      'rgba(153, 102, 255, 0.2)',
      'rgba(201, 203, 207, 0.2)'
    ];

    $borderColor = [
      'rgb(255, 99, 132)',
      'rgb(255, 159, 64)',
      'rgb(255, 205, 86)',
      'rgb(75, 192, 192)',
      'rgb(54, 162, 235)',
      'rgb(153, 102, 255)',
      'rgb(201, 203, 207)'
    ];

    $dataSets = [
      'label' => $this->widget->getWording(), // Title of the chart
      'data' => [], // Data of the chart
      'backgroundColor' => $backgroundColor,
      'borderColor' => $borderColor,
      'borderWidth' => 5
    ];

    //For native widget, we need to call a specific function
    if ($this->widget->getTypeWidget() == Widget::TYPE__NATIVE){

      $dataAttribute = $this->getDataSetsForNativeWidget();

    } else {

      switch ($this->widget->getSubTypeWidget()) {

        case Widget::SUB_TYPE__BAR :

          $query = $this->widget->getQuery();
          $allResults = $this->em->getConnection()->executeQuery($query)->fetchAllAssociative();

          if (count($allResults) > 0) {
            $total = 0;
            foreach ($allResults as $result) {
              $total += $result['count'];
            }
            $total = ['name' => 'All', 'count' => $total];

            $firstResults = array_slice($allResults, 0, 5);
            $results = array_merge([$total], $firstResults);

            $labels = [];
            $datas = [];
            foreach ($results as $result) {
              $labels[] = $result['name'];
              $datas[] = $result['count'];
            }
            $dataAttribute['labels'] = $labels;
            $dataSets['data'] = $datas;
          }
          break;

        case Widget::SUB_TYPE__PIE :
        case Widget::SUB_TYPE__DONUT :
          break;

      }
      //END SWITCH

      $dataAttribute['datasets'][] = $dataSets;
    }

    $this->chartItem->dataAttribute = $dataAttribute;
    return $dataAttribute;
  }

  /**
   * Return the data attribute of the JSON chart definition for native widget
   * @return array
   * @throws \Exception
   */
  private function getDataSetsForNativeWidget(): array
  {
    if ($this->widget === null){
      throw new \Exception('The widget attribute of StatisticsService is null, use setWidget() method before');
    }

    $dataAttribute = [];
    $user = $this->security->getUser();

    switch ($this->widget->getSubTypeWidget()) {

      case Widget::SUB_TYPE_NATIVE__SCROBBLES_PER_MONTH_ANNUALY :

        $widgetRepository = $this->em->getRepository(Widget::class);
        $results = $widgetRepository->getScrobblesPerMonthAnnually($user);
        $allResults = [];
        foreach ($results as $result) {
          $allResults[$result['year']][$result['month']] = $result['count'];
        }

        $curentYear = date('Y');
        $lastYear = date('Y') - 1;

        $dataAttribute['labels'] = ['Jan.', 'Feb.', 'Mar.', 'Apr.', 'May', 'Jun.', 'Jul.', 'Aug.', 'Sep.', 'Oct.', 'Nov.', 'Dec.'];
        $datas = [];
        for ($year = $lastYear; $year <= $curentYear; $year++) {
          for ($month = 1; $month <= 12; $month++) {
            if (isset($allResults[$year][$month])) {
              $datas[$year][] = $allResults[$year][$month];
            } else {
              $datas[$year][] = 0;
            }
          }
        }

        $firstDataSets['data'] = $datas[$curentYear];
        $firstDataSets['label'] = $curentYear;
        $secondDataSets['data'] = $datas[$lastYear];
        $secondDataSets['label'] = $lastYear;
        $dataAttribute['datasets'][] = $firstDataSets;
        $dataAttribute['datasets'][] = $secondDataSets;

        break;


        case Widget::SUB_TYPE_NATIVE__TOTAL_SCROBBLES_PER_YEAR :

          $dataSets = [];

          $widgetRepository = $this->em->getRepository(Widget::class);
          $resultCollection = $widgetRepository->getTotalScrobblesPerYear($user);

          if (count($resultCollection) > 0) {
            $results = [];
            $labels = [];

            foreach ($resultCollection as $result) {
              $results[$result['year']] = $result['count'];
            }

            //constitue un tableau pour les 5 dernieres du nombre de scrobble total
            for ($year = date('Y'); $year != (date('Y') - 5); $year--) {
              $labels[] = $year;
              if (isset($results[$year])) {
                $dataSets[] = $results[$year];
              } else {
                $dataSets[] = 0;
              }
            }
            $dataAttribute['labels'] = $labels;
          }

          $dataAttribute['datasets'][]['data'] = $dataSets;
          break;

    }

    $this->chartItem->dataAttribute = $dataAttribute;
    return $dataAttribute;
  }


//  public function getDataForChart(Widget $widget): array
//  {
//    $data = [];
//
//    switch ($widget->getSubTypeWidget()) {
//
//      case Widget::SUB_TYPE__BAR :
//
//        $query = $widget->getQuery();
//        $allResults = $this->em->getConnection()->executeQuery($query)->fetchAllAssociative();
//
//        if (count($allResults) > 0) {
//          $total = 0;
//          foreach ($allResults as $result) {
//            $total += $result['count'];
//          }
//          $total = ['name' => 'All', 'count' => $total];
//
//          $firstResults = array_slice($allResults, 0, 5);
//
//          $results = array_merge([$total], $firstResults);
//
//          foreach ($results as $result) {
//            $data[] = [
//              'label' => $result['name'],
//              'data' => $result['count']
//            ];
//          }
//        }
//        break;
//
//      case Widget::SUB_TYPE__PIE :
//      case Widget::SUB_TYPE__DONUT :
//
//        $query = $widget->getQuery();
//        $allResults = $this->em->getConnection()->executeQuery($query)->fetchAllAssociative();
//
//        if (count($allResults) > 0) {
//          $firstResults = array_slice($allResults, 0, 5);
//
//          $total = 0;
//          foreach ($allResults as $result) {
//            $total += $result['count'];
//          }
//
//          $totalFirstResults = 0;
//          foreach ($firstResults as $result) {
//            $totalFirstResults += $result['count'];
//          }
//          $others = ['name' => 'Others', 'count' => $total - $totalFirstResults];
//
//          $results = array_merge([$others], $firstResults);
//
//          foreach ($results as $result) {
//            $data[] = [
//              'label' => $result['name'],
//              'data' => $result['count']
//            ];
//          }
//        }
//
//        break;
//    }
//
//    // dump($data);
//    return $data;
//  }


  /**
   * Get the options for the chart
   * The options are represented by an array which will be converted to JSON
   * @param ChartOptions|null $chartOptions
   * @return array
   * @throws \Exception
   */
  public function getOptionsForChart(?ChartOptions $chartOptions = null): array
  {
    //if chartOptions is null, we use the subTypeModel of the widget for get it
    if ($chartOptions === null){
      if ($this->subTypeModel === null){
        throw new \Exception('The subTypeModel attribute of StatisticsService is null, use setModels() method before');
      }
      $chartOptions = $this->subTypeModel->getChartOptions();
    }

    $options = [
      'aspectRatio' => $chartOptions->aspectRatio,
      'scales' => [
        'x' => [
          //Tick is the label of the axis, not of the legend
          //https://www.chartjs.org/docs/latest/axes/_common_ticks.html
          'ticks' => [
            'font' => [
              'size' => $chartOptions->ticksFontSizeX
            ],
            'display' => $chartOptions->ticksVisibleX
          ],
          'grid' => [
            'display' => false
          ]
        ],
        'y' => [
          'ticks' => [
            'font' => [
              'size' => $chartOptions->ticksFontSizeY
            ],
            'display' => $chartOptions->ticksVisibleY
          ],
          'max' => $this->getMaxAxisValue(),//max value of the axis
          'grid' => [
            'display' => false
          ]
        ]
      ],
      'plugins' => [
        //Legend is the element outside the chart
        'legend' => [
          'display' => $chartOptions->legendVisible
        ]
      ],
      'indexAxis' => $chartOptions->indexAxis
    ];

    // dump($options);
    return $options;
  }


  /**
   * Get the callback options for the chart
   * The callbacks are represented by constants in the chartItem class
   * @return array
   * @throws \Exception
   */
  public function getCallbackOptionsForChart(): array
  {
    if ($this->widget === null){
      throw new \Exception('The widget attribute of StatisticsService is null, use setWidget() method before');
    }

    $callbackOptions = [];

    switch ($this->widget->getSubTypeWidget()) {

      case Widget::SUB_TYPE__BAR :

        $callbackOptions[] = ChartItem::CALLBACK_OPTION__TRUNCATE_TICKS_X;
        break;

      case Widget::SUB_TYPE__PIE :
      case Widget::SUB_TYPE__DONUT :

        break;
    }

    // dump($callbackOptions);
    return $callbackOptions;
  }


  /**
   * Create the SQL query from the parameters
   * TODO : create DQL datetime functions for use DQL instead of SQL
   * @param array $parameters
   * @return string
   */
  public function createSqlQuery(array $parameters): string
  {
    $query = $parameters['select'] . " " . $parameters['from'] . " ";

    if (isset($parameters['join'])) {
      $query .= $parameters['join'] . " ";
    }

    $query .= $parameters['where'] . " ";

    if (isset($parameters['groupby'])) {
      $query .= $parameters['groupby'] . " ";
    }
    if (isset($parameters['orderby'])) {
      $query .= $parameters['orderby'] . " ";
    }

    return $query;
  }

  /**
   * Get the max value of the axis
   * (For Top Type widget, we keep the second max value)
   * @return int
   */
  private function getMaxAxisValue(): int
  {
    $max = 0;
    $secondMax = 0;

    If (
      //For this type we keep the second max value
      $this->widget->getTypeWidget() == Widget::TYPE__TOP_ARTISTS
      OR $this->widget->getTypeWidget() == Widget::TYPE__TOP_ALBUMS
      OR $this->widget->getTypeWidget() == Widget::TYPE__TOP_TRACKS
    ){
      foreach ($this->chartItem->dataAttribute['datasets'] as $dataSet){
        foreach ($dataSet['data'] as $data){
          if ($data > $max){
            $secondMax = $max;
            $max = $data;
          } else if ($data > $secondMax AND $data <= $max){
            $secondMax = $data;
          }
        }
      }
      $max = $secondMax;

    } else {
      foreach ($this->chartItem->dataAttribute['datasets'] as $dataSet){
        foreach ($dataSet['data'] as $data){
          if ($data > $max){
            $max = $data;
          }
        }
      }
    }

    return $max;
  }

  /**
   * @throws \Exception
   */
  public function getChartTypeForNativeWidget(): string
  {
    if ($this->subTypeModel === null){
      throw new \Exception('The subTypeModel attribute of StatisticsService is null, use setModels() method before');
    }

    return $this->subTypeModel->getChartType();
  }

}