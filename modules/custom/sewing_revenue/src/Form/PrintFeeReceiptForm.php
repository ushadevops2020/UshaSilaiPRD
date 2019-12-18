<?php
/**
 * @file
 * Contains Drupal\sewing_revenue\Form.
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



class PrintFeeReceiptForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'print_fee_receipt_form';
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
    // $townbystate = $masterDataService->getTownByStateId($stateId);
    $schoolbylocation = $masterDataService->getSchoolBylocationId($userLocation);
    $revenueStudentTax= $masterDataService->getRevenueHead(REVENUE_HEAD_STUDENT_FEE_NID);
    $stateByLocation[''] = '-Select-';
    asort($stateByLocation);
    $townbylocation[''] = '-Select-';
    asort($townbylocation);    
    $schoolbylocation[''] = '-Select-';
    asort($schoolbylocation);
    $revenueHead = $masterDataService->getRevenueHead();
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
        }
        if(!empty($record['cheque_transaction_date']) && !empty($record['cheque_transaction_time'])) {
          $chequeDate = date("m/d/Y",$record['cheque_transaction_date']). ' ' .date("h:i:s A",$record['cheque_transaction_time']);
        }
    }
    $form['field_hidden_fee_id'] = [
        HASH_TYPE => FIELD_HIDDEN,
        HASH_DEFAULT_VALUE => $feeId,
        HASH_ATTRIBUTES => array('id' => 'hidden-fee-id'),
    ];
    $form[ACTIONS]['print'] = [
      HASH_TYPE => 'submit',
      HASH_VALUE => t('Print Receipt'),
      HASH_ATTRIBUTES => array('onClick' => 'window.print();'),
      HASH_PREFIX => '<div class= "form-item print-fee-button">',
      HASH_SUFFIX => '</div>',
    ];

    $form['swr_print_receipt_no'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Receipt Number'),
      HASH_DEFAULT_VALUE => (isset($record['receipt_number']) && $feeId) ? $record['receipt_number']:'',
      HASH_ATTRIBUTES => array('disabled' => 'disabled'),
    ];

    $form['swr_print_state'] = [
      HASH_TYPE => SELECTFIELD,
      HASH_TITLE => t('State'),
      HASH_OPTIONS => $stateByLocation,
      HASH_DEFAULT_VALUE => (isset($record['state_id']) && $feeId) ? $record['state_id']:'',
      HASH_ATTRIBUTES => array('disabled' => 'disabled'),
    ];

    $form['swr_print_town'] = [
      HASH_TYPE => SELECTFIELD,
      HASH_TITLE => t('Town'),
      HASH_OPTIONS => $townbylocation,
      HASH_DEFAULT_VALUE => (isset($record['town_id']) && $feeId) ? $record['town_id']:'',
      HASH_ATTRIBUTES => array('disabled' => 'disabled'),
    ];

    $form['swr_school_code'] = [
      HASH_TYPE => SELECTFIELD,
      HASH_TITLE => t('School Code'),
      HASH_OPTIONS => $schoolbylocation,
      HASH_DEFAULT_VALUE => (isset($record['school_id']) && $feeId) ? $record['school_id']:'',
      HASH_ATTRIBUTES => array('disabled' => 'disabled'),
    ];

    $form['swr_print_school_type'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('School Type'),
      HASH_DEFAULT_VALUE => (isset($schoolDetails['schoolType']) && $feeId) ? $schoolDetails['schoolType']:'',
      HASH_ATTRIBUTES => array('disabled' => 'disabled'),
      HASH_PREFIX => '<div class= "school-details">'
    ];
    $form['swr_print_school_grade'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Grade'),
      HASH_DEFAULT_VALUE => (isset($schoolDetails['grade']) && $feeId) ? $schoolDetails['grade']:'',
      HASH_ATTRIBUTES => array('disabled' => 'disabled'),
    ];
    $form['swr_print_sap_code'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('SAP Code'),
      HASH_DEFAULT_VALUE => (isset($schoolDetails['sapCode']) && $feeId) ? $schoolDetails['sapCode']:'',
      HASH_ATTRIBUTES => array('disabled' => 'disabled'),
    ];
    $form['swr_print_school_admin'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('School Admin'),
      HASH_DEFAULT_VALUE => (isset($schoolDetails['schoolAdmin']) && $feeId) ? $schoolDetails['schoolAdmin']:'',
      HASH_ATTRIBUTES => array('disabled' => 'disabled'),
    ];
    $form['swr_print_no_student'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('NO of Student'),
      HASH_DEFAULT_VALUE => (isset($schoolDetails['noOfStudents']) && $feeId) ? $schoolDetails['noOfStudents']:'',
      HASH_ATTRIBUTES => array('disabled' => 'disabled'),
    ];
    $form['swr_print_course'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Course Offered'),
      HASH_DEFAULT_VALUE => (isset($schoolDetails['noOfCourses']) && $feeId) ? $schoolDetails['noOfCourses']:'',
      HASH_ATTRIBUTES => array('disabled' => 'disabled'),
      HASH_SUFFIX => '</div>'
    ];
    
    $form['swr_print_revenue_head_type'] = [
      HASH_TYPE => 'radios',
      HASH_TITLE => t('Revenue Type'),
      HASH_OPTIONS => $revenueHead,
      HASH_DEFAULT_VALUE => (isset($record['revenue_head_type']) && $feeId) ? $record['revenue_head_type']:'',
      HASH_ATTRIBUTES => array(CLASS_CONST => array('revenue-head-class'),'disabled' => 'disabled'),
    ];
   
    $form['swr_print_revenue_head_value'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Fee Amount <span id=max-fee-amount-display></span>'),
      HASH_DEFAULT_VALUE => (isset($record['revenue_head_value']) && $feeId) ? $record['revenue_head_value']:'',
      HASH_ATTRIBUTES => array('disabled' => 'disabled'),
    ];
    
    $form['swr_student_fee'] = [
        HASH_TYPE => 'checkbox',
        HASH_TITLE => t('Do you want to add student fees <strong>(Tax Applicable '.$revenueStudentTax[REVENUE_HEAD_STUDENT_FEE_NID].'%)</strong>'),
        HASH_DEFAULT_VALUE => (isset($record['want_to_add_student_fee']) && $feeId) ? $record['want_to_add_student_fee']:'',
        HASH_ATTRIBUTES => array('disabled' => 'disabled'),
    ];
    $form['show_student_data'] = [
      HASH_TYPE => 'table',
      HASH_ATTRIBUTES => ['id' => 'studentListData']
    ];
    
    $form['swr_print_total_fee_entry'] = [
        HASH_TYPE => TEXTFIELD,
        HASH_TITLE => t('Total Fee Entry'),
        HASH_DEFAULT_VALUE => (isset($record['total_fee_entry']) && $feeId) ? $record['total_fee_entry']:'',
        HASH_ATTRIBUTES => array('disabled' => 'disabled'),
        HASH_PREFIX => '<div class= "revenue-total-tax">'
    ];
    $form['swr_print_total_pay_to_uil'] = [
        HASH_TYPE => TEXTFIELD,
        HASH_TITLE => t('Total Payment To UIL'),
        HASH_DEFAULT_VALUE => (isset($record['total_pay_to_uil']) && $feeId) ? $record['total_pay_to_uil']:'',
        HASH_ATTRIBUTES => array('disabled' => 'disabled'),
    ];
    $form['swr_print_tax'] = [
        HASH_TYPE => TEXTFIELD,
        HASH_TITLE => t('Tax (as per configuration)'),
        HASH_DEFAULT_VALUE => (isset($record['tax']) && $feeId) ? $record['tax']:'',
        HASH_ATTRIBUTES => array('disabled' => 'disabled'),
        HASH_SUFFIX => '</div>',
    ];

    $form['swr_payment_type'] = array(
      HASH_TYPE => 'radios',
      HASH_TITLE => t('Payment Mode'),
      HASH_OPTIONS => PAYMENT_MODE_OPTIONS,
      HASH_DEFAULT_VALUE => (isset($record['payment_type']) && $feeId) ? $record['payment_type']:'',
      HASH_ATTRIBUTES => ['id' => ['payment-mode-id'], 'disabled' => 'disabled', CLASS_CONST => ['payment-mode']]
    );
    $form['swr_beneficiary'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Beneficiary Name'),
      HASH_DEFAULT_VALUE => (isset($record['neft_beneficiary_name']) && $feeId) ? $record['neft_beneficiary_name']:'',
      HASH_ATTRIBUTES => array('disabled' => 'disabled'),
    ];
    $form['swr_beneficiary_ac_no'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('A/C No'),
      HASH_DEFAULT_VALUE => (isset($record['neft_beneficiary_account_no']) && $feeId) ? $record['neft_beneficiary_account_no']:'',
      HASH_ATTRIBUTES => array('disabled' => 'disabled'),
    ];
    $form['swr_remitter'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Remitter Name'),
      HASH_DEFAULT_VALUE => (isset($record['neft_remitter_name']) && $feeId) ? $record['neft_remitter_name']:'',
      HASH_ATTRIBUTES => array('disabled' => 'disabled'),
    ];
    $form['swr_remitter_ac_no'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('A/C No'),
      HASH_DEFAULT_VALUE => (isset($record['neft_remitter_account_no']) && $feeId) ? $record['neft_remitter_account_no']:'',
      HASH_ATTRIBUTES => array('disabled' => 'disabled'),
    ];
    $form['swr_ifsc'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('IFSC Code'),
      HASH_DEFAULT_VALUE => (isset($record['neft_ifsc_code']) && $feeId) ? $record['neft_ifsc_code']:'',
      HASH_ATTRIBUTES => array('disabled' => 'disabled'),
    ];
    $form['swr_transaction'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Transaction No'),
      HASH_DEFAULT_VALUE => (isset($record['neft_transaction_no']) && $feeId) ? $record['neft_transaction_no']:'',
      HASH_ATTRIBUTES => array('disabled' => 'disabled'),
    ];
    $form['swr_date'] = [
      HASH_TYPE => 'datetime',
      HASH_TITLE => t('Transaction Date & Time'),
      HASH_DEFAULT_VALUE => new DrupalDateTime($neftDate),
      HASH_ATTRIBUTES => array('disabled' => 'disabled'),
      HASH_PREFIX => '<div class= "form-item fee-date-neft">',
      HASH_SUFFIX => '</div>',
    ];
    $form['swr_cheque_no'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Cheque/DD No'),
      HASH_DEFAULT_VALUE => (isset($record['cheque_dd_no']) && $feeId) ? $record['cheque_dd_no']:'',
      HASH_ATTRIBUTES => array('disabled' => 'disabled'),
    ];
    $form['swr_bank_drawn'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Bank Drawn'),
      HASH_DEFAULT_VALUE => (isset($record['cheque_bank_drawn']) && $feeId) ? $record['cheque_bank_drawn']:'',
      HASH_ATTRIBUTES => array('disabled' => 'disabled'),
    ];
    $form['swr_cheque_transaction'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Transaction No'),
      HASH_DEFAULT_VALUE => (isset($record['cheque_transaction_no']) && $feeId) ? $record['cheque_transaction_no']:'',
      HASH_ATTRIBUTES => array('disabled' => 'disabled'),
    ];
    $form['swr_cheque_date'] = [
      HASH_TYPE => 'datetime',
      HASH_TITLE => t('Transaction Date & Time'),
      HASH_DEFAULT_VALUE => new DrupalDateTime($chequeDate),
      HASH_ATTRIBUTES => array('disabled' => 'disabled'),
      HASH_PREFIX => '<div class= "form-item fee-date-cheque">',
      HASH_SUFFIX => '</div>',
    ];

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
    
  }  

}