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
class sewingWeeklyPjpPlanForm extends FormBase {

  	/**
   	* {@inheritdoc}
   	*/
  	public function getFormId() {
    return 'sewing_weekly_pjp_plan_form';
  	}

  	/**
  	 * {@inheritdoc}
  	 */
  	public function buildForm(array $form, FormStateInterface $form_state, $options = NULL){
    	$destinationData = drupal_get_destination();
		/***
		** Previous Week Plan Section
		***/
		$forPrevWeek = strtotime('next Monday -2 week');
		$prevWeekStartDate = date("d-M-Y", date('w', $forPrevWeek)==date('w') ? strtotime(date("Y-m-d",$forPrevWeek)." +7 days") : $forPrevWeek);
		$prevWeekEndDate = date("d-M-Y", strtotime(date("Y-m-d",$forPrevWeek)." +6 days"));
		$form['prev_heading'] = [
				    '#markup' => '<i class="fa fa-hand-o-right" aria-hidden="true"></i> Previous Week Plan ('.$prevWeekStartDate.' To '.$prevWeekEndDate.')',
					HASH_PREFIX => '<h3>',
					HASH_SUFFIX => '</h3>',
				];		
		$form['prev_weekly_plan'] = array(
			HASH_TYPE => 'table',
			HASH_TITLE => 'Sample Table',
			'#header' => ['Date', 'Day', '', 'Permanent Journey Plan (PJP) - (office / school code)', 'Purpose of Visit', 'Status Update','PJP Deviation - (office / school code)', 'Purpose of Visit'],
		);	
		for ($i=1; $i<=7; $i++) {
			if($i==1){
				$date = date('w', $forPrevWeek)==date('w') ? strtotime(date("Y-m-d",$forPrevWeek)." +7 days") : $forPrevWeek;
				$form['prev_weekly_plan'][$i]['prev_week_date'] = array(
					HASH_TYPE => 'textfield',
					HASH_TITLE => t('Date'),
					HASH_DEFAULT_VALUE => date("d-M-Y",$date),
					'#title_display' => 'invisible',
					HASH_ATTRIBUTES => ['readonly' => 'readonly', CLASS_CONST => ['readonly-field']],
				);
				$form['prev_weekly_plan'][$i]['prev_week_day'] = array(
					HASH_TYPE => 'textfield',
					HASH_TITLE => t('Day'),
					HASH_DEFAULT_VALUE => date("l",$date),
					'#title_display' => 'invisible',
					HASH_ATTRIBUTES => ['readonly' => 'readonly', CLASS_CONST => ['readonly-field']],
				);
				$form['prev_weekly_plan'][$i]['prev_week_date_hidden'] = array(
					HASH_TYPE => 'hidden',
					HASH_TITLE => t('Date Hidden'),
					HASH_DEFAULT_VALUE => $date,
					'#title_display' => 'invisible',
				);
			}else{
				$day = $i -1;
				$date = strtotime(date("Y-m-d",$forPrevWeek)." +$day days");
				$form['prev_weekly_plan'][$i]['prev_week_date'] = array(
					HASH_TYPE => 'textfield',
					HASH_TITLE => t('Date'),
					HASH_DEFAULT_VALUE => date("d-M-Y",$date),
					'#title_display' => 'invisible',
					HASH_ATTRIBUTES => ['readonly' => 'readonly', CLASS_CONST => ['readonly-field']],
				);
				$form['prev_weekly_plan'][$i]['prev_week_day'] = array(
					HASH_TYPE => 'textfield',
					HASH_TITLE => t('Day'),
					HASH_DEFAULT_VALUE => date("l",$date),
					'#title_display' => 'invisible',
					HASH_ATTRIBUTES => ['readonly' => 'readonly', CLASS_CONST => ['readonly-field']],
				);
				$form['prev_weekly_plan'][$i]['prev_week_date_hidden'] = array(
					HASH_TYPE => 'hidden',
					HASH_TITLE => t('Date Hidden'),
					HASH_DEFAULT_VALUE => $date,
					'#title_display' => 'invisible',
				);
			}
			$form['prev_weekly_plan'][$i]['pjp'] = array(
			  HASH_TYPE => 'textfield',
			  HASH_TITLE => t('Permanent Journey Plan (PJP)'),
			  HASH_REQUIRED => TRUE,
			  '#title_display' => 'invisible',
			  HASH_ATTRIBUTES => ['readonly' => 'readonly'],
			);
			$form['prev_weekly_plan'][$i]['purpose_of_visit_1'] = array(
				HASH_TYPE => SELECTFIELD,
				HASH_TITLE => t('Purpose of Visit'),
				HASH_OPTIONS => [
						'' => SELECT_VALUE, 
						'Routine visit' => 'Routine visit', 
						'Exam' => 'Exam', 
						'Training' => 'Training',
						'Operational Work' => 'Operational Work',
						'Others' => 'Others',
					],
				HASH_REQUIRED => TRUE,
				'#title_display' => 'invisible',
				HASH_ATTRIBUTES => ['disabled' => 'disabled'], 
			);
			$form['prev_weekly_plan'][$i]['status_update'] = array(
				HASH_TYPE => SELECTFIELD,
				HASH_TITLE => t('Status Update'),
				HASH_OPTIONS => [
						'' => SELECT_VALUE, 
						'Plan Consistency' => 'Plan Consistency', 
						'Plan Deviation' => 'Plan Deviation', 
					],
				HASH_REQUIRED => TRUE,
				'#title_display' => 'invisible',
			);
			$form['prev_weekly_plan'][$i]['pjp_deviation'] = array(
			  HASH_TYPE => 'textfield',
			  HASH_TITLE => t('PJP Deviation'),
			  HASH_REQUIRED => TRUE,
			  '#title_display' => 'invisible',
			);
			$form['prev_weekly_plan'][$i]['purpose_of_visit_2'] = array(
			  HASH_TYPE => 'textfield',
			  HASH_TITLE => t('Purpose of Visit'),
			  HASH_REQUIRED => TRUE,
			  '#title_display' => 'invisible',
			);
		}

		/***
		** Current Week Plan Section
		***/
		$forPrevWeek = strtotime('next Monday -1 week');
		$currentWeekStartDate = date("d-M-Y", date('w', $forPrevWeek)==date('w') ? strtotime(date("Y-m-d",$forPrevWeek)." +7 days") : $forPrevWeek);
		$currentWeekEndDate = date("d-M-Y", strtotime(date("Y-m-d",$forPrevWeek)." +6 days"));
		$form['current_heading'] = [
				    '#markup' => '<i class="fa fa-hand-o-right" aria-hidden="true"></i> Current Week Plan ('.$currentWeekStartDate.' To '.$currentWeekEndDate.')',
					HASH_PREFIX => '<h3>',
					HASH_SUFFIX => '</h3>',
				];		
		$form['current_weekly_plan'] = array(
			HASH_TYPE => 'table',
			HASH_TITLE => 'Sample Table',
			'#header' => ['Date', 'Day', '', 'Permanent Journey Plan (PJP) - (office / school code)', 'Purpose of Visit', 'Status Update','PJP Deviation - (office / school code)', 'Purpose of Visit'],
		);
				
		for ($i=1; $i<=7; $i++) {
			if($i==1){
				$date = date('w', $forPrevWeek)==date('w') ? strtotime(date("Y-m-d",$forPrevWeek)." +7 days") : $forPrevWeek;
				$form['current_weekly_plan'][$i]['current_week_date'] = array(
					HASH_TYPE => 'textfield',
					HASH_TITLE => t('Date'),
					HASH_DEFAULT_VALUE => date("d-M-Y",$date),
					'#title_display' => 'invisible',
					HASH_ATTRIBUTES => ['readonly' => 'readonly', CLASS_CONST => ['readonly-field']],
				);
				$form['current_weekly_plan'][$i]['current_week_day'] = array(
					HASH_TYPE => 'textfield',
					HASH_TITLE => t('Day'),
					HASH_DEFAULT_VALUE => date("l",$date),
					'#title_display' => 'invisible',
					HASH_ATTRIBUTES => ['readonly' => 'readonly', CLASS_CONST => ['readonly-field']],
				);
				$form['current_weekly_plan'][$i]['current_week_date_hidden'] = array(
					HASH_TYPE => 'hidden',
					HASH_TITLE => t('Date Hidden'),
					HASH_DEFAULT_VALUE => $date,
					'#title_display' => 'invisible',
				);
			}else{
				$day = $i -1;
				$date = strtotime(date("Y-m-d",$forPrevWeek)." +$day days");
				$form['current_weekly_plan'][$i]['current_week_date'] = array(
					HASH_TYPE => 'textfield',
					HASH_TITLE => t('Date'),
					HASH_DEFAULT_VALUE => date("d-M-Y",$date),
					'#title_display' => 'invisible',
					HASH_ATTRIBUTES => ['readonly' => 'readonly', CLASS_CONST => ['readonly-field']],
				);
				$form['current_weekly_plan'][$i]['current_week_day'] = array(
					HASH_TYPE => 'textfield',
					HASH_TITLE => t('Day'),
					HASH_DEFAULT_VALUE => date("l",$date),
					'#title_display' => 'invisible',
					HASH_ATTRIBUTES => ['readonly' => 'readonly', CLASS_CONST => ['readonly-field']],
				);
				$form['current_weekly_plan'][$i]['current_week_date_hidden'] = array(
					HASH_TYPE => 'hidden',
					HASH_TITLE => t('Date Hidden'),
					HASH_DEFAULT_VALUE => $date,
					'#title_display' => 'invisible',
				);
			}
			$form['current_weekly_plan'][$i]['pjp'] = array(
			  HASH_TYPE => 'textfield',
			  HASH_TITLE => t('Permanent Journey Plan (PJP)'),
			  HASH_REQUIRED => TRUE,
			  '#title_display' => 'invisible',
			);
			$form['current_weekly_plan'][$i]['purpose_of_visit_1'] = array(
				HASH_TYPE => SELECTFIELD,
				HASH_TITLE => t('Purpose of Visit'),
				HASH_OPTIONS => [
						'' => SELECT_VALUE, 
						'Routine visit' => 'Routine visit', 
						'Exam' => 'Exam', 
						'Training' => 'Training',
						'Operational Work' => 'Operational Work',
						'Others' => 'Others',
					],
				HASH_REQUIRED => TRUE,
				'#title_display' => 'invisible',
			);
			$form['current_weekly_plan'][$i]['status_update'] = array(
				HASH_TYPE => SELECTFIELD,
				HASH_TITLE => t('Status Update'),
				HASH_OPTIONS => [
						'' => SELECT_VALUE, 
						'Plan Consistency' => 'Plan Consistency', 
						'Plan Deviation' => 'Plan Deviation', 
					],
				HASH_REQUIRED => TRUE,
				'#title_display' => 'invisible',
				HASH_ATTRIBUTES => ['disabled' => 'disabled'],
			);
			$form['current_weekly_plan'][$i]['pjp_deviation'] = array(
			  HASH_TYPE => 'textfield',
			  HASH_TITLE => t('PJP Deviation'),
			  HASH_REQUIRED => TRUE,
			  '#title_display' => 'invisible',
			  HASH_ATTRIBUTES => ['readonly' => 'readonly'],
			);
			$form['current_weekly_plan'][$i]['purpose_of_visit_2'] = array(
			  HASH_TYPE => 'textfield',
			  HASH_TITLE => t('Purpose of Visit'),
			  HASH_REQUIRED => TRUE,
			  '#title_display' => 'invisible',
			  HASH_ATTRIBUTES => ['readonly' => 'readonly'],
			);
		}
		

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
  			HASH_VALUE => $this->t('Submit'), 
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
		$userId = \Drupal::currentUser()->id();
  		$field = $form_state->getValues();
		print_r($field['prev_weekly_plan']);
		print_r($field['current_weekly_plan']);
		//print_r($_REQUEST);
		die;
  		
  	}
}