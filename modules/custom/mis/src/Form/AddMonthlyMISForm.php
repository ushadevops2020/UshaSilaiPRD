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
use Drupal\node\Entity\Node;

class AddMonthlyMISForm extends FormBase {
  /**
   * {@inheritdoc}
   */
	public function getFormId() {
		return 'add_monthly_mis_form';
	}

/**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $misId = $_GET['id'];
    $disabled = FALSE;
    $masterDataService = \Drupal::service('silai.master_data');
    $misDataService = \Drupal::service('silai_mis.monthly_quarterly_list');
    $userId = \Drupal::currentUser()->id();
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    if(in_array(ROLE_SILAI_PC,$roles)){
      $locationId = $user->field_user_location->target_id;
    } elseif(in_array(ROLE_SILAI_NGO_ADMIN,$roles)){
      $locationId = $masterDataService->getNgoLocationIds($current_user->id());
    } else {
      $locationId = '';
    }
    $optionsForState = $masterDataService->getStatesByLocationId($locationId);

    $optionsForState[''] = '- Select a value -';
    asort($optionsForState);
    $record = array();
    
    if (isset($misId) && $misId != '') {
        $conn = Database::getConnection();
        $query = $conn->select('usha_monthly_mis', 'm')
            ->condition('id', $misId)
            ->fields('m');
        $record = $query->execute()->fetchAssoc();
        $optionsForDistrict = $masterDataService->getDistrictsByStateId($record['state']);
        $optionsForBlock = $masterDataService->getBlocksByDistrictId($record['district']);
        $optionsForVillage = $masterDataService->getVillagesByBlockId($record['block']);
        $optionsForSC = $masterDataService->getSchoolCodeByVillageId($record['village']);
        
        $monthyQuaterly = $misDataService->monthlyQuarterlyList($record['fiscal_year'], $record['monthly_quarterly_type'], $record['school_code'], $record['id']);
        $fiscalyr = $misDataService->getMQFiscalYear($record['monthly_quarterly_type'], $record['school_code'], $misId);
    }
    if(isset($misId) && $misId != '' && empty($record)) {
        drupal_set_message('Id is not exists.', 'error');
    }
    $mandatoryFields = array('state', 'district', 'block', 'village');
    $textAreaFields   = array('remark');
    $dropDownFields   = array('fiscal_year', 'monthly_quarterly_type','school_type', 'monthly_quarterly_value', 'state', 'district', 'block', 'village', 'ss_sign_board_received', 'sb_prominent_place', 'condition_of_sb', 'machine_condition', 'no_of_student', 'activity_code', 'machine_remark', 'additional_information', 'students_practice');
    $dropDownLocationFields   = array('monthly_quarterly_type','state', 'district', 'block', 'village', 'school_code');
    $autoComplete = array('school_code');
    $schoolType = $this->getSilaiSchoolType();
    $numericValue = array('no_of_learners', 'no_of_learners_course_completed', 'fee_charged_learners_month', 'income_from_learners_fee', 'income_from_tailoring', 'income_from_sewing_machine_repairing', 'total_income');
    $hoadminDisableFields = array('state', 'district', 'block', 'village', 'school_code', 'fiscal_year', 'monthly_quarterly_type', 'monthly_quarterly_value');

    foreach(MONTHLY_MIS_FIELDS as $key=>$value) {
      switch ($key) {
        case 'monthly_quarterly_type':
          $type = SELECTFIELD;
          $option = MONTHLY_QUARTERLY_TYPE_OPTIONS;
          $required = TRUE;
          $attribute = 'edit-field-monthly-quarterly-type'; 
          break;
        case 'activity_code':
          $type = SELECTFIELD;
          $option = MIS_SCHOOL_WORKING_STATUS_OPTIONS;
          $required = TRUE;
          $attribute = 'edit-field-activity-code'; 
          break;
        case 'no_of_student':
          $type = SELECTFIELD;
          $option = MIS_REASON_FOR_NON_WORKING_OF_SCHOOL_OPTIONS;
          $required = FALSE;
          $attribute = 'edit-field-no-of-student'; 
          break;
		case 'machine_remark':
          $type = SELECTFIELD;
          $option = MONTHLY_QUARTERLY_IF_NO_THEN_WHAT_IS_THE_PROBLEM; 
          $required = FALSE;
		  $attribute = 'edit-field-machine-remark'; 
          break; 
		case 'additional_information':
          $type = SELECTFIELD;
          $option = MONTHLY_QUARTERLY_ACTIVITIES_DONE_FOR_INCREASING_LEARNERS; 
          $required = FALSE;
		  $attribute = 'edit-field-additional_information'; 
          break;  
		case 'students_practice':
          $type = SELECTFIELD;
          $option = MONTHLY_QUARTERLY_WHERE_DO_STUDENTS_PRACTICE;
          $required = FALSE;
		  $attribute = 'edit-field-students_practice'; 
          break;
        case 'fiscal_year':
          $type = SELECTFIELD;
          $option = $fiscalyr;
          $required = TRUE;
          $attribute = 'edit-field-fiscal-year';
          break;  
        case 'monthly_quarterly_value':
          $type = SELECTFIELD;
          $option = $monthyQuaterly;
          $attribute = 'edit-field-monthly-value';
          $required = TRUE;
          break; 
        case 'school_type':
          $type = SELECTFIELD;
          $option = $schoolType;
          $attribute = 'edit-field-school-type';
          $required = FALSE;
          break;
        case 'condition_of_sb':
          $type = SELECTFIELD;
          $option = CONDITION_OF_SIGN_BOARD;
          $attribute = 'edit-field-condition-of-sb';
          $required = FALSE;
          break;
        case 'state':
          $type = SELECTFIELD;
          $option = $optionsForState;
          $attribute = 'edit-field-mis-state';
          $required = TRUE;
          break;
        case 'district':
          $type = SELECTFIELD;
          $option = $optionsForDistrict;
          $attribute = 'edit-field-mis-district';
          $required = TRUE;
          break;
        case 'block':
          $type = SELECTFIELD;
          $option = $optionsForBlock;
          $attribute = 'edit-field-mis-block';
          $required = TRUE;
          break;    
        case 'village':
          $type = SELECTFIELD;
          $option = $optionsForVillage;
          $attribute = 'edit-field-mis-village';
          $required = TRUE;
          break;
        case 'school_code':
          $type = SELECTFIELD;
          $option = $optionsForSC;
          $attribute = 'edit-field-mis-school-code';
          $required = TRUE;
          break;    
        case in_array($key, $dropDownFields):
            $type = SELECTFIELD;
            $option = NO_YES_OPTIONS;
            $attribute = 'edit-field-mis'.$key;
            $required = FALSE;
          break; 
        case in_array($key, $textAreaFields):
          $type = TEXTAREAFIELD;
          $required = FALSE;    
          break;
        /* case 'date_of_training':
          $type = 'date';
          $required = FALSE;
          $record[$key] = date('Y-m-d', $record[$key]);
          break;  */           
        default:
          $type = 'textfield';
          break;
      }
      if(in_array($key, $numericValue)) {
        $attribute1 = array(ONLY_NUMERIC_VALUE);
      } else {
        $attribute1 = '';
      }
      $form['field_hidden_misid'] = array(
        HASH_TYPE => FIELD_HIDDEN,
        HASH_ATTRIBUTES => array('id' => 'field-hidden-misid'),
        HASH_VALUE => ($misId) ? $misId : '',
      );
      if(in_array(ROLE_SILAI_HO_ADMIN, $roles) && in_array($key, $hoadminDisableFields)) {
        $disabled = TRUE;
      } else{
        $disabled = FALSE;
      }
      if(!in_array($key, $dropDownFields) && !in_array($key, $autoComplete)) {
        $form[$key] = array (
          HASH_TYPE => $type,
          HASH_TITLE => t($value),
          HASH_REQUIRED => $required,
          HASH_ATTRIBUTES => [CLASS_CONST => $attribute1],
          HASH_DEFAULT_VALUE => (isset($record[$key]) && $misId) ? $record[$key]:'',
        );
      } else {
        $form[$key] = array (
          HASH_TYPE => $type,
          HASH_TITLE => t($value),
          HASH_OPTIONS => $option,
          HASH_DEFAULT_VALUE => (isset($record[$key]) && $misId) ? $record[$key]:'',
          HASH_ATTRIBUTES => array('id' => $attribute, 'disabled' => $disabled),
          HASH_REQUIRED => $required,
        );
      } 
    }

    $form[ACTIONS] = array(HASH_TYPE => ACTIONS);
    $form[ACTIONS]['cancel'] = array(
      HASH_TYPE => 'button',
      HASH_VALUE => t('Cancel'),
      '#weight' => -1,
      '#attributes' => array('onClick' => 'window.location.href = "/monthly-quarterly-mis-list"; event.preventDefault();'),
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
      // print_r($form_state->getValue('quarterly_value'));die;
      if($form_state->getValue('machine_condition') == 0 && $form_state->getValue('machine_remark') == '') {
          $form_state->setErrorByName('machine_remark', $this->t('Please enter remark for machine condition.'));
      }
  }

  /**
   * Method to add and update user's information using custom form
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $database = Database::getConnection(); 
    $userId = \Drupal::currentUser()->id();
    $user = User::load(\Drupal::currentUser()->id());
    $userRoles = $user->getRoles();

    $nameWithRole = $user->get('field_first_name')->value.' '. $user->get('field_last_name')->value.' ('.$userRoles[1].')' ;

    $misId = $_GET['id'];
    $dataArr = array();
    $ngoId = '';
    $textFields   = array('monthly_quarterly_value', 'quarterly_value','machine_remark','usefulness_of_course','additional_information','remark','students_practice','activities_status','brand_of_machine', 'enquiry', 'feedback');
    $schoolCode = $form_state->getValue('school_code');
    if(!empty($schoolCode)) {
      $data = node::load($schoolCode);
      $ngoId = $data->field_name_of_ngo->target_id;
      $schoolType = $data->field_school_type->target_id;
      $locationId = $data->field_silai_location->target_id;
    }
    foreach ($form_state->getValues() as $key => $value) {
        if(in_array($key, array_keys(MONTHLY_MIS_FIELDS))) {
          if($key == 'date_of_training') {
            $value = !empty($value)?strtotime($value):strtotime("now");
          }elseif(!in_array($key,$textFields)) {
            $value = (isset($value) && $value != '')?$value:strtotime("now");
          }
          $dataArr[$key] = $value;
        }
    }

       $data = [
        'sender_role' => $userRoles[1],
        'receiver_id' => '',
        'receiver_role' => '',
        'message' => '',
        'location' => '',
        'created_by' => $userId
    ];

      if (isset($misId) && $misId != '') {
        $dataArr['location'] = $locationId;
        $dataArr['ngo_id'] = $ngoId;
        $dataArr['school_type'] = $schoolType;
        $dataArr['updated_by'] = $userId;
        $dataArr['updated_date'] = time(); 
        $query    = $database->update('usha_monthly_mis')->fields($dataArr)->condition('id', $misId)->execute();
        drupal_set_message("Monthly/Quaterly MIS has been successfully updated");
        
        $data['message'] = MONTHLY_MIS_UPDATE_MESSAGE;
      } else {
        $dataArr['location'] = $locationId;
        $dataArr['mother_name_ngo_partner'] = '';
        $dataArr['ngo_id'] = $ngoId;
        $dataArr['school_type'] = $schoolType;
        $dataArr['created_date'] = time();
        $dataArr['created_by'] = $userId;
        $query    = $database->insert('usha_monthly_mis')->fields($dataArr)->execute();
        drupal_set_message('Monthly/Quaterly MIS has been successfully added.');
        $message = preg_replace('/{.*}/', $nameWithRole, MONTHLY_MIS_ADD_MESSAGE);
        $data['message'] = $message;
      }

      $masterDataService = \Drupal::service('silai.master_data');
      if(in_array($userRoles[1], [ROLE_SILAI_NGO_ADMIN])) {
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

  /**
   * Method get Scholl Type list from Content type
   * {@inheritdoc}
   */
  public function getSilaiSchoolType() {
    $schoolTypeCodeArr = array();
    $query = \Drupal::entityQuery(NODE)
        ->condition(TYPE, 'silai_school_type_master');
    $query->condition(STATUS, 1);  
    $nids = $query->execute();
    #Load multiple nodes
    $node_storage = \Drupal::entityManager()->getStorage(NODE);
    $schoolTypes = $node_storage->loadMultiple($nids);
    foreach ($schoolTypes as $n) {
        $schoolTypeCodeArr[$n->nid->value] = $n->title->value.' ('.$n->field_silai_school_type_code->value.')';
    }
    return $schoolTypeCodeArr;
  }  


  public function getFiscalYear($misId='') {

  }

}