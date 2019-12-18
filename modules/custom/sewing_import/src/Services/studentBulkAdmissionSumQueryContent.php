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

class studentBulkAdmissionSumQueryContent {
  public static function studentBulkAdmissionSumQueryContentItem($item, &$context){
    $context['sandbox']['current_item'] = $item;
    $message = 'Student Sum Query Update.. ' . $item;
    $results = array();
    update_node($item);
    $context['message'] = $message;
    $context['results'][] = $item;
  }
  function studentBulkAdmissionSumQueryContentItemCallback($success, $results, $operations) {
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
function update_node($item) {
	$conn = Database::getConnection();
	$query = $conn->select('usha_student_fee_receipt', 'f')
		->condition('student_id', $item[0])
		->fields('f');
	$studentFeeRecords = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
	$studentFeeSum = 0;
	foreach($studentFeeRecords as $studentFeeRecord){
		$studentFeeSum = $studentFeeSum + $studentFeeRecord->payment_to_uil;
	}
	$node = \Drupal\node\Entity\Node::load($item[0]);
	$studentDueFee = $node->field_sewing_course_fee_due->value;
	$studentReceiveFee = $studentFeeSum;
	$studentOutstandingFee = $studentDueFee - $studentReceiveFee;
	$node->set('field_sewing_course_fee_received', $studentReceiveFee);
	$node->set('field_sewing_course_fee_out', $studentOutstandingFee);
	$node->save();
}