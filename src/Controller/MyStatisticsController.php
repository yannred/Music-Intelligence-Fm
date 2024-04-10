<?php

namespace App\Controller;

use App\Data\Notification;
use App\Entity\Widget;
use App\Entity\WidgetGrid;
use App\Form\WidgetType;
use App\Service\StatisticsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyStatisticsController extends CustomAbsrtactController
{
  protected StatisticsService $statisticsService;
  protected Security $security;
  protected WidgetGrid $userWidgetGrid;

  public function __construct(
    EntityManagerInterface $entityManager,
    Security $security,
    StatisticsService $statisticsService
  )
  {
    parent::__construct($entityManager);
    $this->statisticsService = $statisticsService;
    $this->security = $security;

    //create a new grid if no grid is found
    $grid = $this->entityManager->getRepository(WidgetGrid::class)->findOneBy(['user' => $this->security->getUser(), 'defaultGrid' => true]);
    if ($grid === null) {
      $this->userWidgetGrid = $this->statisticsService->createGrid();
    } else {
      $this->userWidgetGrid = $grid;
    }
  }

  /**
   * Display the user's statistics page (dashboard for widgets)
   * @param Request $request
   * @return Response
   */
  #[Route('/myPage/myStatistics', name: 'app_my_statistics')]
  public function index(Request $request): Response
  {
    return $this->render('my_statistics/index.html.twig', [
      'activeNavbarItem' => $request->get('_route'),
    ]);
  }


  /**
   * Modal for creating or updating a user statistic widget
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
      $action = $this->generateUrl('app_widget_new_statistic');
    } else {
      $widget = $this->entityManager->getRepository(Widget::class)->findOneBy(['id' => $id, 'widgetGrid' => $this->userWidgetGrid]);
      $paramView['modify'] = true;
      $action = $this->generateUrl('app_widget_new_statistic', ['id' => $id]);
    }
    $form = $this->createForm(WidgetType::class, $widget, ['action' => $action]);
    $form->handleRequest($request);
    $paramView['form'] = $form;

    if ($form->isSubmitted() && $form->isValid()) {

      try {
        $success = true;

        /** @var Widget $widget */
        $widget = $form->getData();

        //TODO : create custom validator
        //Type and SubType are required
        if ($widget->getTypeWidget() == 0) {
          $notifications[] = new Notification('Statistic type is required', 'warning');
          $success = false;
        }
        if ($widget->getSubTypeWidget() == 0) {
          $notifications[] = new Notification('Chart type is required', 'warning');
          $success = false;
        }

        //All Controls OK
        if ($success) {

          $widget->validateDateRange();

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
    }

    $paramView['notifications'] = $notifications;
    return $this->render($view, $paramView, $response);
  }

}
