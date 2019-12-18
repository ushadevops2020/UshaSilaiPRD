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


class GenerateFeeReceiptFormDemo extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'generate_fee_receipt_form_demo';
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
    $stateByLocation[''] = '-Select-';
    asort($stateByLocation);
    $townbylocation[''] = '-Select-';
    asort($townbylocation);    
    $schoolbylocation[''] = '-Select-';
    asort($schoolbylocation);

    $revenueHead = $masterDataService->getRevenueHead();
    
    $form['swr_state'] = [
        HASH_TYPE => SELECTFIELD,
        HASH_TITLE => t('State'),
        HASH_OPTIONS => $stateByLocation,
        HASH_DEFAULT_VALUE => $stateByLocation ? $stateByLocation : ''
    ];

    $form['swr_town'] = [
        HASH_TYPE => SELECTFIELD,
        HASH_TITLE => t('Town'),
        HASH_OPTIONS => $townbylocation,
        HASH_DEFAULT_VALUE => $townbylocation ? $townbylocation : ''
    ];

    $form['swr_school_code'] = [
        HASH_TYPE => SELECTFIELD,
        HASH_TITLE => t('School Code'),
        HASH_OPTIONS => $schoolbylocation,
        HASH_DEFAULT_VALUE => $schoolbylocation ? $schoolbylocation : ''
    ];

    $form['swr_school_type'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('School Type'),
      HASH_MAXLENGTH => 30,
      HASH_ATTRIBUTES => array('readonly' => 'readonly'),
    ];
    $form['swr_school_grade'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Grade'),
      HASH_MAXLENGTH => 30,
      HASH_ATTRIBUTES => array('readonly' => 'readonly'),
    ];
    $form['swr_sap_code'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('SAP Code'),
      HASH_MAXLENGTH => 30,
      HASH_ATTRIBUTES => array('readonly' => 'readonly'),
    ];
    $form['swr_school_admin'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('School Admin'),
      HASH_MAXLENGTH => 30,
      HASH_ATTRIBUTES => array('readonly' => 'readonly'),
    ];
    $form['swr_no_student'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('NO of Student'),
      HASH_MAXLENGTH => 30,
      HASH_ATTRIBUTES => array('readonly' => 'readonly'),
    ];
    $form['swr_course'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Course Offered'),
      HASH_MAXLENGTH => 30,
      HASH_ATTRIBUTES => array('readonly' => 'readonly'),
    ];
    
    $form['swr_revenue_head_type'] = [
      HASH_TYPE => 'radios',
      HASH_TITLE => t('Revenue Type'),
      HASH_OPTIONS => $revenueHead,
      HASH_ATTRIBUTES => [CLASS_CONST => array('revenue-head-class')],
    ];
    $form['field_hidden_revenue_tax'] = [
        HASH_TYPE => FIELD_HIDDEN,
        HASH_ATTRIBUTES => array('id' => 'revenue-tax'),
    ];
    $form['field_hidden_revenue_student_tax'] = [
        HASH_TYPE => FIELD_HIDDEN,
        HASH_ATTRIBUTES => array('id' => 'revenue-student-tax'),
    ];
    $form['field_hidden_revenue_value'] = [
        HASH_TYPE => FIELD_HIDDEN,
        HASH_ATTRIBUTES => array('id' => 'max-fee-amount'),
    ];

    $form['swr_revenue_head_value'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Fee Amount'),
      HASH_MAXLENGTH => 30,
      HASH_STATES => array(
          VISIBLE => array(
              ':input[name=swr_revenue_head_type]' => array('checked' =>TRUE),
          ),
          REQUIRED => array(
              ':input[name=swr_revenue_head_type]' => array('checked' =>TRUE),
          ),
        ),
      HASH_ATTRIBUTES => [CLASS_CONST => array(ONLY_NUMERIC_VALUE)],
    ];
    

    $form['swr_student_fee'] = [
        HASH_TYPE => 'checkbox',
        HASH_TITLE => t('Do you want to add student fees')
    ];
    $form['show_student_data'] = [
      HASH_TYPE => 'table',
      // '#header' => $header,
      '#attributes' => ['id' => 'studentListData']
    ];
    
    $form['swr_total_fee_entry'] = [
        HASH_TYPE => TEXTFIELD,
        HASH_TITLE => t('Total Fee Entry'),
        HASH_ATTRIBUTES => array('readonly' => 'readonly'),
    ];
    $form['swr_total_pay_to_uil'] = [
        HASH_TYPE => TEXTFIELD,
        HASH_TITLE => t('Total Payment To UIL'),
        HASH_ATTRIBUTES => array('readonly' => 'readonly'),
    ];
    $form['swr_tax'] = [
        HASH_TYPE => TEXTFIELD,
        HASH_TITLE => t('Tax (as per configuration)'),
        HASH_ATTRIBUTES => array('readonly' => 'readonly'),
    ];

    $form['swr_payment_type']['active'] = array(
      HASH_TYPE => 'radios',
      HASH_TITLE => t('Payment Mode'),
      HASH_DEFAULT_VALUE => 0,
      HASH_OPTIONS => array(
                      0 => t('Pay via NEFT'),
                      1 => t('Pay via cheque/DD'),
                    ),
    );
    $form['swr_beneficiary'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Beneficiary Name'),
      HASH_DEFAULT_VALUE => ($beneficiaryName) ? $beneficiaryName : '',
      HASH_MAXLENGTH => 30,
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
      HASH_STATES => array(
          VISIBLE => array(
              ':input[name=swr_payment_type]' => array('value' =>0),
          )
        )
    ];
    $form['swr_beneficiary_ac_no'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('A/C No'),
      HASH_DEFAULT_VALUE => ($beneficiaryAcNo) ? $beneficiaryAcNo : '',
      HASH_MAXLENGTH => 30,
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
      HASH_STATES => array(
          VISIBLE => array(
              ':input[name=swr_payment_type]' => array('value' =>0),
          )
        )
    ];
    $form['swr_remitter'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Remitter Name'),
      HASH_DEFAULT_VALUE => ($remitterName) ? $remitterName : '',
      HASH_MAXLENGTH => 30,
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
      HASH_STATES => array(
          VISIBLE => array(
              ':input[name=swr_payment_type]' => array('value' =>0),
          )
        )
    ];
    $form['swr_remitter_ac_no'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('A/C No'),
      HASH_DEFAULT_VALUE => ($remitterAcNo) ? $remitterAcNo : '',
      HASH_MAXLENGTH => 30,
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
      HASH_STATES => array(
          VISIBLE => array(
              ':input[name=swr_payment_type]' => array('value' =>0),
          )
        )
    ];
    $form['swr_ifsc'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('IFSC Code'),
      HASH_DEFAULT_VALUE => ($ifscCode) ? $ifscCode : '',
      HASH_MAXLENGTH => 30,
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
      HASH_STATES => array(
          VISIBLE => array(
              ':input[name=swr_payment_type]' => array('value' =>0),
          )
        )
    ];
    $form['swr_transaction'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Transaction No'),
      HASH_DEFAULT_VALUE => ($transactionNo) ? $transactionNo : '',
      HASH_MAXLENGTH => 30,
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
      HASH_STATES => array(
          VISIBLE => array(
              ':input[name=swr_payment_type]' => array('value' =>0),
          )
        )
    ];
    $form['swr_date'] = [
      HASH_TYPE => 'datetime',
      HASH_TITLE => t('Transaction Date & Time'),
      HASH_DEFAULT_VALUE => ($transactionDate) ? $transactionDate : '',
      HASH_MAXLENGTH => 30,
      HASH_STATES => array(
          VISIBLE => array(
              ':input[name=swr_payment_type]' => array('value' =>0),
          )
        ),
      '#prefix' => '<div>',
      '#suffix' => '</div>'
    ];
    $form['swr_cheque_no'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Cheque/DD No'),
      HASH_DEFAULT_VALUE => ($remitterAcNo) ? $remitterAcNo : '',
      HASH_MAXLENGTH => 30,
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
      HASH_STATES => array(
          VISIBLE => array(
              ':input[name=swr_payment_type]' => array('value' =>1),
          )
        )
    ];
    $form['swr_bank_drawn'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Bank Drawn'),
      HASH_DEFAULT_VALUE => ($ifscCode) ? $ifscCode : '',
      HASH_MAXLENGTH => 30,
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
      HASH_STATES => array(
          VISIBLE => array(
              ':input[name=swr_payment_type]' => array('value' =>1),
          )
        )
    ];
    $form['swr_cheque_transaction'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Transaction No'),
      HASH_DEFAULT_VALUE => ($transactionNo) ? $transactionNo : '',
      HASH_MAXLENGTH => 30,
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
      HASH_STATES => array(
          VISIBLE => array(
              ':input[name=swr_payment_type]' => array('value' =>1),
          )
        )
    ];
    $form['swr_cheque_date'] = [
      HASH_TYPE => 'datetime',
      HASH_TITLE => t('Transaction Date & Time'),
      HASH_DEFAULT_VALUE => ($transactionDate) ? $transactionDate : '',
      HASH_MAXLENGTH => 30,
      HASH_STATES => array(
          VISIBLE => array(
              ':input[name=swr_payment_type]' => array('value' =>1),
          )
        )
    ];

    $form[ACTIONS] = array(HASH_TYPE => ACTIONS);
    $form[ACTIONS]['cancel'] = array(
      HASH_TYPE => 'button',
      HASH_VALUE => t('Cancel'),
      '#weight' => -1,
      '#attributes' => array('onClick' => 'window.location.href = "/"; event.preventDefault();'),
    );
    $form[ACTIONS]['send'] = [
      HASH_TYPE => 'submit',
      HASH_VALUE => t('Generate Receipt')
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
    $field = $form_state->getValues();
    $CustomFields = \Drupal::request()->request;
    $userId = \Drupal::currentUser()->id();
    $database = Database::getConnection(); 
    foreach (FEE_GENERATION_ARRAY as $key => $value) {
      if( $key != 'neft_transaction_date' && $key != 'cheque_transaction_date') {
        $dataArr[$key] = $field[$value];
      }
      elseif ($key == 'cheque_transaction_date' && $field['swr_payment_type']==1){
            $cheque_date=$CustomFields->get('swr_cheque_date');            
            $dataArr['cheque_transaction_date'] = !empty($cheque_date['date'])?strtotime($cheque_date['date']):NULL;
            $dataArr['cheque_transaction_time'] = !empty($cheque_date['time'])?strtotime($cheque_date['time']):NULL;
      }
      elseif($key == 'neft_transaction_date' && $field['swr_payment_type']==0){
            $neft_date=$CustomFields->get('swr_date');            
            $dataArr['neft_transaction_date'] = !empty($neft_date['date'])?strtotime($neft_date['date']):NULL;
            $dataArr['neft_transaction_time'] = !empty($neft_date['time'])?strtotime($neft_date['time']):NULL;
      }
    }

    $dataArr['created_by'] = $userId;
    $dataArr['created_date'] = time();
    $query_id = $database->insert('usha_generate_fee_receipt')->fields($dataArr)->execute();
    $fee_details=$CustomFields->get('received_fee');
    if(isset($query_id) && $query_id >0){
      foreach($fee_details as $key => $value ){
        $studentId= $key;
        $studentreceivedfee= $value;
        $fieldArr = array(
          'generate_fee_id' => $query_id,
          'received_fee' => $studentreceivedfee,
          'student_id' => $studentId,
          'created_date' => time(),
        );
      $query = $database->insert('usha_student_fee_receipt')->fields($fieldArr)->execute();
      }
    }

    return;
  }

  public function studentListCallback(array $form, FormStateInterface $form_state) {
      $response = new AjaxResponse();
      $debugOut = @Kint::dump($form_state);
      $query =\Drupal::entityQuery('node')
        ->condition('type', 'manage_sewing_students')
        ->condition(STATUS, 1)
        ->condition('field_sewing_school_code_list', 128);
      $ids = $query->execute();
      $nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
        foreach($nodes as $node) {
          $form['swr_student_table'][$node->id()]['srno'] = [
              HASH_PLAIN_TEXT => $node->id(),
          ];
          $form['swr_student_table'][$node->id()]['admissionNumber'] = [
              HASH_PLAIN_TEXT => $node->get('field_student_admission_no')->getValue()[0]['value'],
          ];
          $form['swr_student_table'][$node->id()]['name'] = [
              HASH_PLAIN_TEXT => $node->getTitle(),
          ];
          $form['swr_student_table'][$node->id()]['totalFee'] = [
              HASH_PLAIN_TEXT => $node->get('field_student_admission_no')->getValue()[0]['value'],
          ];
          $form['swr_student_table'][$node->id()]['balanceFee'] = [
              HASH_PLAIN_TEXT => $node->get('field_sewing_course_fee_due')->getValue()[0]['value'],
          ];
          $form['swr_student_table'][$node->id()]['ReceivedFee'] = array(
            HASH_TYPE => TEXTFIELD,
            HASH_DEFAULT_VALUE => $node->get('field_sewing_course_fee_received')->getValue()[0]['value'],
            HASH_ATTRIBUTES => [CLASS_CONST => array(ONLY_NUMERIC_VALUE)],
          );
          $form['swr_student_table'][$node->id()]['PaymentUIL'] = [
              HASH_PLAIN_TEXT => $node->get('field_student_admission_no')->getValue()[0]['value'],
          ];
          $form['swr_student_table'][$node->id()]['taxApllicabale'] = [
              HASH_PLAIN_TEXT => $node->get('field_student_admission_no')->getValue()[0]['value'],
          ];
        }
      $response->addCommand(new ReplaceCommand('#debug-out', $debugOut ));
    return $response;
  // return $form;
  }
}