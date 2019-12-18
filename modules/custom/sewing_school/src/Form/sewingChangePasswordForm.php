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
use Drupal\user\Entity\User;

/**
 * acceptLearnersForm class.
 */
class sewingChangePasswordForm extends FormBase {

  	/**
   	* {@inheritdoc}
   	*/
  	public function getFormId() {
    return 'sewing_change_password_form';
  	}

  	/**
  	 * {@inheritdoc}
  	 */
  	public function buildForm(array $form, FormStateInterface $form_state, $options = NULL){
    	$destinationData = drupal_get_destination();
		$current_user = \Drupal::currentUser();
		$roles = $current_user->getRoles();
		$uid = \Drupal::currentUser()->id();
		$userData = \Drupal\user\Entity\User::load($uid);
		$userName = $userData->getUsername();
		
		//print_r($userData);
		//die;
		/* $form['field_user_name'] = [
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t('User Name'),
			HASH_REQUIRED => TRUE,
			HASH_DEFAULT_VALUE => ($userName) ? $userName : '',
			HASH_MAXLENGTH => 15,
			HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC], 'readonly' => 'readonly'],
		]; */
		$form[HASH_PREFIX] = '<div id="wrapper_modal_change_password_form">';
		$form[HASH_SUFFIX] = '</div>';
		$form['field_user_password'] = [
			HASH_TYPE => 'password',
			HASH_TITLE => t('New Password'),
			HASH_REQUIRED => TRUE,
			HASH_MAXLENGTH => 20,
		];
		$form['field_user_confirm_password'] = [
			HASH_TYPE => 'password',
			HASH_TITLE => t('Confirm New Password'),
			HASH_REQUIRED => TRUE,
			HASH_MAXLENGTH => 20,
		];
		
		# Cancle and save button
  		$form[ACTIONS] = array(HASH_TYPE => ACTIONS);
		$form[ACTIONS]['cancel'] = array(
			HASH_TYPE => 'button',
			'#value' => t('Cancel'),
			'#weight' => -1,
			HASH_ATTRIBUTES => array('onClick' => 'window.location.href = "'.$destinationData['destination'].'"; event.preventDefault();'),
		);
  		$form[ACTIONS]['submit'] = array(
  			HASH_TYPE => 'submit',
  			HASH_VALUE => $this->t('Change'), 
			'#attributes' => [
				'class' => [
					'use-ajax',
				],
			],
			'#ajax' => [
				'callback' => [$this, 'changePasswordAjax'],
				'event' => 'click',
			],
  		);
    	return $form;
  	}
  	/**
  	 * {@inheritdoc}
   	*/
  	public function validateForm(array &$form, FormStateInterface $form_state) {
		$password = $form_state->getValue('field_user_password');
		$confirmPassword = $form_state->getValue('field_user_confirm_password');
		if($password != $confirmPassword) {
			$form_state->setErrorByName('field_user_password', t('Password And Confirm password is not coreect.')); 
		}
  	}
	 /**
     * AJAX callback handler that displays any errors or a success message.
     */
      public function changePasswordAjax(array $form, FormStateInterface $form_state) {
		  $destinationData = drupal_get_destination();
          $doamin = _get_current_domain();
          $response = new AjaxResponse(); 
          if ($form_state->hasAnyErrors()) {
              $response->addCommand(new ReplaceCommand('#wrapper_modal_change_password_form', $form));
              return $response;
          }
          else {
            $command = new CloseModalDialogCommand();
            $response->addCommand($command);
            drupal_set_message(t('Your password has been changed successfully.'), STATUS);
            $response->addCommand(new RedirectCommand($destinationData['destination']));
            return $response;
          } 
      }
  	/**
   	* Method to add and update user's information using custom form
   	* {@inheritdoc}
   	*/
  	public function submitForm(array &$form, FormStateInterface $form_state) {
		$current_user = \Drupal::currentUser();
		$roles = $current_user->getRoles();
		$uid = \Drupal::currentUser()->id();
  		$field = $form_state->getValues();
		$password = $field['field_user_password'];
		//print_r($field['field_user_confirm_password']);
		//die;
		$user = \Drupal\user\Entity\User::load($uid);
        $user->setPassword($password);
        $user->save();
		//drupal_set_message(t('Your password is change successfully.'), 'status');
  	}
}