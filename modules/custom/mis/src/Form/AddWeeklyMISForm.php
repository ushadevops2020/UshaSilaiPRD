<?php
/**
 * @file
 * Contains \Drupal\silai\Form\AddWeeklyMISForm.
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

class AddWeeklyMISForm extends FormBase {
  /**
   * {@inheritdoc}
   */
	public function getFormId() {
		return 'add_weekly_mis_form';
	}

/**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $misId = $_GET['id'];
    $record = array();
    
    if (isset($misId) && $misId != '') {
        $conn = Database::getConnection();
        $query = $conn->select('usha_weekly_mis', 'm')
            ->condition('id', $misId)
            ->fields('m');
        $record = $query->execute()->fetchAssoc();
    }
    $misDataService = \Drupal::service('silai_mis.monthly_quarterly_list');

    $mandatoryFields = array('num_classical_schools_till_date',
                              'num_satellite_schools_till_date',
                              'num_new_partnership_explored',
                              'black_machines_sold_silai_schools',
                              'white_machines_sold_silai_schools',
                              'num_appliances_sold_silai_schools',
                              'case_studies_shared_head_office',
                              'certificates_issued_to_teachers',
                              'certificate_issued_to_learners');
    $dropDownFields   = array('week_start_date', 'monthly_mis_of_last_fy_css', 'monthly_mis_of_current_fy_css');
    $textAreaFields   = array('feedback_ngo', 'comment', 'new_partnership_explored');
    $weeksData = $misDataService->getWeeks($misId);
    $notNumericValue = array('new_partnership_explored', 'one_model_name', 'two_model_name', 'details_sold_silai_schools', 'details_of_case_studies_shared', 'feedback_ngo', 'comment');
    foreach(WEEKLY_MIS_FIELDS as $key=>$value) {
      switch ($key) {
        case 'week_start_date':
          $form[$key] = array (
            HASH_TYPE => 'select',
            HASH_TITLE => t('Week'),
            HASH_OPTIONS => $weeksData,
            HASH_REQUIRED => TRUE,
            HASH_DEFAULT_VALUE => (isset($record[$key]) && $misId) ? $record[$key].'@@'.$record['week_end_date']:'',
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
            HASH_DEFAULT_VALUE => (isset($record[$key]) && $misId) ? $record[$key]:'',
          );
          break;     
        default:

      }
      if(!in_array($key, $notNumericValue)) {
        $attribute = array(ONLY_NUMERIC_VALUE);
      } else {
        $attribute = '';
      } 
      if(!in_array($key, $dropDownFields)) {
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
      '#attributes' => array('onClick' => 'window.location.href = "/weekly-mis-list"; event.preventDefault();'),
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
    $numericFields = array('num_classical_schools_till_date','num_satellite_schools_till_date','num_classical_schools_visited','num_satellite_school_visited','num_visits_schools_centers','total_visits_ngo_office','num_new_partnership_explored','black_machines_sold_silai_schools','white_machines_sold_silai_schools','num_appliances_sold_silai_schools','trainings_ongoing_classical_schools','trainings_ongoing_satellite_schools','case_studies_shared_head_office','certificates_issued_to_teachers','certificate_issued_to_learners','num_of_photo','num_of_ss_board_replacement','board_received_head_office','board_installed_ss','silai_book_received_head_office','silai_book_provided_ss','num_new_cs_training_completed','num_new_ss_training_completed','num_women_training_completed','monthly_mis_of_last_fy_css','monthly_mis_of_current_fy_css','total_teacher_certificate_stock','total_learners_certificate_stock');
    $mandatoryFields = array('num_classical_schools_till_date',
                              'num_satellite_schools_till_date',
                              'num_new_partnership_explored',
                              'black_machines_sold_silai_schools',
                              'white_machines_sold_silai_schools',
                              'num_appliances_sold_silai_schools',
                              'case_studies_shared_head_office',
                              'certificates_issued_to_teachers',
                              'certificate_issued_to_learners');
    // foreach ($numericFields  as $key => $value) {
    //   if (!is_numeric($form_state->getValue($value)) && !in_array($form_state->getValue($value), $mandatoryFields)) {
    //       $form_state->setErrorByName($value, $this->t('Mobile number is too short.'));
    //   }
    // }
    // foreach ($form_state->getValues() as $key => $value) {
    //   if (!is_numeric($form_state->getValue($key)) && in_array($form_state->getValue($value), $mandatoryFields) && ) {
    //       $form_state->setErrorByName($key, $this->t('Mobile number is too short.'));
    //   }
    // } 
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
    if(in_array(ROLE_SILAI_PC,$roles)){
      $locationId = $user->field_user_location->target_id;
    } elseif(in_array(ROLE_SILAI_NGO_ADMIN,$roles)){
      $masterDataService = \Drupal::service('silai.master_data');
      $locationIdArr = $masterDataService->getNgoLocationIds($current_user->id());
      $locationId = current($locationIdArr);
    }
    $textFields   = array('feedback_ngo','comment','new_partnership_explored','details_sold_silai_schools','details_of_case_studies_shared');
    
    foreach ($form_state->getValues() as $key => $value) { 
        if(in_array($key, array_keys(WEEKLY_MIS_FIELDS))) {
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
        'message' => '',
        'location' => '',
        'created_by' => $userId
    ];

    $database = Database::getConnection(); 
      if (isset($misId) && $misId != '') {
        $dataArr['updated_by'] = $userId;
        $dataArr['updated_date'] = time(); 
        $query    = $database->update('usha_weekly_mis')->fields($dataArr)->condition('id', $misId)->execute();
        drupal_set_message("Weekly MIS has been successfully updated");
        $data['message'] = WEEKLY_MIS_UPDATE_MESSAGE;
      } else {
        $dataArr['created_by'] = $userId;
        $dataArr['created_date'] = time();
        $dataArr['pc_uid'] = $userId;
        $dataArr['location'] = $locationId;
        $query    = $database->insert('usha_weekly_mis')->fields($dataArr)->execute();
        drupal_set_message('Weekly MIS has been successfully added.');
        $message = preg_replace('/{.*}/', $nameWithRole, WEEKLY_MIS_ADD_MESSAGE);
        $data['message'] = $message;
      }

      $masterDataService = \Drupal::service('silai.master_data');
      if(in_array($roles[1], [ROLE_SILAI_NGO_ADMIN])) {
          $ngoData = $masterDataService->getLinkedNgoForUser($userId);
            $node_storage = \Drupal::entityManager()->getStorage('node');
            // Load a single node.
            $node = $node_storage->load($ngoData[$userId]);

            $currentUserLoc = $node->get('field_ngo_location')->getValue()[0]['target_id'];
            $adminUsers = $masterDataService->getUsersByRole(ROLE_SILAI_HO_ADMIN);
            $pcUsers =  $masterDataService->getUsersByRole(ROLE_SILAI_PC, $currentUserLoc); 
            $targetUsers = array_merge($pcUsers, $adminUsers); 

        } else {
          $currentUserLoc = $user->field_user_location->target_id;
           $adminUsers = $masterDataService->getUsersByRole(ROLE_SILAI_HO_ADMIN);
           $targetUsers = $adminUsers;  
        }
        $data['location'] = $currentUserLoc;
        if(!empty($targetUsers)){
          $masterDataService->notificationAlert($data, $targetUsers);
        }
    return;
  }  

}