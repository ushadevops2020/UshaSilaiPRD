<?php
namespace Drupal\sewing_import\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilder;
use Drupal\views\Views;
use Symfony\Component\HttpFoundation\JsonResponse; 
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Database\Database;
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\taxonomy\Entity\Term;

class revenueBulkImportContent {
  public static function revenueBulkImportContentItem($item, &$context){
    $context['sandbox']['current_item'] = $item;
    $message = 'Student Data Importing.. ' . $item;
    $results = array();
    create_node($item);
    $context['message'] = $message;
    $context['results'][] = $item;
  }
  function revenueBulkImportContentItemCallback($success, $results, $operations) {
    if ($success) {
      $message = \Drupal::translation()->formatPlural(
        count($results),
        'One item processed.', '@count items successfully processed.'
      );
    }
    else {
      $message = t('Finished with an error.');
    }
    drupal_set_message($message);
  }
}

// This function actually creates each item as a node as type 'Page'
function create_node($item) {
  $feeDataArray = [
        'receipt_number' => $item[0],
        'school_id' => $item[2],
        'revenue_head_type' => REVENUE_BULK_PAYMENT_TYPE_REVENUE_HEAD[$item[3]],
        'want_to_add_student_fee' => ($item[3] == 'ST') ? 1 : 0,
        'total_student_fee' => ($item[3] == 'ST') ? $item[7] : 0,
        'total_fee_entry' => $item[6], 
        'total_pay_to_uil' => $item[7],
        'tax' => $item[8],
        'payment_type' => ($item[9] == 3) ? 0 : $item[9],
        'neft_beneficiary_name' => ($item[9] == 3) ? $item[15] : 0,
        'neft_beneficiary_account_no' => ($item[9] == 3) ? $item[16] : 0,
        'neft_remitter_name' => ($item[9] == 3) ? $item[17] : 0,
        'neft_remitter_account_no' => ($item[9] == 3) ? $item[18] : 0,
        'neft_ifsc_code' => ($item[9] == 3) ? $item[19] : 0,
        'neft_transaction_no' => ($item[9] == 3) ? $item[20] : 0,
        'neft_transaction_date' => ($item[9] == 3) ? $item[21] : 0,
        'neft_transaction_time' => ($item[9] == 3) ? $item[22] : 0,
        'cheque_dd_no' => ($item[9] == 1) ? $item[10] : 0,
        'cheque_amount' => ($item[9] == 1) ? $item[11] : 0,
        'cheque_bank_drawn' => ($item[9] == 1) ? $item[14] : 0,
        'cheque_transaction_date' => ($item[9] == 1) ? $item[12] : 0,
        'cheque_transaction_time' => ($item[9] == 1) ? $item[13] : 0,
        'cash_amount' => ($item[9] == 2) ? $item[11] : 0,
        'created_date' => $item[1],
        'created_by' => \Drupal::currentUser()->id(),
      ];
      $database = Database::getConnection(); 
      $query_id = $database->insert('usha_generate_fee_receipt')->fields($feeDataArray)->execute();
      if($item[3] == 'ST'){
        $studentFeeDataArray = [
          'generate_fee_id' => $query_id,
          'student_id' => $item[5],
          'received_fee' => $item[6],
          'payment_to_uil' => $item[7],
          'created_date' => $item[1],
        ];
        $database = Database::getConnection(); 
        $query_id = $database->insert('usha_student_fee_receipt')->fields($studentFeeDataArray)->execute();
      }
} 