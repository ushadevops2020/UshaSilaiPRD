<?php

namespace Drupal\silai_school_transfer\Controller;

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
class SilaiSchoolTransferController extends ControllerBase {
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
  
  
  public function silaiSchoolTransfer($nid) { 
      $modalTitle = 'School Transfer Form';
      $response = new AjaxResponse(); 
      // Get the modal form using the form builder.
      $modal_form = $this->formBuilder->getForm('Drupal\silai_school_transfer\Form\silaiSchoolTransfer', $nid);
      // Add an AJAX command to open a modal dialog with the form as the content.
      $response->addCommand(new OpenModalDialogCommand($modalTitle, $modal_form, ['width' => '800', 'height' => 'auto']));
      return $response;
  }
  
  
  public function silaiBulkSchoolNGOTransfer() { 
    $form = \Drupal::formBuilder()->getForm('Drupal\silai_school_transfer\Form\silaiBulkSchoolNGOTransfer');
    return $form;
  }
 
}
