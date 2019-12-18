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

class studentBulkAdmissionNoUpdateContent {
  public static function studentBulkAdmissionNoUpdateContentItem($item, &$context){
    $context['sandbox']['current_item'] = $item;
    $message = 'Student Updating.. ' . $item;
    $results = array();
    update_node($item);
    $context['message'] = $message;
    $context['results'][] = $item;
  }
  function studentBulkAdmissionNoUpdateContentItemCallback($success, $results, $operations) {
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
	$node = \Drupal\node\Entity\Node::load($item[0]); 
	$node->set('field_student_admission_no', $item[2]);
	$node->save();
	if(!empty($item[3])){
		$database = \Drupal::database();
		$dataArray = array('student_id' => $item[0]);
		$queryUpdate = $database->update('usha_student_fee_receipt')->fields($dataArray)->condition('student_id', $item[3])->execute();
	}
} 