<?php
/**
 * @file
 * Contains \Drupal\silai\Form\AddWeeklyMISForm.
 */
namespace Drupal\sewing_revenue\Form;

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
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Datetime\DrupalDateTime;



class GenerateFeeReceiptForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'generate_fee_receipt_form';
  }

/**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $state = array();
    $masterDataService = \Drupal::service('sewing.master_data');
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $userLocation =  $user->field_user_location->target_id;
    $stateByLocation = $masterDataService->getStatesByLocationId($userLocation);
    $townbylocation = $masterDataService->getTownBylocationId($userLocation);
    $schoolbylocation = $masterDataService->getSchoolBylocationId($userLocation);
    
    $stateByLocation[''] = '-Select-';
    asort($stateByLocation);
    $townbylocation[''] = '-Select-';
    asort($townbylocation);    
    $schoolbylocation[''] = '-Select-';
    asort($schoolbylocation);

    $revenueHead = $masterDataService->getRevenueHead();
    $revenueStudentTax= $masterDataService->getRevenueHead(REVENUE_HEAD_STUDENT_FEE_NID);
    $neftDate = $chequeDate = '';
    $feeId = $_GET['id'];
    if (isset($feeId) && $feeId != '') {
        $conn = Database::getConnection();
        $query = $conn->select('usha_generate_fee_receipt', 'f')
            ->condition('id', $feeId)
            ->fields('f');
        $record = $query->execute()->fetchAssoc();
        $schoolDetails = $masterDataService->getSchoolDataBySchoolCode($record['school_id']);
        if(!empty($record['neft_transaction_date']) && !empty($record['neft_transaction_time'])) {
          $neftDate = date("m/d/Y",$record['neft_transaction_date']). ' ' .date("h:i:s A",$record['neft_transaction_time']);
          $neftDate = new DrupalDateTime($neftDate);
        }
       // if(!empty($record['cheque_transaction_date']) && !empty($record['cheque_transaction_time'])) {
        if(!empty($record['cheque_transaction_date'])) {
          $chequeDate = date("Y-m-d",$record['cheque_transaction_date']);
          //$chequeDate = date("m/d/Y",$record['cheque_transaction_date']). ' ' .date("h:i:s A",$record['cheque_transaction_time']);
          //$chequeDate = new DrupalDateTime($chequeDate);
          $chequeDate = $chequeDate;
        }
        $disabled = true;
    } else {
        $disabled = false;
    }
    $form['field_hidden_fee_id'] = [
        HASH_TYPE => FIELD_HIDDEN,
        HASH_DEFAULT_VALUE => $feeId,
        HASH_ATTRIBUTES => array('id' => 'hidden-fee-id'),
    ];
	if(isset($feeId)){
		$form['receipt_number'] = [
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t('Receipt Number'),
			HASH_DEFAULT_VALUE => (isset($record['receipt_number']) && $feeId) ? $record['receipt_number']:'',
			HASH_ATTRIBUTES => array('readonly' => 'readonly'),
			HASH_PREFIX => '<div class= "receipt-number">',
			HASH_SUFFIX => '</div>'
		];
	}
    $form['swr_state'] = [
        HASH_TYPE => SELECTFIELD,
        HASH_TITLE => t('State'),
        HASH_OPTIONS => $stateByLocation,
        // HASH_DEFAULT_VALUE => $stateByLocation ? $stateByLocation : ''
        HASH_DEFAULT_VALUE => (isset($record['state_id']) && $feeId) ? $record['state_id']:$stateByLocation,
        HASH_ATTRIBUTES => array('disabled' => $disabled),
    ];

    $form['swr_town'] = [
        HASH_TYPE => SELECTFIELD,
        HASH_TITLE => t('Town'),
        HASH_OPTIONS => $townbylocation,
        // HASH_DEFAULT_VALUE => $townbylocation ? $townbylocation : ''
        HASH_DEFAULT_VALUE => (isset($record['town_id']) && $feeId) ? $record['town_id']:$townbylocation,
        HASH_ATTRIBUTES => array('disabled' => $disabled),
    ];
    if(isset($feeId)){
      $schoolCode = Node::load($record['school_id'])->field_sewing_school_code->value;
      $form['swr_school_code'] = [
          HASH_TYPE => SELECTFIELD,
          HASH_TITLE => t('School Code'),
          HASH_OPTIONS => [$record['school_id'] => $schoolCode],
          HASH_REQUIRED => TRUE,
          HASH_DEFAULT_VALUE => (isset($record['school_id']) && $feeId) ? $record['school_id']:$schoolbylocation,
          HASH_ATTRIBUTES => array('disabled' => $disabled),
      ];
    }else{
      $form['swr_school_code'] = [
          HASH_TYPE => SELECTFIELD,
          HASH_TITLE => t('School Code'),
          HASH_OPTIONS => $schoolbylocation,
          HASH_REQUIRED => TRUE,
          HASH_DEFAULT_VALUE => (isset($record['school_id']) && $feeId) ? $record['school_id']:$schoolbylocation,
          HASH_ATTRIBUTES => array('disabled' => $disabled),
      ];
    }
    // $form['swr_school_code'] = [
    //     HASH_TYPE => SELECTFIELD,
    //     HASH_TITLE => t('School Code'),
    //     HASH_OPTIONS => $schoolbylocation,
    //     HASH_REQUIRED => TRUE,
    //     HASH_DEFAULT_VALUE => (isset($record['school_id']) && $feeId) ? $record['school_id']:$schoolbylocation,
    //     HASH_ATTRIBUTES => array('disabled' => $disabled),
    // ];

    $form['swr_school_type'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('School Type'),
      HASH_DEFAULT_VALUE => (isset($schoolDetails['schoolType']) && $feeId) ? $schoolDetails['schoolType']:'',
      HASH_ATTRIBUTES => array('readonly' => 'readonly'),
      HASH_PREFIX => '<div class= "school-details">'
    ];
    $form['swr_school_type_id'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('School Type ID'),
      HASH_DEFAULT_VALUE => (isset($schoolDetails['schoolTypeId']) && $feeId) ? $schoolDetails['schoolTypeId']:'',
      HASH_ATTRIBUTES => array('readonly' => 'readonly')
    ];
    $form['swr_school_grade'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Grade'),
      HASH_DEFAULT_VALUE => (isset($schoolDetails['grade']) && $feeId) ? $schoolDetails['grade']:'',
      HASH_ATTRIBUTES => array('readonly' => 'readonly'),
    ];
    $form['swr_sap_code'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('SAP Code'),
      HASH_DEFAULT_VALUE => (isset($schoolDetails['sapCode']) && $feeId) ? $schoolDetails['sapCode']:'',
      HASH_ATTRIBUTES => array('readonly' => 'readonly'),
    ];
    $form['swr_school_admin'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('School Admin'),
      HASH_DEFAULT_VALUE => (isset($schoolDetails['schoolAdmin']) && $feeId) ? $schoolDetails['schoolAdmin']:'',
      HASH_ATTRIBUTES => array('readonly' => 'readonly'),
    ];
    $form['swr_no_student'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('NO of Student'),
      HASH_DEFAULT_VALUE => (isset($schoolDetails['noOfStudents']) && $feeId) ? $schoolDetails['noOfStudents']:'',
      HASH_ATTRIBUTES => array('readonly' => 'readonly'),
    ];
    $form['swr_course'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Course Offered'),
      HASH_DEFAULT_VALUE => (isset($schoolDetails['noOfCourses']) && $feeId) ? $schoolDetails['noOfCourses']:'',
      HASH_ATTRIBUTES => array('readonly' => 'readonly'),
      HASH_SUFFIX => '</div>'
    ];
	//if($record['want_to_add_student_fee']!=1){
		$form['swr_revenue_head_type'] = [
		  HASH_TYPE => 'radios',
		  HASH_TITLE =>  'Revenue Type  <a id= "reset-revenue-type"> Reset</a>',
		  HASH_OPTIONS => $revenueHead,
		  HASH_ATTRIBUTES => [CLASS_CONST => array('revenue-head-class'), 'disabled' => $disabled],
		  HASH_DEFAULT_VALUE => (isset($record['revenue_head_type']) && $feeId) ? $record['revenue_head_type']:'',
		];
	//}
    
    $form['field_hidden_revenue_tax'] = [
      HASH_TYPE => FIELD_HIDDEN,
      HASH_ATTRIBUTES => array('id' => 'revenue-tax'),
    ];
    $form['field_hidden_revenue_student_tax'] = [
      HASH_TYPE => FIELD_HIDDEN,
      HASH_DEFAULT_VALUE => $revenueStudentTax[REVENUE_HEAD_STUDENT_FEE_NID],
      HASH_ATTRIBUTES => array('id' => 'revenue-student-tax'),
    ];
    $form['field_hidden_revenue_value'] = [
        HASH_TYPE => FIELD_HIDDEN,
        HASH_ATTRIBUTES => array('id' => 'max-fee-amount'),
    ];
    $form['field_hidden_affiliation_nid'] = [
      HASH_TYPE => FIELD_HIDDEN,
      HASH_DEFAULT_VALUE => REVENUE_HEAD_AFFILIATION_FEE_NID,
      HASH_ATTRIBUTES => array('id' => 'edit-swr-affiliation-nid'),
    ];
    $form['field_hidden_revenue_renewal_nid'] = [
      HASH_TYPE => FIELD_HIDDEN,
      HASH_DEFAULT_VALUE => REVENUE_HEAD_RENEWAL_FEE_NID,
      HASH_ATTRIBUTES => array('id' => 'edit-swr-renewal-nid'),
    ];
	//if($record['want_to_add_student_fee']!=1){
		$form['swr_revenue_head_value'] = [
		  HASH_TYPE => TEXTFIELD,
		  HASH_TITLE => t('Fee Amount <span id=max-fee-amount-display></span>'),
		  HASH_DEFAULT_VALUE => (isset($record['revenue_head_value']) && $feeId) ? $record['revenue_head_value']:'',
		  HASH_STATES => array(
			  VISIBLE => array(
				  ':input[name=swr_revenue_head_type]' => array('checked' =>TRUE),
			  ),
			  REQUIRED => array(
				  ':input[name=swr_revenue_head_type]' => array('checked' =>TRUE),
			  ),
			),
		  HASH_ATTRIBUTES => [CLASS_CONST => array(ONLY_NUMERIC_VALUE), 'disabled' => $disabled],
		];
   // }

    $form['swr_student_fee'] = [
        HASH_TYPE => 'checkbox',
        HASH_TITLE => t('Do you want to <strong style="color:red;">add student fees</strong> <strong>(Tax Applicable '.$revenueStudentTax[REVENUE_HEAD_STUDENT_FEE_NID].'%)</strong>'),
        HASH_DEFAULT_VALUE => (isset($record['want_to_add_student_fee']) && $feeId) ? $record['want_to_add_student_fee']:'',
        HASH_ATTRIBUTES => array('disabled' => $disabled),
    ];
    $form['show_student_data'] = [
      HASH_TYPE => 'table',
      HASH_ATTRIBUTES => ['id' => 'studentListData']
    ];
    
    $form['swr_total_fee_entry'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Gross Amount'),
      HASH_ATTRIBUTES => array('readonly' => 'readonly'),
      HASH_DEFAULT_VALUE => (isset($record['total_fee_entry']) && $feeId) ? $record['total_fee_entry']:'',
      HASH_PREFIX => '<div class= "revenue-total-tax">'
    ];
    $form['swr_total_pay_to_uil'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Net Amount'),
      HASH_DEFAULT_VALUE => (isset($record['total_pay_to_uil']) && $feeId) ? $record['total_pay_to_uil']:'',
      HASH_ATTRIBUTES => array('readonly' => 'readonly'),
    ];
    $form['swr_tax'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Tax (as per configuration)'),
      HASH_DEFAULT_VALUE => (isset($record['tax']) && $feeId) ? $record['tax']:'',
      HASH_ATTRIBUTES => array('readonly' => 'readonly'),
      HASH_SUFFIX => '</div>',
    ];

    $form['swr_payment_type'] = array(
      HASH_TYPE => 'radios',
      HASH_TITLE => t('Payment Mode'),
      HASH_DEFAULT_VALUE => (isset($record['payment_type']) && $feeId) ? $record['payment_type']:'',
      HASH_OPTIONS => PAYMENT_MODE_OPTIONS,
      HASH_ATTRIBUTES => ['id' => ['payment-mode-id'], CLASS_CONST => ['payment-mode']],
    );
    $form['swr_beneficiary'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Beneficiary Name'),
      HASH_DEFAULT_VALUE => (isset($record['neft_beneficiary_name']) && $feeId) ? $record['neft_beneficiary_name']:'',
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
      HASH_STATES => array(
          VISIBLE => array(
              ':input[name=swr_payment_type]' => array('value' => 0),
          )
        )
    ];
    $form['swr_beneficiary_ac_no'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Beneficiary A/C Number'),
      HASH_DEFAULT_VALUE => (isset($record['neft_beneficiary_account_no']) && $feeId) ? $record['neft_beneficiary_account_no']:'',
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
      HASH_STATES => array(
          VISIBLE => array(
              ':input[name=swr_payment_type]' => array('value' => 0),
          )
        )
    ];
    $form['swr_remitter'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Remitter Name'),
      HASH_DEFAULT_VALUE => (isset($record['neft_remitter_name']) && $feeId) ? $record['neft_remitter_name']:'',
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
      HASH_STATES => array(
          VISIBLE => array(
              ':input[name=swr_payment_type]' => array('value' => 0),
          )
        )
    ];
    $form['swr_remitter_ac_no'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Remitter A/C Number'),
      HASH_DEFAULT_VALUE => (isset($record['neft_remitter_account_no']) && $feeId) ? $record['neft_remitter_account_no']:'',
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
      HASH_STATES => array(
          VISIBLE => array(
              ':input[name=swr_payment_type]' => array('value' => 0),
          )
        )
    ];
    $form['swr_ifsc'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('IFSC Code'),
      HASH_DEFAULT_VALUE => (isset($record['neft_ifsc_code']) && $feeId) ? $record['neft_ifsc_code']:'',
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
      HASH_STATES => array(
          VISIBLE => array(
              ':input[name=swr_payment_type]' => array('value' => 0),
          )
        )
    ];
    $form['swr_transaction'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Transaction No'),
      HASH_DEFAULT_VALUE => (isset($record['neft_transaction_no']) && $feeId) ? $record['neft_transaction_no']:'',
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
      HASH_STATES => array(
          VISIBLE => array(
              ':input[name=swr_payment_type]' => array('value' => 0),
          )
        )
    ];
    $form['swr_date'] = [
      HASH_TYPE => 'datetime',
      HASH_TITLE => t('Transaction Date & Time'),
      HASH_DEFAULT_VALUE => $neftDate,
      HASH_STATES => array(
          VISIBLE => array(
              ':input[name=swr_payment_type]' => array('value' => 0),
          )
        ),
      HASH_PREFIX => '<div class= "form-item fee-date-neft">',
      HASH_SUFFIX => '</div>',
    ];
    $form['swr_cheque_no'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Cheque No'),
      HASH_DEFAULT_VALUE => (isset($record['cheque_dd_no']) && $feeId) ? $record['cheque_dd_no']:'',
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
      HASH_STATES => array(
          VISIBLE => array(
              ':input[name=swr_payment_type]' => array('value' => 1),
          )
        )
    ];
    $form['swr_cheque_amount'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Cheque/DD Amount'),
      HASH_DEFAULT_VALUE => (isset($record['cheque_amount']) && $feeId) ? $record['cheque_amount']:'',
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC], 'readonly' => 'readonly'],
      HASH_STATES => array(
          VISIBLE => array(
              ':input[name=swr_payment_type]' => array('value' => 1),
          )
        )
    ];
    $form['swr_bank_drawn'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Bank Drawn'),
      HASH_DEFAULT_VALUE => (isset($record['cheque_bank_drawn']) && $feeId) ? $record['cheque_bank_drawn']:'',
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
      HASH_STATES => array(
          VISIBLE => array(
              ':input[name=swr_payment_type]' => array('value' => 1),
          )
        )
    ];
    $form['swr_cheque_transaction'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Transaction No'),
      HASH_DEFAULT_VALUE => (isset($record['cheque_transaction_no']) && $feeId) ? $record['cheque_transaction_no']:'',
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
      HASH_STATES => array(
          VISIBLE => array(
              ':input[name=swr_payment_type]' => array('value' => 1),
          )
        )
    ];
    $form['swr_cheque_date'] = [
      HASH_TYPE => 'date',
      HASH_TITLE => t('Cheque Date'),
      HASH_DEFAULT_VALUE => $chequeDate,
      HASH_STATES => array(
          VISIBLE => array(
              ':input[name=swr_payment_type]' => array('value' => 1),
          )
        ),
      HASH_PREFIX => '<div class= "form-item fee-date-cheque">',
      HASH_SUFFIX => '</div>',
    ];
    $form['swr_cash_amount'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Cash Amount'),
      HASH_DEFAULT_VALUE => (isset($record['cash_amount']) && $feeId) ? $record['cash_amount']:'',
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
      HASH_STATES => array(
          VISIBLE => array(
              ':input[name=swr_payment_type]' => array('value' => 2),
          )
        )
    ];

    $form[ACTIONS] = array(HASH_TYPE => ACTIONS);
    $form[ACTIONS]['cancel'] = array(
      HASH_TYPE => 'button',
      HASH_VALUE => t('Cancel'),
      HASH_WEIGHT => -1,
      HASH_ATTRIBUTES => array('onClick' => 'window.location.href = "/fee-receipt-list"; event.preventDefault();')
    );
	$form[ACTIONS]['send'] = [
		HASH_TYPE => 'submit',
		HASH_VALUE => t('Generate Receipt'),
		//HASH_ATTRIBUTES => array('class' => 'generate-fee-send'),
		'#attributes' => array('class' => array('generate-fee-btn alter-button')),
		HASH_PREFIX => '<span class="wrp-btn"><span class="temp-button js-form-submit form-submit btn btn-primary">'.t('Generate Receipt').'</span>',
		HASH_SUFFIX => '</span>'
	];
/*
    $form[ACTIONS]['send'] = [
      HASH_TYPE => 'submit',
      HASH_VALUE => t('Generate Receipt')
	

    ];
	*/
    return $form;
  }



  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $field = $form_state->getValues();
    $CustomFields = \Drupal::request()->request;
    $masterDataService = \Drupal::service('sewing.master_data');
    $schoolCode=$field['swr_school_code'];
    $revenueType=$field['swr_revenue_head_type'];
    $gradeData = $masterDataService->getGradeMasterData($schoolCode);
    $paymentType = $field['swr_payment_type'];
	
	$totalPayToUIL = $CustomFields->get('swr_total_pay_to_uil');
	if(empty($totalPayToUIL)){
		$message = 'Payment to UIL cannot be Zero. Please enter some amount for selected Revenue Type';
        $form_state->setErrorByName('swr_total_pay_to_uil', $message);
	}
    if($paymentType == 0) {
      $neftDate = $form_state->getValue('swr_date')->format("Y-m-d H:i:s");
      $validateDate = !empty($neftDate)?strtotime($neftDate):'';
      $backDate = date('Y-m-d H:i:s', strtotime('-3 days'));
      if(strtotime($backDate) > $validateDate) {
        $message = 'NEFT back date to be allowed within 72 hours back date from the current Date.';
        $form_state->setErrorByName('swr_date', $message);         
      }
    }
    if($revenueType == REVENUE_HEAD_AFFILIATION_FEE_NID) {
      $max_fee_amount = $gradeData['field_affiliation_fees']; 
    } elseif($revenueType == REVENUE_HEAD_RENEWAL_FEE_NID) {
      $max_fee_amount = $gradeData['field_renewal_fees'];
    } else {
      $max_fee_amount = 0;
    }
    $schoolData = Node::load($schoolCode);
    $school_renewal_date = $schoolData->field_sewing_date_of_renewal->value;
    $school_affliation_date = $schoolData->field_sewing_affiliation_date->value;
    $school_renewal_FeeReceived = $schoolData->field_renewal_received_fees->value;
    $school_affliation_FeeReceived = $schoolData->field_affiliation_received_fees->value;

    if($revenueType == REVENUE_HEAD_AFFILIATION_FEE_NID && strtotime($school_affliation_date)>=time()){
      $message = 'Affiliation fee cannot be entered before the affiliation date '.$school_affliation_date.'.';
      $form_state->setErrorByName('swr_revenue_head_value', $message); 
    }
    /* elseif($revenueType == REVENUE_HEAD_RENEWAL_FEE_NID && strtotime($school_renewal_date)>=time()){
      $message = 'Renewal Fee already exist. Next renewal date is '.$school_renewal_date.'.';
      $form_state->setErrorByName('swr_revenue_head_value', $message); 
    } */
    elseif($revenueType == REVENUE_HEAD_RENEWAL_FEE_NID && empty($school_affliation_FeeReceived) ){
      $message = 'You have not paid Afiliation Fee for this school yet. Kindly first pay affiliation fee.';
      $form_state->setErrorByName('swr_revenue_head_value', $message); 
    }
    elseif($revenueType == REVENUE_HEAD_RENEWAL_FEE_NID && empty($school_renewal_date) ){
      $message = 'You can not enter Renewal amount because Renewal date is empty.';
      $form_state->setErrorByName('swr_revenue_head_value', $message); 
    }

    
    if($revenueType == REVENUE_HEAD_AFFILIATION_FEE_NID || $revenueType == REVENUE_HEAD_RENEWAL_FEE_NID){
      if(!empty($field['swr_revenue_head_value']) && $field['swr_revenue_head_value']>0 && ($field['swr_revenue_head_value']>$max_fee_amount))
      {      
          $message = 'Revenue Fee not more than Max fee.';
          $form_state->setErrorByName('swr_revenue_head_value', $message); 
      }
    }

    $query =\Drupal::entityQuery('node')
        ->condition('type', 'manage_sewing_students')
        ->condition(STATUS, 1)
        ->condition('field_sewing_school_code_list', $schoolCode);
    $ids = $query->execute();
    $nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
    $received_fee_amount=$CustomFields->get('received_fee');
    $i = 0;
    if(!empty($nodes)) {
          foreach($nodes as $node) {
           /*  $courseCode = $node->get('field_sewing_course_code_list')->target_id;
            $courseMaster = \Drupal\node\Entity\Node::load($courseCode);
            $courseFee  = !empty($courseMaster->get('field_course_fee')->getValue()[0]['value'])? $courseMaster->get('field_course_fee')->getValue()[0]['value']: 0;
            $recivedFee = $node->get('field_sewing_course_fee_received')->getValue()[0]['value'];
            $balanceFee = $courseFee - $recivedFee;
            $StudentID=$node->id();
            $studentreceived_fee=$received_fee_amount[$StudentID];
            if(!empty($studentreceived_fee) && ($studentreceived_fee>0) && ($studentreceived_fee>$balanceFee))
            {      
              $message = 'Received fee should not be more than Balance Fee.';
              $form_state->setErrorByName('received_fee', $message); 
            }
            $i++; */
          }          
      } 

  }

  /**
   * Method to add and update user's information using custom form
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    $user = User::load($current_user->id());
    $feeId = $_GET['id'];
    $nameWithRole = $user->get('field_first_name')->value.' '. $user->get('field_last_name')->value.' ('.$roles[1].')' ;
    $locationId = $user->field_user_location->target_id;

    $field = $form_state->getValues();
    $CustomFields = \Drupal::request()->request;
    $userId = \Drupal::currentUser()->id();
    $schoolCode = $field['swr_school_code'];
    $node = \Drupal\node\Entity\Node::load($schoolCode);
    
    $masterDataService = \Drupal::service('sewing.master_data');
    $receipt_number = $masterDataService->generate_receipt_form_code($schoolCode);
    $database = Database::getConnection(); 
    $neft_textfields = array('neft_beneficiary_name','neft_beneficiary_account_no','neft_remitter_name','neft_remitter_account_no','neft_ifsc_code','neft_transaction_no','neft_transaction_date');
    $cheque_textfields = array('cheque_dd_no','cheque_amount','cheque_bank_drawn','cheque_transaction_no','cheque_transaction_date');

    $data = [
      'sender_role' => $roles[1],
      'receiver_id' => '',
      'receiver_role' => '',
      'message' => $message,
      'location' => $locationId,
      'created_by' => $current_user->id()
    ];
  if(empty($feeId)) {  
    foreach (FEE_GENERATION_ARRAY as $key => $value) {
      if( $key != 'payment_type' && !in_array($key,$neft_textfields) && !in_array($key,$cheque_textfields)) {
        $dataArr[$key] = $field[$value];
      }
      elseif($key == 'payment_type'){
        $dataArr['payment_type']=$field['swr_payment_type'];
      }
      elseif($field['swr_payment_type']==1){
          if(in_array($key,$cheque_textfields) && ( $key != 'cheque_transaction_date') ) {
           $dataArr[$key] = $field[$value];
          }
          elseif (in_array($key,$cheque_textfields) && $key == 'cheque_transaction_date'){
            $cheque_date=$CustomFields->get('swr_cheque_date');             
            $dataArr['cheque_transaction_date'] = !empty($cheque_date)?strtotime( $cheque_date):NULL;
            $dataArr['cheque_transaction_time'] = 0;
          }
      }
      elseif($field['swr_payment_type']==0){
          if(in_array($key,$neft_textfields) && $key != 'neft_transaction_date' ) {
           $dataArr[$key] = $field[$value];
          }
          elseif (in_array($key,$neft_textfields) && $key == 'neft_transaction_date'){
            $neft_date=$CustomFields->get('swr_date');            
            $dataArr['neft_transaction_date'] = !empty($neft_date['date'])?strtotime($neft_date['date']):NULL;
            $dataArr['neft_transaction_time'] = !empty($neft_date['time'])?strtotime($neft_date['time']):NULL;
          }
      }
    }

    $dataArr['created_by'] = $userId;
    $dataArr['created_date'] = time();
    $dataArr['receipt_number'] = $receipt_number;
    $query_id = $database->insert('usha_generate_fee_receipt')->fields($dataArr)->execute();

    if(!empty($field['swr_revenue_head_type']) && !empty($field['swr_school_code'])) {
      $swr_school_code = $field['swr_school_code'];  
      $swr_date = date('Y-m-d');
      $swrdate= strtotime('+1 year',strtotime($swr_date));
      $swrrenewaldate = date('Y-m-d',$swrdate);

      if(!empty($field['swr_revenue_head_value']) && $field['swr_revenue_head_value']>0 && $field['swr_revenue_head_type']==REVENUE_HEAD_AFFILIATION_FEE_NID){            
        $node = Node::load($swr_school_code);
        $node->set('field_affiliation_received_fees', $field['swr_revenue_head_value']); 
        $node->set('field_affiliation_fee_receive_on', $swr_date); 
        $node->save();
      }
      elseif(!empty($field['swr_revenue_head_value']) && $field['swr_revenue_head_value']>0 && $field['swr_revenue_head_type']==REVENUE_HEAD_RENEWAL_FEE_NID){      
        $node = Node::load($swr_school_code);
        $swr_renewal_date=$node->field_sewing_date_of_renewal->value;
        $swr_renewal_date= strtotime('+1 year',strtotime($swr_renewal_date));
        $swr_renewal_date = date('Y-m-d',$swr_renewal_date);
        $node->set('field_renewal_received_fees', $field['swr_revenue_head_value']); 
        $node->set('field_renewal_fee_received_on', $swr_date);
        $node->set('field_sewing_date_of_renewal', $swr_renewal_date); 
        $node->save();
      }
    }

    $fee_details=$CustomFields->get('received_fee');
    $pay_uil_details=$CustomFields->get('payment_to_uil');
    $totalStudentFee = 0;
    if(isset($query_id) && $query_id >0){
      foreach($fee_details as $key => $value ){
        $student_pay_uil=0;
        $studentId= $key;
        $studentreceivedfee= $value;
        $student_pay_uil= $fee_details[$studentId];
        $totalStudentFee +=$studentreceivedfee;  
        $fieldArr = array(
          'generate_fee_id' => $query_id,
          'received_fee' => $studentreceivedfee,
          'payment_to_uil' => $student_pay_uil,
          'student_id' => $studentId,
          'created_date' => time(),
        );
        if(!empty($studentreceivedfee) && $studentreceivedfee>=0 ){
          $query = $database->insert('usha_student_fee_receipt')->fields($fieldArr)->execute();
          $node = Node::load($studentId);
          $old_receivedfee = $node->field_sewing_course_fee_received->value?$node->field_sewing_course_fee_received->value:0;
          
          $studentData = \Drupal\node\Entity\Node::load($studentId);
          //$courseCode = $studentData->get('field_sewing_course_code_list')->target_id;
          //$courseMaster = \Drupal\node\Entity\Node::load($courseCode);
          $courseFee  = $studentData->field_sewing_course_fee_due->value;

          $totalReceivedFee = $old_receivedfee+$studentreceivedfee;
          $totalOutStanding =  $courseFee - $totalReceivedFee;
          
          $node->set('field_sewing_course_fee_received', $totalReceivedFee);
          $node->set('field_sewing_course_fee_out', $totalOutStanding);
          $node->save(); 
        }
      }
      $database->update('usha_generate_fee_receipt')->fields(array('total_student_fee' => $totalStudentFee))->condition('id', $query_id)->execute();
      $schoolNode = \Drupal\node\Entity\Node::load($schoolCode); 
    }
      $data['message'] = preg_replace('/{.*}/',$schoolNode->field_sewing_school_code->value, FEE_SUBMISSION_ADD_MESSAGE);
      $returnID = $query_id;
    } else {
        foreach (FEE_GENERATION_ARRAY as $key => $value) {
          if($key == 'payment_type'){
            $dataArr['payment_type']=$field['swr_payment_type'];
          } elseif($field['swr_payment_type']==1){
              if(in_array($key,$cheque_textfields) && ( $key != 'cheque_transaction_date') ) {
               $dataArr[$key] = $field[$value];
              }
              elseif (in_array($key,$cheque_textfields) && $key == 'cheque_transaction_date'){
                $cheque_date=$CustomFields->get('swr_cheque_date');            
                $dataArr['cheque_transaction_date'] = !empty($cheque_date)?strtotime($cheque_date):NULL;
                $dataArr['cheque_transaction_time'] = 0;
              }
              $dataArr['neft_beneficiary_name'] = NULL;
              $dataArr['neft_beneficiary_account_no'] = NULL;
              $dataArr['neft_remitter_name'] = NULL;
              $dataArr['neft_remitter_account_no'] = NULL;
              $dataArr['neft_ifsc_code'] = NULL;
              $dataArr['neft_transaction_no'] = NULL;
              $dataArr['neft_transaction_date'] = NULL;
              $dataArr['neft_transaction_time'] = NULL;
          } elseif($field['swr_payment_type']==0){
              if(in_array($key,$neft_textfields) && $key != 'neft_transaction_date' ) {
               $dataArr[$key] = $field[$value];
              }
              elseif (in_array($key,$neft_textfields) && $key == 'neft_transaction_date'){
                $neft_date=$CustomFields->get('swr_date');            
                $dataArr['neft_transaction_date'] = !empty($neft_date['date'])?strtotime($neft_date['date']):NULL;
                $dataArr['neft_transaction_time'] = !empty($neft_date['time'])?strtotime($neft_date['time']):NULL;
              }
              $dataArr['cheque_dd_no'] = NULL;
              $dataArr['cheque_amount'] = NULL;
              $dataArr['cheque_bank_drawn'] = NULL;
              $dataArr['cheque_transaction_no'] = NULL;
              $dataArr['cheque_transaction_date'] = NULL;
              $dataArr['cheque_transaction_time'] = 0;
          }
        }
        $dataArr['updated_by'] = $userId;
        $dataArr['updated_date'] = time();
        $query    = $database->update('usha_generate_fee_receipt')->fields($dataArr)->condition('id', $feeId)->execute();
        $data['message'] = preg_replace('/{.*}/',$schoolNode->field_sewing_school_code->value, FEE_SUBMISSION_UPDATE_MESSAGE);
        $returnID = $feeId;
    }
    $masterDataService = \Drupal::service('sewing.master_data');
    if(in_array($roles[1], [ROLE_SEWING_SSI])) {
        $masterSilaiDataService = \Drupal::service('silai.master_data');
        $hoAdminUsers = $masterSilaiDataService->getUsersByRole(ROLE_SEWING_HO_ADMIN);
        $hoUsers = $masterSilaiDataService->getUsersByRole(ROLE_SEWING_HO_USER);
        $targetUsers = array_merge($hoUsers,$hoAdminUsers);
        if(!empty($targetUsers)){
          $masterDataService->sewingNotificationAlert($data, $targetUsers);
        }
    }
    $url = "print-fee-receipt?id=".$returnID;
    $response = new TrustedRedirectResponse($url);
    $form_state->setResponse($response);
  }
}