<?php

namespace Drupal\silai\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Database\Database;

/**
 * SurveyQuetionnaireController class.
 */
class SurveyQuetionnaireController extends ControllerBase {

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilder
   */
  protected $formBuilder;

  /**
   * The SurveyQuetionnaireController constructor.
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
/**
 * editSurveyQuetionnaireForm function.
 */
  	public function editSurveyQuetionnaireForm($nid) {
	  	$build = [];
	  	$build['heading'] = [ HASH_TYPE => 'markup'];
	  	$build['form'] = $this->formBuilder()->getForm('Drupal\silai\Form\SchoolSurveyQuetionnaire', $nid);
	  	return $build;
	}
}