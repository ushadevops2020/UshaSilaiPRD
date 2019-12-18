<?php
/**
 * @file
 * Contains \Drupal\silai\Form\AddSewingWeeklyMISForm.
 */
namespace Drupal\mis\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Database\Database;
use Drupal\user\Entity\User;

class AddSewingWeeklyMISForm extends FormBase {
  /**
   * {@inheritdoc}
   */
	public function getFormId() {
		return 'add_sewing_weekly_mis_form';
	}

/**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $misId = $_GET['id'];
    $record = array();
    $current_user = \Drupal::currentUser(); 
    $roles = $current_user->getRoles();
    $destinationData = drupal_get_destination();
    if (isset($misId) && $misId != '') {
        $form[HASH_TITLE] = t('Edit Weekly MIS');
        $conn = Database::getConnection();
        $query = $conn->select('usha_sewing_weekly_mis', 'm')
            ->condition('id', $misId)
            ->fields('m');
        $record = $query->execute()->fetchAssoc();
    }
    $misDataService = \Drupal::service('silai_mis.sewing_mis');
    $mandatoryFields = array('week_start_date',);
    $dropDownFields   = array('week_start_date');
    
    $autoCompleteFields   = array('school_code_visited_week');
    $weeksData = $misDataService->getSewingWeeks($misId, 'usha_sewing_weekly_mis');
    $notNumericValue = array('school_code_visited_week', 'black_machine_model_name', 'white_machine_model_name', 'details_of_uj_accessories');
    if(in_array(ROLE_SEWING_HO_ADMIN, $roles)) {
      $disabled = TRUE;
    } else{
      $disabled = FALSE;
    }
    foreach(SEWING_WEEKLY_MIS_FIELDS as $key=>$value) {
      switch ($key) {
        case 'week_start_date':
          $form[$key] = array (
            HASH_TYPE => 'select',
            HASH_TITLE => t('Week'),
            HASH_OPTIONS => $weeksData,
            HASH_REQUIRED => TRUE,
            HASH_ATTRIBUTES => array('disabled' => $disabled),
            HASH_DEFAULT_VALUE => (isset($record[$key]) && $misId) ? $record[$key].'@@'.$record['week_end_date']:'',
          );
          break;
        case 'school_code_visited_week':
          $form[$key] = array(
              HASH_TYPE => 'textfield',
              HASH_TITLE => t($value),
              '#autocomplete_route_name' => 'mis.school_code_autocomplete',
              '#autocomplete_route_parameters' => array('field_name' => 'name', 'count' => 10),
              HASH_ATTRIBUTES => array('disabled' => $disabled),
              HASH_DEFAULT_VALUE => (isset($record[$key]) && $misId) ? $record[$key]:'',
          );
          break;  
        case in_array($key, $mandatoryFields) && !in_array($key, $dropDownFields):
          $type = 'textfield';
          $required = TRUE;
          break;
        case !in_array($key, $dropDownFields) && !in_array($key, $textAreaFields):
          $type = 'textfield';
          $required = FALSE;
          break;
        case in_array($key, $textAreaFields):
          $type = TEXTAREAFIELD;
          $required = FALSE;    
          break;
        case in_array($key, $dropDownFields):
          $form[$key] = array (
            HASH_TYPE => 'select',
            HASH_TITLE => t($value),
            HASH_OPTIONS => RADIOSFIELD_NO_YES_OPTIONS,
            HASH_REQUIRED => TRUE,
            HASH_DEFAULT_VALUE => (isset($record[$key]) && $misId) ? $record[$key]:'',
          );
          break;     
        default:

      }
      if(!in_array($key, $notNumericValue)) {
        $attribute = array(ONLY_NUMERIC_VALUE);
        $required = TRUE;
      } else {
        $attribute = '';
        $required = FALSE;
      } 
      if(!in_array($key, $dropDownFields) && !in_array($key, $autoCompleteFields)) {
        $form[$key] = array (
          HASH_TYPE => $type,
          HASH_TITLE => t($value),
          HASH_REQUIRED => $required,
          HASH_ATTRIBUTES => [CLASS_CONST => $attribute],
          HASH_DEFAULT_VALUE => (isset($record[$key]) && $misId) ? $record[$key]:'',
        );
      } 
    }

    $form[ACTIONS] = array(HASH_TYPE => ACTIONS);
    $form[ACTIONS]['cancel'] = array(
      HASH_TYPE => 'button',
      HASH_VALUE => t('Cancel'),
      '#weight' => -1,
      '#attributes' => array('onClick' => 'window.location.href = "' . $destinationData['destination'].'"; event.preventDefault();'),
    );
    $form[ACTIONS]['send'] = [
      HASH_TYPE => 'submit',
      HASH_VALUE => $this->t('Save')
    ];
    return $form;
  }



  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    $user = User::load($current_user->id());
    $locationId = $user->field_user_location->target_id;
    if(in_array(ROLE_SEWING_SSI,$roles)){
      $schoolCodeArray = explode(',', $form_state->getValue('school_code_visited_week'));
      foreach($schoolCodeArray as $schoolCode) {
        if(!empty($schoolCode)) {
          $query = \Drupal::entityQuery(NODE)
            ->condition(TYPE, 'sewing_school')
            ->condition('field_sew_school_approval_status', APPROVED_STATUS)
            ->condition('field_location', $locationId)
            ->condition('field_sewing_school_code', trim($schoolCode));
          $schoolCodeArr = $query->execute();
          if(empty($schoolCodeArr)) {
              $form_state->setErrorByName('school_code_visited_week', $this->t('School Code is not valid '.trim($schoolCode).', please enter valid school code.'));
          }
        }  
      }
    }  
  }

  /**
   * Method to add and update user's information using custom form
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $userId = \Drupal::currentUser()->id();
    $misId = $_GET['id'];
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    $user = User::load($current_user->id());
    $nameWithRole = $user->get('field_first_name')->value.' '. $user->get('field_last_name')->value.' ('.$roles[1].')' ;
    if(in_array(ROLE_SEWING_SSI,$roles)){
      $locationId = $user->field_user_location->target_id;
    }

    $textFields   = array('details_new_partnership_explored', 'black_machine_model_name', 'white_machine_model_name', 'feedback_from_school','comment','details_of_uj_accessories', 'school_code_visited_week');
    foreach ($form_state->getValues() as $key => $value) { 
        if(in_array($key, array_keys(SEWING_WEEKLY_MIS_FIELDS))) {
            if($key == 'week_start_date' || $key == 'week_end_date') {
              $dateArr = explode('@@',$value);
              $value = (isset($dateArr[0]) && $dateArr[0] != '')?$dateArr[0]:0;
              $dataArr['week_end_date'] = $dateArr[1];
            } elseif(!in_array($key,$textFields)) {
              $value = (isset($value) && $value != '')?$value:0;
            }
            $dataArr[$key] = $value;
        }
    }
    $data = [
    'sender_role' => $roles[1],
    'receiver_id' => '',
    'receiver_role' => '',
    'message' => $message,
    'location' => $locationId,
    'created_by' => $current_user->id()
    ];
    $database = Database::getConnection(); 
      if (isset($misId) && $misId != '') {
        $dataArr['updated_by'] = $userId;
        $dataArr['updated_date'] = time(); 
        $query    = $database->update('usha_sewing_weekly_mis')->fields($dataArr)->condition('id', $misId)->execute();
        drupal_set_message("Weekly MIS has been successfully updated");
        $data['message'] = WEEKLY_MIS_UPDATE_MESSAGE;
      } else {
        $dataArr['created_by'] = $userId;
        $dataArr['created_date'] = time();
        $dataArr['location'] = $locationId;
        $query    = $database->insert('usha_sewing_weekly_mis')->fields($dataArr)->execute();
        drupal_set_message('Weekly MIS has been successfully added.');
        $data['message'] = preg_replace('/{.*}/', $nameWithRole, WEEKLY_MIS_ADD_MESSAGE);
      }

      $masterDataService = \Drupal::service('sewing.master_data');
      if(in_array($roles[1], [ROLE_SEWING_SSI])) {
          $masterSilaiDataService = \Drupal::service('silai.master_data');
          $targetUsers = $masterSilaiDataService->getUsersByRole(ROLE_SEWING_HO_ADMIN);
          if(!empty($targetUsers)){
            $masterDataService->sewingNotificationAlert($data, $targetUsers);
          }
      }  

    return;
  }  

}