<?php

namespace Drupal\silai_school_transfer\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Entity\EntityInterface;

use Drupal\node\Entity\Node;
use Drupal\Core\Url;

class silaiSchoolTransfer extends FormBase {


	public function getFormId() {
		return 'silai_school_transfer_form';
	}

	public function buildForm(array $form, FormStateInterface $form_state) {
		$nid = $form_state->getBuildInfo()['args'][0];
		$destinationData = drupal_get_destination();
    	
		$form[HASH_PREFIX] = '<div id="wrapper_modal_school_transfer_form">';
	    $form[HASH_SUFFIX] = '</div>';
		
		$form['school_list'] = array(
			HASH_TYPE => ENTITYAUTOCOMPLETEFIELD,
			'#target_type' => 'node',
			HASH_TITLE => t('School Code'),
			HASH_REQUIRED => TRUE,
			HASH_TAG => TRUE,
			'#selection_handler' => 'default',
			'#selection_settings' => [
			  'target_bundles' =>['silai_school' => 'silai_school'],
			],
		  );
		$form['reason'] = array(
			HASH_TYPE => TEXTAREAFIELD,
			HASH_TITLE => t("Reason"),
			HASH_REQUIRED => TRUE,
			//HASH_DEFAULT_VALUE => ($school_data['additional_notes']) ? $school_data['additional_notes'] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => array('reason')],
		);
		$form[ACTIONS]['submit'] = array(
			HASH_TYPE => 'submit',
			HASH_VALUE => 'Transfer',
			'#attributes' => [
		        'class' => [
		          'use-ajax',
		        ],
		    ],
		    '#ajax' => [
		        'callback' => [$this, 'schoolTransferAjax'],
		        'event' => 'click',
		    ],
		);
		$form['#attached']['library'][] = 'core/drupal.dialog.ajax';
		return $form;
	}
	 /**
	   * AJAX callback handler that displays any errors or a success message.
	   */
	  public function schoolTransferAjax(array $form, FormStateInterface $form_state) {
	      $doamin = _get_current_domain();
	      $response = new AjaxResponse();
	      
	      if ($form_state->hasAnyErrors()) {
	        $response->addCommand(new ReplaceCommand('#wrapper_modal_school_transfer_form', $form));
	        return $response;
	      }
	      else {
	        $command = new CloseModalDialogCommand();
	        $response->addCommand($command);
	        drupal_set_message(t('School Learner has been sucessfully Transfer.'), STATUS);
	        $response->addCommand(new RedirectCommand('/silai-manage-school'));
	        return $response;
	      } 
	  }
	public function validateForm(array &$form, FormStateInterface $form_state) {
		$field = $form_state->getValues();
		$nid = $form_state->getBuildInfo()['args'][0];
    }
    public function submitForm(array &$form, FormStateInterface $form_state) {	
		$nid = $form_state->getBuildInfo()['args'][0];	
		$field = $form_state->getValues();
		$getSchoolIds = $field['school_list'];
		$reason = $field['reason'];
		foreach($getSchoolIds as $getSchoolId){
			$newSchoolId = $getSchoolId['target_id'];
		}
		$oldSchoolData = Node::load($nid);
		$oldSchoolData->set('field_silai_transferred_to', $newSchoolId);
		$oldSchoolData->set('field_sil_school_approval_status', 4);
		$oldSchoolData->set('field_silai_school_reason', $reason);
		$oldSchoolData->save();
		
		$newSchoolData = Node::load($newSchoolId);
		$newSchoolData->set('field_silai_transferred_from', $nid);
		$newSchoolData->save();
		
		$newSchoolName = $newSchoolData->getTitle();
		$query = \Drupal::entityQuery(NODE)->condition(TYPE, 'silai_learners_manage');
        $query->condition('field_silai_school', $nid);
        $learnerIds = $query->execute();
		foreach($learnerIds as $learnerId){
			$learnerData = Node::load($learnerId);
			$learnerData->set('field_silai_school', $newSchoolId);
			$learnerData->set('field_silai_school_name', $newSchoolName);
			$learnerData->save();
		}
	} 
}



















