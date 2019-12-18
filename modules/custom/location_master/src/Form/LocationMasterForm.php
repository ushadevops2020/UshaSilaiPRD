<?php
/**
 * @file
 * Contains Drupal\location_master\LocationMasterForm
 */
namespace Drupal\location_master\Form;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ChangedCommand;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
class LocationMasterForm extends FormBase {
 
	/**
	 * Implementation of Get Form Id
	*/		
	public function getFormId() {
		return 'ajax_module_form';
	}

	/**
	 * Implementation of Build Form
	*/
	public function buildForm(array $form, FormStateInterface $form_state) {
		$form[USER_NAME] = array(
			'#type' => 'textfield',
			'#title' => 'Username',
			'#description' => 'Please enter in a username',
			'#ajax' => array(
			'callback' => 'Drupal\location_master\Form\LocationMasterForm::usernameValidateCallback',
			'wrapper' => 'edit-output',
			'effect' => 'fade',
			'event' => 'change',
			'progress' => array(
				'type' => 'throbber',
				'message' => t('Verifying entry...'),
			),
			),
		);
		return $form;
	}

 	/**
	 * Implementation of Submit Form
	*/
	public function submitForm(array &$form, FormStateInterface $form_state) {
		drupal_set_message('Nothing Submitted. Just an Example.');
	}
	
	/**
	 * Implementation of Call Back usename validation
	 * @params: $form as array()
	 */

	public function usernameValidateCallback(array &$form, FormStateInterface $form_state) {
		#Instantiate an AjaxResponse Object to return.
		$ajax_response = new AjaxResponse();
		#Check if Username exists and is not Anonymous User ('').
		if (user_load_by_name($form_state->getValue(USER_NAME)) && $form_state->getValue(USER_NAME) != false) {
			$text = 'User Found';
			$color = 'green';
		} else {
			$text = 'No User Found';
			$color = 'red';
		}
		$ajax_response->addCommand(new HtmlCommand('#edit-user-name--description', $text));
		$ajax_response->addCommand(new InvokeCommand('#edit-user-name--description', 'css', array('color', $color)));
		return $ajax_response;
	}
}