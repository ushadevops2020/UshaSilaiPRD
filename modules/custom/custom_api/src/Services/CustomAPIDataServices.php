<?php

/**
* @file providing the service that for master Data.
*
*/

namespace  Drupal\custom_api\Services;
use Drupal\Component\Serialization\Json;


class CustomAPIDataServices {
	public function __construct() {
		
	}
	/**
	 * Get Location By Country Id
	 * @params: $country_id as Integer
	 * @return: $locationList as an array
	 */
	public function apiValidation($currentUser, $request) {
		$errorMessage = array();
		$user = \Drupal\user\Entity\User::load($currentUser->id());
	    $csrfToken = $request->headers->get('X-CSRF-Token');

	    if (strpos($request->headers->get('Content-Type'), 'application/json') !== 0) {
	      $errorMessage['isSuccess'] = 0;
	      $errorMessage['responseCode'] = 0;
	      $errorMessage['message'] = 'Content-Type request header is missing';
	    }
	    if(empty($csrfToken)) {
	      $errorMessage['isSuccess'] = 0;
	      $errorMessage['responseCode'] = 0;
	      $errorMessage['message'] = 'X-CSRF-Token request header is missing';
	    } 
	    $queryUId = $request->query->get('uid');
	    $getRequestType = $request->query->get('type');
	    if($getRequestType != 'user_update') {
		    if(empty($queryUId)) {
		      $errorMessage['isSuccess'] = 0;
		      $errorMessage['responseCode'] = 0;
		      $errorMessage['message'] = 'UseId request header is missing.';
		    } else {
		      $account = \Drupal\user\Entity\User::load($queryUId);
		      if ( empty($account)) {
		        $errorMessage['isSuccess'] = 0;
		        $errorMessage['responseCode'] = 0;
				$errorMessage['message'] = 'User is not exists.';
		      }
		    }
		}    
	    return $errorMessage;
	}	
	/**
	 * Get Location By Country Id
	 * @params: $country_id as Integer
	 * @return: $locationList as an array
	 */
	public function getInventoryList($userId = NULL) {
		try {
			$masterDataService = \Drupal::service('silai.master_data');
			$result =  $inventoryArray = array();
			$schoolId = $masterDataService->getSchoolFromUserId($userId);
			$result['responseCode'] = 1;
			if(!empty($schoolId)) {
				$result['message'] = 'Inventory Recived Succesfully';
				$inventoryList = $masterDataService->getInventoryListBySchoolId($schoolId);
				$result['result'] = array();
				foreach ($inventoryList as $key => $inventory) {
				    $inventoryId = $inventory->nid;
				    $nodes = \Drupal\node\Entity\Node::load($inventoryId);
				    $itemId = $nodes->field_silai_item_name->target_id;
				    $itemLoad = \Drupal\node\Entity\Node::load($itemId);
				    $Itemname = $itemLoad->getTitle();
				    $inventoryArray['inventoryId'] = $inventory->id;
				    $inventoryArray['inventoryNodeId'] = $inventory->nid;
				    $inventoryArray['inventoryName'] = $Itemname;
				    $inventoryArray['sendQty'] = $inventory->qty_send;
				    $inventoryArray['receivedQty'] = $inventory->qty_received;
				    $inventoryArray['sendDate'] = date("Y-m-d", $inventory->sent_date);
				    if($inventory->received_date) {
				      $inventoryArray['receivedDate'] = date("Y-m-d", $inventory->received_date);
				    } else {
				    	$inventoryArray['receivedDate'] = '';
				    }
				    $result['result'][] = $inventoryArray;
				}        
			} else {
				$result['message'] = 'No Records Found';
			}	
			return $result;		
		} catch (\Exception $error) {
			$result['message'] = $error->getMessage();
  		}

	}

	/**
	 * get User Profile
	 * @params: $uid as Integer
	 * @return: profile details as an array
	 */
	public function getUserProfile($uId) {
		$result = array();
		$currentUser = \Drupal::currentUser();
	    if(!empty($uId) && $uId == $currentUser->id()) {
	        $result['isSuccess'] = true;
	        $result['message'] = 'User Profile Details';
	        $result['responseCode'] = 1;
		    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
		    $masterDataService = \Drupal::service('silai.master_data');
			$schoolId = $masterDataService->getSchoolFromUserId(\Drupal::currentUser()->id());
			$nodes = \Drupal\node\Entity\Node::load($schoolId);
	        $result['result'] = [
	          "uid" => \Drupal::currentUser()->id(),
	          "name" => $user->getUsername(),
	          "mail" => $user->getEmail(),
	          "firstName" => $user->field_first_name->value,
	          "lastName" => $user->field_last_name->value,
	          "mobile" =>  $user->field_user_contact_no->value,
	          "location" =>  $user->field_user_location->target_id,
	          "school_code" => $nodes->field_school_code->value,
	          "date_of_joining" => $nodes->field_date_open_of_silai_school->value,
	          "total_learners" => 24,
	          "currently_active" => 20,
	          "aadhar_card" => "xxxxxxxxxxxxxxxxxxxxxxx"
	        ];
	    } else {
	    	$result['isSuccess'] = 0;
	        $result['message'] = 'User is not Authenticate.';
	        $result['responseCode'] = 0;
	    }   
		return $result;		
	}

	/**
	 * update User Profile
	 * @params: $uid as Integer
	 * @return: profile details as an array
	 */
	public function updateUserProfileAPI($data= array()) {
		$result = array();
		$currentUser = \Drupal::currentUser();
		// print_r(\Drupal::currentUser()->isAuthenticated());die;
	    if (\Drupal::currentUser()->isAuthenticated()) {
	       user_logout();
           session_destroy();
	    }
		$decoded = Json::decode($data->getContent());
		$data = json_decode( $data->getContent(), TRUE );
		$uId= $decoded['uid'];
		$name= $decoded['name'];
		$mobileNumber= $decoded['mobileNumber'];
		$aadharNo= $decoded['aadharNo'];
		if(!empty($uId) && $uId == $currentUser->id()) {
			$user = \Drupal\user\Entity\User::load($currentUser->id());
			$user->set('field_first_name', $name);
			$user->set('field_user_contact_no', $mobileNumber);
			$violations = $user->validate();
			if (count($violations) === 0) {
				$user->save();
				$result['isSuccess'] = true;
				$result['responseCode'] = 1;
				$result['message'] = 'User profile update Succesfully.';
			} else {
				$result['isSuccess'] = true;
				$result['responseCode'] = 1;
				$result['message'] = 'User profile has not updated Succesfully.';
			}
		}

		return $result;		
	}
   						
}