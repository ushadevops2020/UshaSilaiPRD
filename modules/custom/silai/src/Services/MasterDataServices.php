<?php
/**
 * @file providing the service that for master Data.
*/

namespace  Drupal\silai\Services;
use Drupal\Core\Database\Database;

class MasterDataServices {
	public function __construct() {
		
	}

	/**
	 * Get Location By Country Id
	 * @params: $country_id as Integer
	 * @return: $locationList as an array
	 */
	public function getLocationByCountryId($countryId = NULL) {
		$query = \Drupal::entityQuery('node')
		    ->condition('type', 'manage_silai_locations')
		    ->condition(STATUS, 1);
		if($countryId) {		    
		    $query->condition('field_silai_country', $countryId);
		} 
		$query->sort('title');   
		$ids = $query->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
			$locationList[$node->id()] = $node->getTitle();
		}
		return $locationList;		
	}

   /**
    * Get State By Location
    * @params: $locationId as integer
    * @return: $stateList as an array
   */
	public function getStatesByLocationId($locationId) {
		if($locationId && !is_array($locationId)) {
			$locationId = [$locationId];
		}
		$query =\Drupal::entityQuery('node')
			->condition('type', 'silai_business_states')
			->condition(STATUS, 1);
		if($locationId) {
			$query->condition('field_silai_location', $locationId, 'IN');
		}

		$ids = $query->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);

		foreach($nodes as $node) {
			$stateList[$node->id()] = $node->getTitle();
		}
		return $stateList;
	}
			
	/**
	 * Get District By State
	 * @params: $stateId as integer
	 * @return: $districtList as an array
	*/
	public function getDistrictsByStateId($stateId) {
		$ids = \Drupal::entityQuery('node')
		->condition('type', 'silai_district')
		->condition(STATUS, 1)
		->condition('field_silai_business_state', $stateId)
		->sort('title')
		->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
			$districtList[$node->id()] = $node->getTitle();
		}
		return $districtList;
	}

	/**
	 * Get Town By District Id
	 * @params: $districtId as integer
	 * @return: $districtList as an array
	*/
	public function getTownsByDistrictId($districtId) {
		$ids = \Drupal::entityQuery('node')
		->condition('type', 'silai_town')
		->condition(STATUS, 1)
		->condition('field_silai_district', $districtId)
		->sort('title')
		->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
			$townList[$node->id()] = $node->getTitle();
		}
		return $townList;
	}

	/**
	 * Get Town By District Id
	 * @params: $districtId as integer
	 * @return: $districtList as an array
	*/
	public function getTownsByDistrictName($districtId) {
		$ids = \Drupal::entityQuery('node')
		->condition('type', 'silai_town')
		->condition(STATUS, 1)
		->condition('field_silai_district', $districtId)
		->sort('title')
		->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
			$townList[$node->id()] = $node->getTitle();
		}
		return $townList;
	}

	/**
	 * Get Blocks By Town Id
	 * @params: $townId as integer
	 * @return: $blockList as an array
	*/
	public function getBlocksByTownId($townId) {
		$ids = \Drupal::entityQuery('node')
		->condition('type', 'silai_blocks')
		->condition(STATUS, 1)
		->condition('field_silai_town', $townId)
		->sort('title')
		->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
			$blockList[$node->id()] = $node->getTitle();
		}
		return $blockList;
	}

	/**
	 * Get Users by Location
	 * @params: $locationId as integer
	 * @return: $blockList as an array
	*/
	public function getUsersByLocation($locationId, $roles) {
		if(!is_array($locationId)) {
			$locationId = [$locationId];
		}
		$query = \Drupal::entityQuery('user');
	    if($locationId) {
	        $query->condition('field_user_location', $locationId, 'IN');
        } 
        if($roles) {
        	$query->condition('roles', $roles, 'IN');	
        }
		$uids = $query->execute();
	    $user_storage = \Drupal::entityManager()->getStorage('user');

	    #Load multiple nodes
	    $users = $user_storage->loadMultiple($uids);
	    foreach ($users as $n) {
        $usersList[] = strtolower($n->id());
    }
		return $usersList;
	}

	/**
	 *
	 */
	 public function getNumberOfTrainee($nid) {
	    $connection = \Drupal::database();
	    $query = $connection->select('node_field_data', 'n');
	    $query->join('node__field_silai_trainer_id', 't', 'n.nid = t.entity_id');
	    $query->condition('n.status', 1);
	    $query->condition('t.bundle', 'silai_trainee');
	    $query->condition('t.field_silai_trainer_id_value', $nid);
	    $count_query = $query->countQuery();
	    $num_rows = $query->countQuery()->execute()->fetchField();
	    return $num_rows;
	 }

	 /**
	 * Get Ngo by location Id
	 * @params: $locationId as integer
	 * @return: $ngoList as an array
	*/
	public function getNgoByLocationId($locationId) {
		if(!is_array($locationId)) {
			$locationId = [$locationId];
		}
		$ids = \Drupal::entityQuery('node')
		->condition('type', 'ngo')
		->condition('field_ngo_location', $locationId, 'IN')
		->sort('title')
		->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
			$ngoList[$node->id()] = $node->getTitle();
		}
		return $ngoList;
	}

	/**
	 * Get Ngo by NgoId
	 * @params: $locationId as integer
	 * @return: $ngoList as an array
	*/
	public function getSchoolsByNgoId($ngoId) {
		$ids = \Drupal::entityQuery('node')
		->condition('type', SILAI_SCHOOL)
		->condition('field_name_of_ngo', $ngoId)
		->condition(FIELD_SIL_SCHOOL_APPROVAL_STATUS, APPROVED_STATUS)
		->sort('title')
		->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
			$schoolList[$node->id()] = $node->getTitle();
		}
		return $schoolList;
	}



	/**
	 * Get Ngo by User Id
	 * @params: $locationId as integer
	 * @return: $ngoList as an array
	*/
	public function getLinkedNgoForUser($userId) {
		
		$database = \Drupal::database();
        $connection = Database::getConnection();
        $check_qry = $connection->select('silai_ngo_associated_user', 'n')->fields('n', array('nid', 'user_id'))->condition('user_id', $userId);
        
        $check_data = $check_qry->execute();
        $results = $check_data->fetchAll(\PDO::FETCH_OBJ);

		foreach($results as $node) {
			$ngo[$node->user_id] = $node->nid;
		}
		return $ngo;
	}


	/**
	 * Get Items by item group Id
	 * @params: $itemGroupId as integer
	 * @return: $itemList as an array
	*/
	public function getItemsByItemGroup($itemGroupId) {
		$ids = \Drupal::entityQuery('node')
		->condition('type', 'silai_items')
		->condition('field_silai_item_group', $itemGroupId)
		->sort('title')
		->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
			$itemList[$node->id()] = $node->getTitle();
		}
		return $itemList;
	}

	/**
	 * marking school approved with user approval
	 * @param  [type] $schoolID integer
	 * @return [type]           Mix
	 */
	public function approveSchool($schoolID, $data) {
		//load school
		$node = \Drupal\node\Entity\Node::load($schoolID);
		$userId = $node->field_silai_teacher_user_id->value;
		$stateId = $node->field_silai_business_state->target_id;
		$locationId = $node->field_silai_location->target_id;
		$schoolCreatedBy = $node->uid->target_id;
		$schoolCode = $node->field_school_code->value;

		$currentUser = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
	    $currentUserRoles = $currentUser->getRoles();
	    $currentUserid = $currentUser->id();

	    //load state
		$nodeState = \Drupal\node\Entity\Node::load($stateId);
		$stateCode = $nodeState->field_silai_state_code->value;

		// generate School Code
		$prefix = strtoupper($stateCode);
		$prevSchoolCode = $this->generate_code($schoolID, SILAI_SCHOOL, 'field_school_code', $prefix);
		
		if($prevSchoolCode) {
			$prevSeqNo = (int) substr($prevSchoolCode, -4);
			$seqNo = str_pad( $prevSeqNo + 1, 4, "0", STR_PAD_LEFT );
			
		} else {
			$seqNo = '0001';
		}
		
		//load state
		/*$nodeState = \Drupal\node\Entity\Node::load($stateId);
		$stateCode = $nodeState->field_silai_state_code->value;
		$countryId = $nodeState->field_silai_country->target_id;

		$nodeCountry = \Drupal\node\Entity\Node::load($countryId);
		$countryCode =  $nodeCountry->field_silai_country_code->value;
		*/
		$schoolCode = strtoupper($stateCode).$seqNo;

		$userData = [];
		$userData = $this->getUsersByUsername($schoolCode);
		
		$username = strtoupper($stateCode).$seqNo;
		//approve school and update school code
	    $node->set(FIELD_SIL_SCHOOL_APPROVAL_STATUS, APPROVED_STATUS); 
	    $node->set('field_sil_school_status_remarks', $data['remarks']);
	    $node->set('field_school_code', $schoolCode);
	    $node->setNewRevision(FALSE);    
	    $node->save();
	    
	    //approve user
	    $user = \Drupal\user\Entity\User::load($userId);
		
		$userPwd = 'qwerty';
	    if(empty($userData)) {
	    	$user->setUsername($username);
			$user->setPassword($userPwd);
	    }
	    $user->set('field_user_approval_status', APPROVED_STATUS);
	    $user->save();
	    
	    $user = \Drupal\user\Entity\User::load($schoolCreatedBy);
      	//$userRoles = $user->getRoles(); 
      	//$targetRoles = [$userRoles[1]];
      	$targetUsers = [$schoolCreatedBy];
      	$message = preg_replace('/{.*}/', $schoolCode, SCHOOL_APPROVAL_MESSAGE);
      	$data = [
	        'sender_role' => $currentUserRoles[1],
	        'receiver_id' => '',
	        'receiver_role' => '',
	        'message' => $message,
	        'location' => $locationId,
	        'created_by' => $currentUserid
	      ];
	  if(!empty($targetUsers)){
      	$this->notificationAlert($data, $targetUsers);
	  } 
	    return true;

	}

	/**
	 * marking school rejected with user rejection
	 * @param  [type] $schoolID integer
	 * @return [type]           Mix
	 */
	public function rejectSchool($schoolID, $data) {
		$node = \Drupal\node\Entity\Node::load($schoolID);
		$userId = $node->field_silai_teacher_user_id->value;
		$locationId = $node->field_silai_location->target_id;
		$schoolCreatedBy = $node->uid->target_id;
		$schoolCode = $node->field_school_code->value;

		$currentUser = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
	    $currentUserRoles = $currentUser->getRoles();
	    $currentUserid = $currentUser->id();

		//approve school and update school code
	    $node->set(FIELD_SIL_SCHOOL_APPROVAL_STATUS, REJECTED_STATUS);
	    $node->set('field_sil_school_status_remarks', $data['remarks']);
	    //$node->set('field_school_code', $schoolCode);
	    $node->setNewRevision(FALSE);    
	    $node->save();
	    
	    //approve user
	    $user = \Drupal\user\Entity\User::load($userId);

	    $user->set('field_user_approval_status', REJECTED_STATUS);
	    $user->save();
		
		// For Notification System
	    $user = \Drupal\user\Entity\User::load($schoolCreatedBy);
      	// $userRoles = $user->getRoles(); 
      	// $targetRoles = [$userRoles[1]];
      	$targetUsers = [$schoolCreatedBy];
      	$message = preg_replace('/{.*}/', $schoolCode, SCHOOL_REJECTION_MESSAGE);
      	
      	$data = [
	        'sender_role' => $currentUserRoles[1],
	        'receiver_id' => '',
	        'receiver_role' => '',
	        'message' => $message,
	        'location' => $locationId,
	        'created_by' => $currentUserid
	      ];
	    if(!empty($targetUsers)){
      		$this->notificationAlert($data, $targetUsers);
      	}
	    return true;

	}

	/**
	 * [function to generate dynamic codes]
	 * @param  [int] $nid  
	 * @param  [string] $type 
	 * @return [string]       
	 */
	public function generate_code($nid, $type, $field = 'field_school_code', $code='') {
		$ids = \Drupal::entityQuery('node')
			->condition('type', $type)
			->condition($field, '%'. db_like($code) . '%', 'LIKE')
			->sort($field , 'DESC')
			->range(0, 1)
			->execute();
		
		$list = \Drupal\node\Entity\Node::loadMultiple($ids);
		
		foreach($list as $n) {
			$prevCode = ($n->$field->value) ? $n->$field->value : '';
		}
		
		return $prevCode;
	}


	/**
	 * [function to check dynamic codes]
	 * @param  [int] $nid  
	 * @param  [string] $type 
	 * @return [string]       
	 */
	public function checkCode($prevCode, $type, $parantCode) {
		$isExist = false;
		switch ($type) {
			case 'silai_district':
				$existingParentCode = substr($prevCode, 0, 4);
				break;

			case 'silai_blocks':
				$existingParentCode = substr($prevCode, 0, 6);
				break;

			case 'silai_villages':
				$existingParentCode = substr($prevCode, 0, 8);
				break;

			case 'trainer_silai':
				$existingParentCode = substr($prevCode, 0, 6);
				break;

			case SILAI_SCHOOL:
				$existingParentCode = substr($prevCode, 0, 4);
				break;

			case 'manage_agreements':
				$existingParentCode = substr($prevCode, 0, 9);
				break;

			case 'silai_learners_manage':
				$existingParentCode = substr($prevCode, 0, 10);
				break;	
			
			default:
				$isExist = false;
				break;
		}

		if($existingParentCode == $parantCode) {
			$isExist = true;	
		} 
		 return $isExist;
	}

	

	/**
	 * Implementation of getInventoryListBySchoolId
	 */
	function getInventoryListBySchoolId($schoolId) {
	    $connection = \Drupal::database();
	    $query = $connection->select('node_field_data', 'n');
	    $query->fields('c', array('id', 'nid', 'qty_send', 'qty_received', 'sent_date', 'received_date'));
	    $query->join('custom_manage_inventory', 'c', 'n.nid = c.nid');
	    $query->condition('n.status', 1);
	    $query->condition('c.receiver_role', 'silai_school_admin');
	    $query->condition('c.ref_id', $schoolId);
		$result = $query->execute()->fetchAll();
		return $result;
	}

	/**
	 * Implementation of get School Id From User id
	 */
	function getSchoolFromUserId($teacherId) {
	    $connection = \Drupal::database();
	    $query = $connection->select('node_field_data', 'n');
	    $query->fields('n', array('nid'));
	    $query->join('node__field_silai_teacher_user_id', 't', 'n.nid = t.entity_id');
	    $query->condition('n.status', 1);
	    $query->condition('t.bundle', SILAI_SCHOOL);
	    $query->condition('t.field_silai_teacher_user_id_value', $teacherId);
	    $query->orderBy('n.created', 'DESC'); 
	    $query->range(0, 1);
		$result = $query->execute()->fetchField();
		return $result;		
	}

	/**
	 * Implementation of get get Nfa Sanctioned Amt by NFA number
	 */
	function getNfaSanctionedAmt($nfaNumber) {
	   $ids = \Drupal::entityQuery('node')
		->condition('type', 'nfa')
		->condition('title', $nfaNumber)
		->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
			$getNfaSanctionedAmt = $node->field_sactioned_amount->value;
		}
		return ($getNfaSanctionedAmt) ? $getNfaSanctionedAmt : 0;
	}

	/**
	 * Implementation of getAgreements By Nfa 
	 */
	function getAgreementsByNfa($nfaNumber) {
	   $ids = \Drupal::entityQuery('node')
		->condition('type', 'manage_agreements')
		->condition('field_agreement_nfa_number', $nfaNumber)
		->execute();

		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);

		foreach($nodes as $node) {
			$agreements[$node->id()]['field_agreement_amount'] = $node->field_agreement_amount->value;
		}
		
		return ($agreements) ? $agreements : [];
	}
	/**
	 * Generate Learner Id 
	 * @param  [type] $schoolCode integer
	 * @return [type]           Mix
	 */
	public function generateLearnerId($schoolCode, $currentYear, $contentType) {
		$ids = \Drupal::entityQuery('node')
            ->condition('type', 'silai_learners_manage')
            ->execute();
        $nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
        foreach($nodes as $node) {
            $learner_ids[] = $node->field_learner_id->value;
        }
        rsort( $learner_ids );
        #select 1st array value
        $LearnerId = array_slice( $learner_ids, 0, 1 );
        #Break starting 10 string
        $Sequence = substr($LearnerId[0], 10);
        #add 1 and round in 0000
        $SequenceNo = str_pad( $Sequence+1, 4, "0", STR_PAD_LEFT );
        #Mearge all values
        $SequenceFormat = $schoolCode.''.$currentYear.''.$SequenceNo;
	    return $SequenceFormat;
	}
	/**
	 * Implementation of getNgoLocationIds for current userID 
	 */
	function getNgoLocationIds($currentUserid) {
		$database = \Drupal::database();
        $connection = Database::getConnection();
        $check_qry = $connection->select('silai_ngo_associated_user', 'n')->fields('n', array('nid', 'user_id'))->condition('user_id', $currentUserid);
        
        $check_data = $check_qry->execute();
        $results = $check_data->fetchAll(\PDO::FETCH_OBJ);

		foreach($results as $node) {
			$ngo[$node->user_id] = $node->nid;
		}
	    $ngoNodesData = \Drupal\node\Entity\Node::loadMultiple($ngo);
	    foreach ($ngoNodesData as $ngoNode) {
	        $ngoLocations = $ngoNode->get("field_ngo_location")->getValue();
	    }
	    foreach ($ngoLocations as $ngoLocation) {
	        $locationArray[] = $ngoLocation['target_id'];
	    }
		return $locationArray;
	}


	/**
	 * Get Blocks By Town Id
	 * @params: $townId as integer
	 * @return: $blockList as an array
	*/
	public function getVillagesByBlockId($blockId) {
		$ids = \Drupal::entityQuery('node')
		->condition('type', 'silai_villages')
		->condition(STATUS, 1)
		->condition('field_silai_block', $blockId)
		->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
			$villageList[$node->id()] = $node->getTitle();
		}
		return $villageList;
	}
	/**
	 * Get Count By Content Type
	 * @params: $districtId as integer
	 * @return: $districtList as an array
	*/
	public function getNodeCountForChart($contentType, $locationId) {
		if($locationId){
			$ids = \Drupal::entityQuery('node')
            ->condition('type', $contentType)
            ->condition('field_silai_location', $locationId)
            ->condition('field_sil_school_approval_status', 1)
            ->condition('status', 1)
            ->execute();
		}else{
			$ids = \Drupal::entityQuery('node')
            ->condition('type', $contentType)
            ->condition('field_sil_school_approval_status', 1)
            ->condition('status', 1)
            ->execute();
		}
    	$number =  count($ids);
		return $number;
	} 


	/**
	 * notification message on some points like add school, approve school etc
	 * @param  [Array] $data 
	 * @return [Boolean]   Mix
	 */
	public function notificationAlert($data, $targetRoles= '') {
		if(!is_array($targetRoles)) {
			$targetRoles = [$targetRoles];	
		}
		$database = \Drupal::database();
		foreach ($targetRoles as $key => $value) {
			$user = \Drupal\user\Entity\User::load($value);
    		$userRoles = $user->getRoles();
    		$data['receiver_id'] = $value;
			$data['receiver_role'] = $userRoles[1];
			$data['created_at'] = time();
			if($userRoles[1] == ROLE_SILAI_PC) {
				$data['location'] = $user->field_user_location->target_id;	
			}
			$query = $database->insert(TABLE_CUSTOM_NOTIFICATION)->fields($data)->execute();
		}
		
	    return true;

	}

	/**
   * Get Blocks By District Id
   * @params: $townId as integer
   * @return: $blockList as an array
	*/
	/*
	public function getBlocksByDistrictId($districtId) {
		$ids = \Drupal::entityQuery('node')
		->condition('type', 'silai_town')
		->condition(STATUS, 1)
		->condition('field_silai_district', $districtId)
		->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
		  $townList[$node->id()] = $node->id();
		}

		$townIds = \Drupal::entityQuery('node')
		->condition('type', 'silai_blocks')
		->condition(STATUS, 1)
		->condition('field_silai_town', $townList, 'IN')
		->execute();

		$townNodes = \Drupal\node\Entity\Node::loadMultiple($townIds);
		foreach($townNodes as $node) {
		  $blockList[$node->id()] = $node->getTitle();
		}
		return $blockList;
	}*/

  /**
   * Get Blocks By District Id
   * @params: $townId as integer
   * @return: $blockList as an array
   */
	public function getBlocksByDistrictId($districtId) {
		$townIds = \Drupal::entityQuery('node')
		->condition('type', 'silai_blocks')
		->condition(STATUS, 1)
		->condition('field_silai_district', $districtId)
		->sort('title')
		->execute();

		$townNodes = \Drupal\node\Entity\Node::loadMultiple($townIds);
		foreach($townNodes as $node) {
		  $blockList['"'.$node->id().'"'] = $node->getTitle();
		}
		return $blockList;
	}	

	/**
   * Get School Code By Village Id
   * @params: $townId as integer
   * @return: $schoolCode as an array
  */
	public function getSchoolCodeByVillageId($villageId, $ngoId='') {
		$results = array();
		$query = \Drupal::entityQuery(NODE);
	    $query->condition(TYPE, 'silai_school');
	    $query->condition('field_sil_school_approval_status', APPROVED_STATUS);
	    $query->condition('field_silai_village', $villageId);
	    if(!empty($ngoId)) {
	    	$query->condition('field_name_of_ngo', $ngoId);
	    }
		$nids = $query->execute();
		$node_storage = \Drupal::entityManager()->getStorage(NODE);
		$nodes = $node_storage->loadMultiple($nids);
		foreach ($nodes as $n) {
		  $results[$n->id()] = strtolower($n->field_school_code->value);
		}
		return $results;
	}


	/**
	 * Get Users by Username
	 * @params: $locationId as integer
	 * @return: $blockList as an array
	*/
	public function getUsersByUsername($username) {
		$query = \Drupal::entityQuery('user');
	    $query->condition('name', $username, '=');
        
		$uids = $query->execute();
	    $user_storage = \Drupal::entityManager()->getStorage('user');
	    
	    #Load multiple nodes
	    $users = $user_storage->loadMultiple($uids);
	    foreach ($users as $n) {
        $usersList[] = strtolower($n->id());
    }
		return $usersList;
	}


	/**
	 * Get Users by Roles
	 * @params: $roles as array
	 * @return: $usersList as an array
	*/
	public function getUsersByRole($roles = [], $location='') {
		if(!is_array($location)) {
			$location = [$location];	
		}
		$query = \Drupal::entityQuery('user');
	    $query->condition('roles', $roles, 'IN');
        if(!empty($location)) {
        	$query->condition('field_user_location', $location, 'IN');	
        }
		$uids = $query->execute();
	    $user_storage = \Drupal::entityManager()->getStorage('user');
	    
	    #Load multiple nodes
	    $users = $user_storage->loadMultiple($uids);
	    foreach ($users as $n) {
        	$usersList[] = strtolower($n->id());
    	}
		return $usersList;
	}

	/**
	 * Get Users by Ref Id
	 * @params: $roles as array
	 * @return: $usersList as an array
	*/
	public function getUsersByRefId($refId, $type = ROLE_SILAI_NGO_ADMIN) {
		if($type == ROLE_SILAI_NGO_ADMIN) {
			$database = \Drupal::database();
	        $connection = Database::getConnection();
	        $check_qry = $connection->select('silai_ngo_associated_user', 'n')->fields('n', array('nid', 'user_id'))->condition('nid', $refId);
	        
	        $check_data = $check_qry->execute();
	        $results = $check_data->fetchAll(\PDO::FETCH_OBJ);

			foreach($results as $node) {
				$users[] = $node->user_id;
			}
		} else if($type == ROLE_SILAI_SCHOOL_ADMIN) {
			$database = \Drupal::database();
	        $connection = Database::getConnection();
	        $check_qry = $connection->select('node__field_silai_teacher_user_id', 'n')->fields('n', array('entity_id', 'field_silai_teacher_user_id_value'))->condition('entity_id', $refId);
	        
	        $check_data = $check_qry->execute();
	        $results = $check_data->fetchAll(\PDO::FETCH_OBJ);

			foreach($results as $node) {
				$users[] = $node->field_silai_teacher_user_id_value;
			}
		}

		return $users;	
	}

	/**
	 * Get School Code by NGO ID
	 * @params: $school cide Id as integer
	 * @return: $ngoList as an array
	*/
	public function getSchoolCodeIds($ngoId) {
		$query = \Drupal::entityQuery(NODE);
	    $query->condition(TYPE, 'silai_school');
	    $query->condition('field_sil_school_approval_status', APPROVED_STATUS);
	    $query->condition('field_name_of_ngo', $ngoId);
		$nids = $query->execute();
		$node_storage = \Drupal::entityManager()->getStorage(NODE);
		$nodes = $node_storage->loadMultiple($nids);
		foreach ($nodes as $n) {
		  $results[$n->id()] = $n->id();
		}
		return $results;
	}

	/**
	 * Function to validate duplicate field of node
	 * @param  string $type  [description]
	 * @param  string $field [description]
	 * @param  Mix $code  [description]
	 * @param  string $id    [description]
	 * @return Boolean        [description]
	 */
	public function validateDuplicate($type, $field, $code, $id='') {
		if(empty($id)) {
			$node = \Drupal::routeMatch()->getParameter(NODE);
		    if($node instanceof \Drupal\node\NodeInterface) {
		      $nid = $node->id();
		    }
		} else {
			$nid = $id;
		}
		$status = true;
	    if($type && $field && $code) {
		    $query = \Drupal::entityQuery(NODE)
		        ->condition(TYPE, $type)
		        ->condition($field, $code);

		    $nids = $query->execute();
		    $node_storage = \Drupal::entityManager()->getStorage(NODE);

		    #Load multiple nodes
		    $dataArray = $node_storage->loadMultiple($nids);

		    if(count($dataArray) >= 1 &&  empty($dataArray[$nid])) {
		        $status = false;
	    	} 

    	}

    	return $status;
	}

	/**
	 * Function to validate duplicate field of user
	 * @param  string $type  [description]
	 * @param  string $field [description]
	 * @param  Mix $code  [description]
	 * @param  string $id    [description]
	 * @return Boolean        [description]
	 */
	public function validateDuplicateUser($field, $code, $id='') {
		if(empty($id)) {
			$uid = \Drupal::routeMatch()->getParameter(USER);
		    if($user instanceof \Drupal\user\UserInterface) {
		      $uid = $user->id();
		    }
		} else {
			$uid = $id;
		}
		$status = true;
	    if($field && $code) {
		    $query = \Drupal::entityQuery(USER)
		           ->condition($field, $code);

		    $uids = $query->execute();
		    $user_storage = \Drupal::entityManager()->getStorage(USER);

		    #Load multiple nodes
		    $dataArray = $user_storage->loadMultiple($uids);

		    if(count($dataArray) >= 1 &&  empty($dataArray[$uid])) {
		        $status = false;
	    	} 

    	}

    	return $status;
	}

	/**
    * getSilai School detail By school ID
    * @params: $userId as integer
    * @return: $schoolList as an array
   */
	public function getSchoolDetailById($schoolId) {
		if($schoolId) {
			$node_storage = \Drupal::entityManager()->getStorage('node');
			$schoolData = $node_storage->load($schoolId);

			return $schoolData;
		}
		
	}


	public  function getAllLearnerAssWithNgo($currentUserId){
		$ngoData = $this->getLinkedNgoForUser($currentUserId);
		$ngoId = [$ngoData[$currentUserId]];	
		$query = \Drupal::database()->select('node_field_data', 'node_field_data')->fields('node_field_data');
		$query->leftJoin('node__field_silai_school', 'node__field_silai_school', 'node_field_data.nid = node__field_silai_school.entity_id'); 
		$query->leftJoin('node_field_data', 'node_field_data_node__field_silai_school', 'node__field_silai_school.field_silai_school_target_id = node_field_data_node__field_silai_school.nid'); 
		$query->leftJoin('node__field_name_of_ngo', 'node_field_data_node__field_silai_school__node__field_name_of_ngo', 'node_field_data_node__field_silai_school.nid = node_field_data_node__field_silai_school__node__field_name_of_ngo.entity_id');

		$query->leftJoin('node_field_data', 'node_field_data_node__field_name_of_ngo', 'node_field_data_node__field_silai_school__node__field_name_of_ngo.field_name_of_ngo_target_id = node_field_data_node__field_name_of_ngo.nid');
		$query->leftJoin('node__field_silai_district', 'node_field_data_node__field_silai_school__node__field_silai_district', 'node_field_data_node__field_silai_school.nid = node_field_data_node__field_silai_school__node__field_silai_district.entity_id');
		$query->leftJoin('node_field_data', 'node_field_data_node__field_silai_district', 'node_field_data_node__field_silai_school__node__field_silai_district.field_silai_district_target_id = node_field_data_node__field_silai_district.nid');
		$query->leftJoin('node__field_silai_business_state', 'node_field_data_node__field_silai_school__node__field_silai_business_state', 'node_field_data_node__field_silai_school.nid = node_field_data_node__field_silai_school__node__field_silai_business_state.entity_id');
		$query->leftJoin('node_field_data', 'node_field_data_node__field_silai_business_state', 'node_field_data_node__field_silai_school__node__field_silai_business_state.field_silai_business_state_target_id = node_field_data_node__field_silai_business_state.nid');
		$query->leftJoin('node__field_silai_location', 'node_field_data_node__field_silai_school__node__field_silai_location', 'node_field_data_node__field_silai_school.nid = node_field_data_node__field_silai_school__node__field_silai_location.entity_id');
		$query->leftJoin('node_field_data', 'node_field_data_node__field_silai_location', 'node_field_data_node__field_silai_school__node__field_silai_location.field_silai_location_target_id = node_field_data_node__field_silai_location.nid');
		$query->condition('node_field_data.type', 'silai_learners_manage');
		$query->condition('node_field_data_node__field_name_of_ngo.nid', $ngoId, 'IN');

		$results = $query->execute()->fetchAll();
       		return $results;		
	}




}
