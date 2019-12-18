<?php

namespace Drupal\silai_form_update\Controller;

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
use Drupal\Core\Entity\Query;
/**
 * Defines HelloController class.
 */
class SilaiSchoolFieldUpdateController extends ControllerBase {
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
  
  
	public function silaiSchoolGenderUpdate() { 
	die('die command');
		$schoolIds =\Drupal::entityQuery('node')
			->condition('type', 'silai_school')
			->execute();
		$i = 1 ;
		foreach($schoolIds as $schoolId){
			$schoolData = Node::load($schoolId);
			$schoolData->set('field_silai_school_gender', 2);
			$schoolData->save();
			echo 'Sno:- '.$i.' And Sschool Id:- '. $schoolId;
			echo '<br>';
			$i++;
		}			
		die("School has been successfully updated. die command");
        return $response;
	}
  
  
 
 
}
