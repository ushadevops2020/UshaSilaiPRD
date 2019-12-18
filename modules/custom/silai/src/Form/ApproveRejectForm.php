<?php
/**
 * @file
 * Contains \Drupal\silai\Form\ApproveRejectForm.
 */
namespace Drupal\silai\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Database\Database;

class ApproveRejectForm extends FormBase {
  /**
   * {@inheritdoc}
   */
	public function getFormId() {
		return 'modal_approve_reject_form';
	}

/**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $options = NULL) 
  {
    $schoolId = $_GET['schoolId'];
    
    
    $form[HASH_PREFIX] = '<div id="wrapper_modal_approve_reject_form">';
    $form[HASH_SUFFIX] = '</div>';

    $form['status_messages'] = [
      HASH_TYPE => 'status_messages',
      '#weight' => -10,
    ];

    $form['field_hidden_school_id'] = [
      HASH_TYPE => 'hidden',
      HASH_VALUE => ($schoolId) ? $schoolId : '',
      
    ];

    $form['field_sil_school_status_remarks'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => $this->t('Remarks'),
      HASH_REQUIRED => TRUE,
      HASH_DEFAULT_VALUE => '',
      HASH_MAXLENGTH => 200,
    ];


    $form[ACTIONS] = array(HASH_TYPE => ACTIONS);
    $form[ACTIONS]['cancel'] = array(
    '#type' => 'button',
    '#value' => t('Cancel'),
    '#weight' => -1,
    HASH_ATTRIBUTES => array('onClick' => 'window.location.href = "/silai-manage-school"; event.preventDefault();'),
    );
    $form[ACTIONS]['approve'] = [
      HASH_TYPE => 'submit',
      HASH_VALUE => $this->t(APPROVE_BUTTON_VALUE),
      HASH_ATTRIBUTES => [
        'class' => [
          'use-ajax',
        ],
      ],
      '#ajax' => [
        'callback' => [$this, 'addUserAjax'],
        'event' => 'click',
      ],
    ];
     $form[ACTIONS]['reject'] = [
      HASH_TYPE => 'submit',
      HASH_VALUE => $this->t(REJECT_BUTTON_VALUE),
      HASH_ATTRIBUTES => [
        'class' => [
          'use-ajax',
        ],
      ],
      '#ajax' => [
        'callback' => [$this, 'addUserAjax'],
        'event' => 'click',
      ],
    ];

    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';

    return $form;
  }

  

  /**
   * AJAX callback handler that displays any errors or a success message.
   */
  public function addUserAjax(array $form, FormStateInterface $form_state) {
      $doamin = _get_current_domain();
      $response = new AjaxResponse();
      
      if ($form_state->hasAnyErrors()) {
        $response->addCommand(new ReplaceCommand('#wrapper_modal_approve_reject_form', $form));
        return $response;
      }
      else {
        $triggringButton = $_REQUEST['_triggering_element_value'];
        if(!empty($triggringButton) && strtolower($triggringButton) == strtolower(APPROVE_BUTTON_VALUE)) {
          $msg = 'School approved succesfully.';
        } elseif (!empty($triggringButton) && strtolower($triggringButton) == strtolower(REJECT_BUTTON_VALUE)) {
          $msg = 'School rejected succesfully.';
        }
        $command = new CloseModalDialogCommand();
        $response->addCommand($command);
        drupal_set_message(t($msg), STATUS);
        $response->addCommand(new RedirectCommand('/silai-manage-school'));
        return $response;
      } 
  }


  /**
   * Method to add and update user's information using custom form
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) { 
    $schoolId = $_REQUEST['field_hidden_school_id'];
    $remarks = $form_state->getValue('field_sil_school_status_remarks');
    $data = [
      'remarks' => $remarks,
    ];

    $triggringButton = $_REQUEST['_triggering_element_value'];
    $masterDataService = \Drupal::service('silai.master_data');

    if(!empty($triggringButton) && strtolower($triggringButton) == strtolower(APPROVE_BUTTON_VALUE)) { 

        $approvalStatus = $masterDataService->approveSchool($schoolId, $data);
    } elseif (!empty($triggringButton) && strtolower($triggringButton) == strtolower(REJECT_BUTTON_VALUE)) {
        $rejectionStatus = $masterDataService->rejectSchool($schoolId, $data);
    }
    return ;

  }

  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   *   An array of configuration object names that are editable if called in
   *   conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames() {
  	return ['config.modal_approve_reject_form'];
  }
}