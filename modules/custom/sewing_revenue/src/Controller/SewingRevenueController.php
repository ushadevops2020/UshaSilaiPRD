<?php
namespace Drupal\sewing_revenue\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Access\AccessResult; 
use Drupal\Core\Form\FormInterface;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;

/**
 * Class MISController.
 */
class SewingRevenueController extends ControllerBase {

/**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   *   Print Fee Receipt
   */
  public function printFeeReceipt() {
    $masterDataService = \Drupal::service('sewing.master_data');
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    $feeId = $_GET['id'];
    if (isset($feeId) && $feeId != '') {
        $conn = Database::getConnection();
        $query = $conn->select('usha_generate_fee_receipt', 'f')
            ->condition('id', $feeId)
            ->fields('f');
        $record = $query->execute()->fetchAssoc();
    $schoolData = Node::load($record['school_id']);
	//print_r($schoolData->field_sewing_school_type->target_id);
	//die;
	  $schoolCode = $schoolData->field_sewing_school_code->value;
	  $sapCode    = $schoolData->field_sewing_sap_code->value;
	  $address1 = $schoolData->field_address_1->value;
	  $address2 = $schoolData->field_address_2->value;
	  $address3 = $schoolData->field_address_3->value;
	  $netAmount = !empty($record['total_pay_to_uil'])?$record['total_pay_to_uil']:0;
	  $netAmountWords = $masterDataService->numberTowords($netAmount);
	  $studentFeeStatus = $record['want_to_add_student_fee'];
	  $schoolName = $schoolData->getTitle();
	  if(!empty($record['revenue_head_type'])){
		$node = \Drupal\node\Entity\Node::load($record['revenue_head_type']);
		$revenueType = $node->getTitle();
		$revenueTypeName = $node->getTitle();
		if($studentFeeStatus == 1) {
		$revenueType = $node->getTitle().' & '.'Student Fee';
		} else {
		$revenueType = $node->getTitle();
		}
	  } else {
		$revenueType = 'Student Fee';
	  }  

	  if($record['payment_type'] == 1) {
		$type = 'Cheque/DD';
		$details = $record['cheque_dd_no'];
		$bank = $record['cheque_bank_drawn'];
		$date = !empty($record['cheque_transaction_date'])?date('d/m/Y',$record['cheque_transaction_date']):'--/--/---';
	  }else if($record['payment_type'] == 2){
		$type = 'Cash';
		$details = '________';
		$date = date('d/m/Y',$record['created_date']);
		$bank = 'Cash';
	  } else {
		$type = 'NEFT/RTGS';
		$details = $record['neft_transaction_no'];
		$date = date('d/m/Y',$record['neft_transaction_date']);
		$bank = $record['neft_beneficiary_name'];
	  }
	  $receiptData = ['#title'      => 'Fee Receipt',
		'#theme'      => 'print_fee_receipt_page',
		'#receiptNo'   => $record['receipt_number'],
		'#receiptDate' => date('d/m/Y',$record['created_date']),
		'#schoolCode'  => $schoolCode,
		'#address1'    => $address1,
		'#address2'    => $address2,
		'#address3'    => $address3,
		'#schoolName'  => $schoolName,
		'#type'        => $type,
		'#details'     => $details,
		'#bank'        => $bank,
		'#revenueType' => $revenueType,
		'#revenueTypeName' => $revenueTypeName,
		'#netAmount'   => $netAmount,
		'#netAmountWords' => $netAmountWords,
		'#date'        => $date,
		'#sapCode'     => $sapCode
	  ];
    } 
    return $receiptData;
    }
	public function printFeeReceiptForCompanyRun(){
		$masterDataService = \Drupal::service('sewing.master_data');
		$current_user = \Drupal::currentUser();
		$roles = $current_user->getRoles();
		$feeId = $_GET['id'];
		if (isset($feeId) && $feeId != '') {
			$conn = Database::getConnection();
			$query = $conn->select('usha_generate_fee_receipt', 'f')
				->condition('id', $feeId)
				->fields('f');
			$record = $query->execute()->fetchAssoc();
		$schoolData = Node::load($record['school_id']);
		if($schoolData->field_sewing_school_type->target_id == SCHOOL_TYPE_COMPANY_RUN && $record['want_to_add_student_fee'] == 1){
			  $schoolCode = $schoolData->field_sewing_school_code->value;
			  $query = $conn->select('usha_student_fee_receipt', 's')
				->condition('generate_fee_id', $feeId)
				->fields('s');
			  $studentRecords = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
			  $i = 0;
			  foreach($studentRecords as $studentRecord){
				$studentData = Node::load($studentRecord->student_id);
				$studentDataArray[$i]['receiptNumber'] = $record['receipt_number'];
				$studentDataArray[$i]['admissionNumber'] = $studentData->field_student_admission_no->value;
				$studentDataArray[$i]['studentName'] = $studentData->field_student_salutation->value.' '.$studentData->getTitle().' '.$studentData->field_last_name->value;
				$studentDataArray[$i]['receiptDate'] = date('d/m/Y',$record['created_date']); 
				$studentDataArray[$i]['schoolCode'] = $schoolCode; 
				$studentDataArray[$i]['serviceCode'] = '999293 - Other education & training services and educational support services';
				$studentDataArray[$i]['addressOne'] = $studentData->field_address_1->value;
				$studentDataArray[$i]['addressTwo'] = $studentData->field_address_2->value;
				//$studentDataArray[$i]['addressState'] = Node::load($studentData->field_business_state->target_id)->getTitle();
				
				if($record['payment_type'] == 1) {
					$type = 'Cheque/DD';
					$details = $record['cheque_dd_no'];
					$bank = $record['cheque_bank_drawn'];
					$date = !empty($record['cheque_transaction_date'])?date('d/m/Y',$record['cheque_transaction_date']):'--/--/---';
				}else if($record['payment_type'] == 2){
					$type = 'Cash';
					$details = '';
					$date = date('d/m/Y',$record['created_date']);
					$bank = '';
				} else {
					$type = 'NEFT/RTGS';
					$details = $record['neft_transaction_no'];
					$date = date('d/m/Y',$record['neft_transaction_date']);
					$bank = $record['neft_beneficiary_name'];
				}
				$studentDataArray[$i]['paymentType'] = $type;
				$studentDataArray[$i]['paymentDetails'] = $details;
				$studentDataArray[$i]['paymentDate'] = $date;
				$studentDataArray[$i]['paymentBank'] = $bank;
				$netAmount = $studentRecord->payment_to_uil;
				$studentDataArray[$i]['netAmount'] = $netAmount;
				$studentDataArray[$i]['netAmountWords'] = $masterDataService->numberTowords($netAmount);
				$i++;
			  }
			  $receiptData =  ['#title'      => 'Fee Receipt',
				'#theme'      => 'print_fee_receipt_page_for_company_run',
				'#dataArray'   => $studentDataArray,
			  ];
			}
		} 
    return $receiptData;
	}
} 