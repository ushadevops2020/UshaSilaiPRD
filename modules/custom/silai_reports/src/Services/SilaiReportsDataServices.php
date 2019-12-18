<?php
/**
 * @file providing the service that for master Data.
*/
namespace  Drupal\silai_reports\Services;

use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\TablePosition;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SilaiReportsDataServices {
	public function __construct() {
		
	}
	/**
	 * Get Location By Country Id
	 * @params: $country_id as Integer
	 * @return: $locationList as an array
	 */
	public function getSilaiLocation() {
		$query = \Drupal::entityQuery('node')
		    ->condition('type', 'manage_silai_locations')
		    ->condition(STATUS, 1);
		$ids = $query->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
			$locationList[$node->id()] = $node->getTitle();
		}
		return $locationList;		
	}
	/**
	 * Get Location List
	 * @params: $country_id as Integer
	 * @return: $locationList as an array
	 */
	public function getSilaiLocationList($locationIds = []) {
		$query = \Drupal::entityQuery('node')
		    ->condition('type', 'manage_silai_locations')
		    ->condition(STATUS, 1);
		if($locationIds) {
			$query->condition('nid', $locationIds, 'IN');
		}   
		$ids = $query->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
			$locationList[$node->id()]['name'] = $node->getTitle();
			$locationList[$node->id()]['code'] = $node->field_silai_location_code->value;
		}
		return $locationList;		
	}
	/**
	 * Get School Type List
	 * @return: $schoolTypeList as an array
	*/
	public function getSilaiSchoolTypeList() {
		$query = \Drupal::entityQuery('node')
		->condition('type', 'silai_school_type_master')
		->condition(STATUS, 1);
		$ids = $query->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
			$schoolTypeList[$node->id()] = $node->getTitle();
		}
		return $schoolTypeList;
	}
	/**
	 * Get School Type List
	 * @return: $schoolTypeList as an array
	*/
	public function getSilaiNgoList($locationId) {
		$query = \Drupal::entityQuery('node')
		->condition('type', 'ngo')
		->condition('field_ngo_location', $locationId, 'IN')
		->condition(STATUS, 1);
		$ids = $query->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
			$ngoList[$node->id()]['name'] = $node->getTitle();
			$ngoList[$node->id()]['code'] = $node->field_ngo_code->value;
		}
		return $ngoList;
	}
	/**
	 * Get School Data
	 * @return: $schoolTypeList as an array
	*/
	public function getSilaiSchoolsNodes($requestData = []) {
		$user = User::load(\Drupal::currentUser()->id());
		$userLocationId = $user->field_user_location->target_id;
		$current_user = \Drupal::currentUser();
		$roles = $current_user->getRoles();
		$query =\Drupal::entityQuery('node')
	      ->condition('type', 'silai_school');
	      $query->condition(STATUS, 1);
		  if($roles[1] == 'pc'){
			$query->condition('field_silai_location', $userLocationId);
		  }
	      $query->condition('field_sil_school_approval_status', 1);
	      if(!$requestData || ($requestData && !$requestData['action']) || ($requestData && $requestData['page'])) {
	      	$query->pager(20);
	      }
	    $ids = $query->execute();
	   
	    $nodes = \Drupal\node\Entity\Node::loadMultiple($ids); 
	    
	    return $nodes;
	}
	/**
	 * Get School Data
	 * @return: $schoolTypeList as an array
	*/
	public function getSilaiSchoolsNodesAll() {
		$user = User::load(\Drupal::currentUser()->id());
		$userLocationId = $user->field_user_location->target_id;
		$current_user = \Drupal::currentUser();
		$roles = $current_user->getRoles();
		$query =\Drupal::entityQuery('node')->condition('type', 'silai_school');
			$query->condition(STATUS, 1);
			if($roles[1] == 'pc'){
				$query->condition('field_silai_location', $userLocationId);
			}
			$query->condition('field_sil_school_approval_status', 1);
	    $ids = $query->execute();
	   
	    $nodes = \Drupal\node\Entity\Node::loadMultiple($ids); 
	    
	    return $nodes;
	}
#
}