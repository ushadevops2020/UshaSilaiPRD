<?php

namespace Drupal\sewing_school\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Database\Database;
use Drupal\Core\Entity\EntityInterface;
use Drupal\node\Entity\Node;

/**
 * acceptLearnersForm class.
 */
class schoolApprovalStatusFlow extends FormBase {

  	/**
   	* {@inheritdoc}
   	*/
  	public function getFormId() {
    return 'sewing_approval_school_form';
  	}

  	/**
  	 * {@inheritdoc}
  	 */
  	public function buildForm(array $form, FormStateInterface $form_state, $options = NULL){
      $nid = $form_state->getBuildInfo()['args'][0];
    	$destinationData = drupal_get_destination();
      $form[HASH_PREFIX] = '<div id="wrapper_modal_approve_school_form">';
      $form[HASH_SUFFIX] = '</div>';

      $form['status_messages'] = [
        HASH_TYPE => 'status_messages',
        '#weight' => -10,
      ];
  		$form['approval_status'] = array(
  	        HASH_TYPE => SELECTFIELD,
  	        HASH_TITLE => t('Approval'),
  	        HASH_OPTIONS => [
  	            '' => t(SELECT_VALUE),
  	            '1' => t('Approved'),
  	            '2' => t('Rejected'),
                //'3' => t('Terminated'),
  	          ],
  	        HASH_REQUIRED => TRUE,
  	        HASH_DEFAULT_VALUE => '',
  	    );
	    /*$form['approval_comment'] = array(
  			HASH_TYPE => TEXTAREAFIELD,
  			HASH_TITLE => t("Comment"),
  			HASH_REQUIRED => FALSE,
  			HASH_DEFAULT_VALUE => '',
  			HASH_REQUIRED => TRUE,
  		);*/
      # Cancle and save button   - 
  		$form[ACTIONS] = array(HASH_TYPE => ACTIONS);
      $form[ACTIONS]['cancel'] = array(
      '#type' => 'button',
      '#value' => t('Cancel'),
      '#weight' => -1,
      HASH_ATTRIBUTES => array('onClick' => 'window.location.href = "/manage-school"; event.preventDefault();'),
      );
  		$form[ACTIONS]['submit'] = array(
  			HASH_TYPE => 'submit',
  			HASH_VALUE => $this->t('Submit'), 
        '#attributes' => [
              'class' => [
                'use-ajax',
              ],
          ],
          '#ajax' => [
              'callback' => [$this, 'acceptSchoolAjax'],
              'event' => 'click',
          ],
  		);
    	return $form;
  	}
     /**
     * AJAX callback handler that displays any errors or a success message.
     */
      public function acceptSchoolAjax(array $form, FormStateInterface $form_state) {
          $doamin = _get_current_domain();
          $response = new AjaxResponse(); 
          if ($form_state->hasAnyErrors()) {
              $response->addCommand(new ReplaceCommand('#wrapper_modal_approve_school_form', $form));
              return $response;
          }
          else {
            $command = new CloseModalDialogCommand();
            $response->addCommand($command);
            drupal_set_message(t('Status has been successfully updated.'), STATUS);
            $response->addCommand(new RedirectCommand('/manage-school'));
            return $response;
          } 
      }
  	/**
  	 * {@inheritdoc}
   	*/
  	public function validateForm(array &$form, FormStateInterface $form_state) {
    
  	}
  	/**
   	* Method to add and update user's information using custom form
   	* {@inheritdoc}
   	*/
  	public function submitForm(array &$form, FormStateInterface $form_state) {
  		$nid = $form_state->getBuildInfo()['args'][0];
  		$field = $form_state->getValues();
      if($field['approval_status'] == 1){
        //$node = Node::load($nid);
        //$node->set('field_sew_school_approval_status', $field['approval_status']); 
       // $node->save();
        $masterDataService = \Drupal::service('sewing.master_data');
        $schoolId = $nid;
        $data = [
          'remarks' => $field['approval_status'],
        ];
        $approvalStatus = $masterDataService->approveSchool($schoolId, $data);
      }else if($field['approval_status'] == 2){
        $node = Node::load($nid);
        $node->set('field_sew_school_approval_status', $field['approval_status']); 
        $node->save();
      }/*else if($field['approval_status'] == 3){
        $node = Node::load($nid);
        $node->set('field_sew_school_approval_status', $field['approval_status']); 
        $node->save();
      }*/
  		
  	}
}