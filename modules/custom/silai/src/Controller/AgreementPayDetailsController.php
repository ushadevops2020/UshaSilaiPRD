<?php

namespace Drupal\silai\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Database\Database;

/**
 * AgreementPayDetailsController class.
 */
class AgreementPayDetailsController extends ControllerBase {

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilder
   */
  protected $formBuilder;

  /**
   * The AgreementPayDetailsController constructor.
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
 * agreementPayDetailsForm function.
 */
  	public function agreementPayDetailsForm($nid) {
	    $modalTitle = 'Payment Details';
	    $response = new AjaxResponse();
	    
	    // Get the modal form using the form builder.
	    $modal_form = $this->formBuilder->getForm('Drupal\silai\Form\AgreementPaymentDetailsForm', $nid);

	    // Add an AJAX command to open a modal dialog with the form as the content.
	    $response->addCommand(new OpenModalDialogCommand($modalTitle, $modal_form, ['width' => '800', 'height' => 'auto']));

	    return $response;



	}
}