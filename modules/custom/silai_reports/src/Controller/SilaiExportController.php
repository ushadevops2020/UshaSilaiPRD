<?php

namespace Drupal\silai_reports\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilder;
use Drupal\views\Views;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Database\Database;
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax\RedirectCommand;
/**
 * Defines HelloController class.
 */
class SilaiExportController extends ControllerBase {
  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilder
   */
  protected $formBuilder;

  /**
   * The AcceptLearnersController constructor.
   *
   * @param \Drupal\Core\Form\FormBuilder $formBuilder
   *   The form builder.
   */
  public function __construct(FormBuilder $formBuilder) {
    $this->formBuilder = $formBuilder;
  }
	
  /**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The Drupal service container.
   *
   * @return static
   */ 
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_builder')
    );
  }
  

  #learner Export
  public function silaiLearnerExport() { 
	file_unmanaged_delete('sites/default/files/custom-export/silai-learner-export.csv');
	$view = Views::getView('manage_learners');
	$display = $view->preview('data_export_1');
	file_unmanaged_save_data($display , 'sites/default/files/custom-export/silai-learner-export.csv', FILE_EXISTS_REPLACE);
    $return = ['data' =>1, STATUS => 1];
    return new JsonResponse($return);
  }
  #school Export
  public function silaiSchoolExport() { 
	file_unmanaged_delete('sites/default/files/custom-export/silai-school-export.csv');
	$view = Views::getView('silai_manage_school');
	$display = $view->preview('data_export_1');
	file_unmanaged_save_data($display , 'sites/default/files/custom-export/silai-school-export.csv', FILE_EXISTS_REPLACE);
    $return = ['data' =>1, STATUS => 1];
    return new JsonResponse($return);
  }
  #Monthly MIS Export
  public function silaiMonthlyMISExport() { 
	file_unmanaged_delete('sites/default/files/custom-export/silai-monthly-mis-export.csv');
	$view = Views::getView('manage_monthly_quarterly_mis');
	$display = $view->preview('data_export_1');
	file_unmanaged_save_data($display , 'sites/default/files/custom-export/silai-monthly-mis-export.csv', FILE_EXISTS_REPLACE);
    $return = ['data' =>1, STATUS => 1];
    return new JsonResponse($return);
  }
  #school Survey Export
  public function silaiSchoolServeyExport() {  
	file_unmanaged_delete('sites/default/files/custom-export/silai-school-survey-export.csv');
	$view = Views::getView('silai_manage_school');
	$display = $view->preview('data_export_2');
	file_unmanaged_save_data($display , 'sites/default/files/custom-export/silai-school-survey-export.csv', FILE_EXISTS_REPLACE);
    $return = ['data' =>1, STATUS => 1];
    return new JsonResponse($return);
  }
  #
}
