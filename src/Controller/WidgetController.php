<?php

namespace App\Controller;

use App\Data\GridstackItem;
use App\Data\Notification;
use App\Entity\Widget;
use App\Entity\WidgetGrid;
use App\Form\WidgetType;
use App\Service\StatisticsService;
use App\Service\UtilsService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WidgetController extends CustomAbsrtactController
{

  protected LoggerInterface $logger;
  protected Security $security;
  protected StatisticsService $statisticsService;
  protected UtilsService $utilsService;

  protected WidgetGrid $userWidgetGrid;

  public function __construct(
    EntityManagerInterface $entityManager,
    LoggerInterface $logger,
    Security $security,
    StatisticsService $statisticsService,
    UtilsService $utilsService
  )
  {
    parent::__construct($entityManager);

    $this->entityManager = $entityManager;
    $this->logger = $logger;
    $this->security = $security;
    $this->statisticsService = $statisticsService;
    $this->utilsService = $utilsService;

    //create a new grid if no grid is found
    $grid = $this->entityManager->getRepository(WidgetGrid::class)->findOneBy(['user' => $this->security->getUser(), 'defaultGrid' => true]);
    if ($grid === null) {
      $this->createGrid();
    } else {
      $this->userWidgetGrid = $grid;
    }
  }

  /**
   * Return a JSON response with the gridstack items
   * @return Response
   */
  #[Route('/myPage/grid', name: 'app_widget_load_grid', methods: ['GET'])]
  public function loadGrid(): Response
  {
    $response = new Response();

    $gridStackItems = [];

    $widgetEntities = $this->userWidgetGrid->getWidgets();

    foreach ($widgetEntities as $widgetEntity) {
      $gridStackWidget = new GridstackItem($widgetEntity);
      $modelWidget = $widgetEntity->getTypeModel();

      $gridStackWidget->content = $this->statisticsService->generateContent($widgetEntity, $modelWidget->getContentParameters());

      $gridStackItems[] = $gridStackWidget;
    }

    $response->setStatusCode(Response::HTTP_OK);
    $response->setContent(json_encode($gridStackItems));

    return $response;
  }


  /**
   * Page for creating or updating a statistic
   * @param Request $request
   * @param $id
   * @return Response
   */
  #[Route('/myPage/myStatistics/new/{id}', name: 'app_widget_new_statistic')]
  public function statisticForm(Request $request, $id = null): Response
  {
    $response = new Response();
    $view = 'my_statistics/new.html.twig';
    $notifications = [];
    $paramView = [];

    $creating = true;
    if ($id != null ){
      $creating = false;
    }

    $gridRepository = $this->entityManager->getRepository(WidgetGrid::class);

    if ($creating){
      $widget = new Widget();
    } else {
      $widget = $this->entityManager->getRepository(Widget::class)->findOneBy(['id' => $id, 'widgetGrid' => $this->userWidgetGrid]);
      $paramView['modify'] = true;
    }
    $form = $this->createForm(WidgetType::class, $widget);
    $form->handleRequest($request);
    $paramView['form'] = $form;

    if ($form->isSubmitted() && $form->isValid()) {

      try {
        $success = true;

        /** @var Widget $widget */
        $widget = $form->getData();

        //controls
        //Type and SubType are required
        if ($widget->getTypeWidget() == 0) {
          $notifications[] = new Notification('Statistic type is required', 'warning');
          $success = false;
        }
        if ($widget->getSubTypeWidget() == 0) {
          $notifications[] = new Notification('Chart type is required', 'warning');
          $success = false;
        }

        //Custom date range is required
        if ($widget->getDateType() == Widget::DATE_TYPE__CUSTOM){
          if ($widget->getDateFrom() === null && $widget->getDateTo() === null) {
            $notifications[] = new Notification('Custom date range is required', 'warning');
            $success = false;
          }
          if (! $widget->validateDateRange()) {
            $notifications[] = new Notification('Invalid date range', 'warning');
            $success = false;
          }
        }


        if ($success) {

          //All Controls OK
          $widget->setWidgetGrid($this->userWidgetGrid);

          $model = $widget->getTypeModel();
          $widget->applyModel($model, $creating);
          if ($creating){
            $widget->setPositionX(0);
            $widget->setPositionY($gridRepository->getNextPositionY($this->userWidgetGrid));
          }

          $queryParameters = $model->getQueryParameters($widget);
          $query = $this->statisticsService->createSqlQuery($queryParameters);
          $widget->setQuery($query);

          $this->entityManager->persist($widget);
          $this->entityManager->flush();

          $notifications[] = new Notification('Statistic created', 'success');
          $response->setStatusCode(Response::HTTP_CREATED);
          $view = 'my_statistics/index.html.twig';

        } else {

          //Error during controls
          $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        }

      } catch (\Exception $e) {
        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $notifications[] = new Notification('Internal error : ' . $e->getMessage(), 'danger');
      }
    }

    if ($form->isSubmitted() && $form->isValid() && $response->getStatusCode() == Response::HTTP_CREATED){
      $response->setStatusCode(Response::HTTP_SEE_OTHER );
      $response->headers->set('Location', $this->generateUrl('app_my_statistics'));
    } else {
      $response = null;
    }


    $paramView['notifications'] = $notifications;
    return $this->render($view, $paramView, $response);
  }


  /**
   * Update chart datas (position, size)
   * @param Request $request
   * @return Response
   */
  #[Route('/myPage/widget/{id}', name: 'app_widget_update', methods: ['UPDATE'])]
  public function updateWidget(Request $request): Response
  {
    $response = new Response();

    $body = $request->getContent();
    $parameters = json_decode($body, true);

    $widget = $this->entityManager->getRepository(Widget::class)->findOneBy(['id' => $request->get('id'), 'widgetGrid' => $this->userWidgetGrid]);

    if ($widget === null) {
      $response->setStatusCode(Response::HTTP_NOT_FOUND);
    } else {
      $widget->setWidth($parameters['w']);
      $widget->setHeight($parameters['h']);
      $widget->setPositionX($parameters['x']);
      $widget->setPositionY($parameters['y']);

      $this->entityManager->persist($widget);
      $this->entityManager->flush();

      $response->setStatusCode(Response::HTTP_CREATED);
      $response->setContent(json_encode(['id' => $widget->getId()]));
    }

    return $response;
  }


  /**
   * Delete a widget
   * @param Request $request
   * @return Response
   */
  #[Route('/myPage/widget/{id}', name: 'app_widget_delete', methods: ['DELETE'])]
  public function deleteWidget(Request $request, ): Response
  {
    $response = new Response();

    $widget = $this->entityManager->getRepository(Widget::class)->findOneBy(['id' => $request->get('id'), 'widgetGrid' => $this->userWidgetGrid]);

    if ($widget === null) {
      $response->setStatusCode(Response::HTTP_NOT_FOUND);
    } else {
      $this->entityManager->remove($widget);
      $this->entityManager->flush();
      $response->setStatusCode(Response::HTTP_OK);
    }

    return $response;
  }

  //TODO : move in WidgetGrid Entity
  private function createGrid()
  {
    $grid = new WidgetGrid();
    $grid->setUser($this->security->getUser());
    $grid->setDefaultGrid(true);
    $grid->setCode('default');
    $grid->setWording('Created by default (' . date('Y-m-d H:i:s').')');

    $this->entityManager->persist($grid);
    $this->entityManager->flush();

    $this->userWidgetGrid = $grid;
  }

}
