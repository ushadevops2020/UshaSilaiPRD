<?php
namespace Drupal\silai_import\Services;

use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

class schoolSurveyBulkImportContent {
  public static function schoolSurveyAddImportContentItem($item, &$context){
    $context['sandbox']['current_item'] = $item;
    $message = 'School Survey Data Importing.. ' . $item;
    $results = array();
    create_node($item);
    $context['message'] = $message;
    $context['results'][] = $item;
  }
  function schoolSurveyAddImportContentItemCallback($success, $results, $operations) {
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

function create_node($item) {
	$schoolNid = $item[0];
	$conn = Database::getConnection();
	$query = $conn->select(TABLE_SILAI_ADD_SCHOOL_DATA, 's')
			->condition(NID, $schoolNid)
			->fields('s');
	$school_data = $query->execute()->fetchAssoc();
	$dataArray = [
		'signage_in_the_school' => $item[1],
		'condition_of_signboard' => $item[2],
		'average_learners_attending' => $item[3],
		'how_many_learners_you_have_trained' => $item[4],
		'average_fee_charged' => $item[5],
		'monthly_income_learners_fee' => $item[6],
		'monthly_income_stitching' => $item[7],
		'income_from_sewing_machine_repairing' => $item[8],
		'monthly_income_from_silai_schools' => $item[9],
		'usha_black_machine_you_have' => $item[10],
		'how_many_non_usha_black_machines' => $item[11],
		'religion' => $item[12],
	];
	$dataArrayInsert = [
		'nid' => $schoolNid,
		'signage_in_the_school' => $item[1],
		'condition_of_signboard' => $item[2],
		'average_learners_attending' => $item[3],
		'how_many_learners_you_have_trained' => $item[4],
		'average_fee_charged' => $item[5],
		'monthly_income_learners_fee' => $item[6],
		'monthly_income_stitching' => $item[7],
		'income_from_sewing_machine_repairing' => $item[8],
		'monthly_income_from_silai_schools' => $item[9],
		'usha_black_machine_you_have' => $item[10],
		'how_many_non_usha_black_machines' => $item[11],
		'religion' => $item[12],
	];
	if(!empty($school_data)){
		$database = \Drupal::database();
		$query_update = $database->update(TABLE_SILAI_ADD_SCHOOL_DATA)->fields($dataArray)->condition(NID, $item[0])->execute();
	}else{
		$database = \Drupal::database();
	    $query = $database->insert(TABLE_SILAI_ADD_SCHOOL_DATA)->fields($dataArrayInsert)->execute();
	}
} 