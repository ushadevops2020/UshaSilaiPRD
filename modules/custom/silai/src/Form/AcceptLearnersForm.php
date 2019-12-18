<?php

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
use Drupal\Core\Entity\EntityInterface;
use Drupal\node\Entity\Node;

/**
 * acceptLearnersForm class.
 */
class AcceptLearnersForm extends FormBase {

  	/**
   	* {@inheritdoc}
   	*/
  	public function getFormId() {
    return 'accept_learners_form';
  	}

  	/**
  	 * {@inheritdoc}
  	 */
  	public function buildForm(array $form, FormStateInterface $form_state, $options = NULL){
      $nid = $form_state->getBuildInfo()['args'][0];
    	$destinationData = drupal_get_destination();
  		$form['approval_status'] = array(
  	        HASH_TYPE => SELECTFIELD,
  	        HASH_TITLE => t('Approval'),
  	        HASH_OPTIONS => [
  	            '' => t(SELECT_VALUE),
  	            '1' => t('Accept'),
  	            '2' => t('Reject'),
  	          ],
  	        HASH_REQUIRED => TRUE,
  	        HASH_DEFAULT_VALUE => '',
  	    );
  	    $form['approval_comment'] = array(
  			HASH_TYPE => TEXTAREAFIELD,
  			HASH_TITLE => t("Comment"),
  			HASH_REQUIRED => FALSE,
  			HASH_DEFAULT_VALUE => '',
  			HASH_REQUIRED => TRUE,
  		);
      	# Cancle and save button   - 
  		$form[ACTIONS] = array(HASH_TYPE => ACTIONS);
  		$form[ACTIONS]['submit'] = array(
  			HASH_TYPE => 'submit',
  			HASH_VALUE => $this->t('Save'),
  		);
    	return $form;
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
  		$node = Node::load($nid);
  		$node->set('field_approve_status', $field['approval_status']); 
  		$node->set('field_comment', $field['approval_comment']); 
  		$node->save();
  	}

}