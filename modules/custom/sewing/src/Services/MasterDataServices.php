<?php
/**
 * @file providing the service that for master Data.
*/

namespace  Drupal\sewing\Services;
use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

class MasterDataServices {
	public function __construct() {
		
	}

   /**
    * Get State By Location
    * @params: $locationId as integer
    * @return: $stateList as an array
   */
	public function getStatesByLocationId($locationId) {
		$query =\Drupal::entityQuery('node')
			->condition('type', 'manage_business_states')
			->condition(STATUS, 1);
		if($locationId) {
			$query->condition('field_location', $locationId);
		}
		$ids = $query->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);

		foreach($nodes as $node) {
			$stateList[$node->id()] = $node->getTitle();
		}
		return $stateList;
	}   

   /**
    * Get Town By State
    * @params: $stateId as integer
    * @return: $townList as an array
   */
	public function getTownByStateId($stateId) {
		$query =\Drupal::entityQuery('node')
			->condition('type', 'manage_towns')
			->condition(STATUS, 1);
		if($stateId) {
			$query->condition('field_business_state', $stateId);
		}
		$ids = $query->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);

		foreach($nodes as $node) {
			$townList[$node->id()] = $node->getTitle();
		}
		return $townList;
	}   

   /**
    * Get Town By Location
    * @params: $locationId as integer
    * @return: $townList as an array
   */
	public function getTownBylocationId($locationId) {
		if(!is_array($locationId)) {
			$locationId = [$locationId];
		}
		$query =\Drupal::entityQuery('node')
			->condition('type', 'manage_towns')
			->condition(STATUS, 1);
		if($locationId) {
			$query->condition('field_location', $locationId, 'IN');
		}
		$ids = $query->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);

		foreach($nodes as $node) {
			$townList[$node->id()] = $node->getTitle();
		}
		return $townList;
	}

   /**
    * Get School By Location
    * @params: $locationId as integer
    * @return: $schoolList as an array
   */
	public function getSchoolBylocationId($locationId, $stateId ='', $townId = '') {
		if(!is_array($locationId)) {
			$locationId = [$locationId];
		}

		$query =\Drupal::entityQuery('node')
			->condition('type', 'sewing_school')
			->condition(STATUS, 1)->condition('field_sew_school_approval_status', 1);
		if($locationId) {
			$query->condition('field_location', $locationId, 'IN');
		}
		if($stateId) {
			$townIdArr = array_keys($this->getTownByStateId($stateId));
			$query->condition('field_town_city', $townIdArr, 'IN');
		}
		if($townId) {
			$query->condition('field_town_city', $townId);
		}
		$ids = $query->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);

		foreach($nodes as $node) {
			$schoolList[$node->id()] = $node->field_sewing_school_code->value;
		}
		return $schoolList;
	}


	public function getSchoolListBylocationId($locationId, $stateId ='', $townId = '') {
		if(!is_array($locationId)) {
			$locationId = [$locationId];
		}

		$query =\Drupal::entityQuery('node')
			->condition('type', 'sewing_school')
			->condition(STATUS, 1)->condition('field_sew_school_approval_status', 1);
		if($locationId) {
			$query->condition('field_location', $locationId, 'IN');
		}
		if($stateId) {
			$townIdArr = array_keys($this->getTownByStateId($stateId));
			$query->condition('field_town_city', $townIdArr, 'IN');
		}
		if($townId) {
			$query->condition('field_town_city', $townId);
		}
		$ids = $query->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);

		foreach($nodes as $node) {
			$schoolList[$node->id()] = $node->title->value.' ('.$node->field_sewing_school_code->value.')';
		}
		return $schoolList;
	}
	
  /**
    * Get School
    * @params: 
    * @return: $schoolList as an array
   */
	public function getSchool() {
		$query =\Drupal::entityQuery('node')
			->condition('type', 'sewing_school')
			->condition(STATUS, 1)->condition('field_sew_school_approval_status', 1);
		
		$ids = $query->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);

		foreach($nodes as $node) {
			$schoolList[$node->id()] = $node->field_sewing_school_code->value;
		}
		return $schoolList;
	}

	/**
    * getSewing Student By School Code
    * @params: $locationId as integer
    * @return: $stateList as an array
   */
	public function getStudentBySchoolCode($schoolCode, $feeId='') {
		$form = array();
		if (isset($feeId) && $feeId != '') {
			$conn = Database::getConnection();
			$query = $conn->select('usha_student_fee_receipt', 's')
						->condition('generate_fee_id', $feeId)
						->fields('s');
			$records = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
			foreach($records as $record){
				$ids[$record->student_id] = $record->student_id;
			}
		}else{
			$query =\Drupal::entityQuery('node')
			->condition('type', 'manage_sewing_students')
			->condition('field_student_status', 1)
			->condition('field_sewing_school_code_list', $schoolCode)
			->condition('field_sewing_course_fee_out', 0, '>')
			->condition('field_student_admission_date', '2017-04-01', '>=')
			->sort('field_student_admission_date' , 'DESC')
			->sort('field_student_admission_no' , 'DESC');
			$ids = $query->execute();
		}
		/* $query =\Drupal::entityQuery('node')
        ->condition('type', 'manage_sewing_students')
        ->condition('field_student_status', 1)
        ->condition('field_sewing_school_code_list', $schoolCode)
        ->condition('field_sewing_course_fee_out', 0, '>')
        ->sort('field_student_admission_date' , 'DESC')
        ->sort('field_student_admission_no' , 'DESC');
		$ids = $query->execute(); */
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		$paymentToUIL = $this->getGradeMasterData($schoolCode);
		$paymentToUILP = $paymentToUIL['field_payable_to_uil'];
		
		$i = 0;
		if(!empty($nodes)) {
	        foreach($nodes as $node) {
	        	$courseCode = $node->get('field_sewing_course_code_list')->target_id;
	        	$courseMaster = \Drupal\node\Entity\Node::load($courseCode);
	        	//$courseFee  = !empty($courseMaster->get('field_course_fee')->getValue()[0]['value'])? $courseMaster->get('field_course_fee')->getValue()[0]['value']: 0;
	        	$courseName  = $courseMaster->getTitle();
				$courseDuration  = Node::load($courseMaster->field_course_duration->target_id)->field_duration->value;
	        	$courseFee  = $node->field_sewing_course_fee->value;
	        	$payableUILFee  = $node->field_sewing_course_fee_due->value;
	        	$recivedFee = $node->get('field_sewing_course_fee_received')->getValue()[0]['value'];
	        	$balanceFee = $payableUILFee - $recivedFee;
				//$newBalanceFee = ($courseFee * ($paymentToUILP/100)) - $recivedFee;
				//if($balanceFee > 0){
					if($balanceFee <= 0) {
						$disabled = 'disabled';
					} else {
						$disabled = '';
					}
					if (isset($feeId) && $feeId != '') {
						$conn = Database::getConnection();
						$query = $conn->select('usha_student_fee_receipt', 's')
							->condition('generate_fee_id', $feeId)
							->condition('student_id', $node->id())
							->fields('s');
						$record = $query->execute()->fetchAssoc();
						$feeReceived = (!empty($record['received_fee']) && $feeId) ? $record['received_fee']:'';
						$paytoUIL = (!empty($record['payment_to_uil']) && $feeId) ? $record['payment_to_uil']:'';
						$disabled = 'disabled';
					} else {
						$feeReceived = '';
						$paytoUIL = '';
					}
					$studentName = $node->field_student_salutation->value.' '.$node->getTitle().' '.$node->field_last_name->value;
					$form[$i]['Sr No.'] = $i+1;
					$form[$i]['Admission Number'] = $node->get('field_student_admission_no')->getValue()[0]['value'];
					$form[$i]['Student Name'] = $studentName;
					$form[$i]['Course Name & Duration'] = $courseName.' ('.$courseDuration.' Month)';
					$form[$i]['Course Fee'] = $courseFee;
					$form[$i]['payable To UIL Fee'] = $payableUILFee;
					$form[$i]['Balance Fee'] = $balanceFee;
					$form[$i]['Recived Fee'] = ($recivedFee) ? $recivedFee : 0;
					//$form[$i]['Balance Fee'] = $newBalanceFee;
					$form[$i]['Payment To UIL'] = '<input type="text" value="'.$feeReceived.'" name ="received_fee['.$node->id().']" id ="received_fee_'.$node->id().'" class="only-numeric-value fee-received-class" data-b-fee="'.$balanceFee.'" data-uil-per="'.$paymentToUILP.'" data-col="'.$node->id().'" '.$disabled.' style="width: 115px;"><input type="hidden" name= "student_id['.$node->id().']" value="'.$node->id().'">';
					//$form[$i]['Payment To UIL1'] = '<input type="text" value="'.$paytoUIL.'" name ="payment_to_uil['.$node->id().']" class="only-numeric-value payment-to-uil-class" id ="payment_to_uil_'.$node->id().'" readonly="readonly" data-col="'.$node->id().'">';
					$form[$i]['Payment to UIL %'] = $paymentToUILP;
					$i++;
				//}
	        }
	    } else {
				$form[0]['Sr No.'] = 'Student not found';
				$form[0]['Admission Number'] = '';
				$form[0]['Student Name'] = '';
				$form[0]['Course Fee'] = '';
				$form[0]['Balance Fee'] = '';
				$form[0]['Fee Received'] = '';
				$form[0]['Payment To UIL'] = '';
				$form[0]['Payment to UIL %'] = '';
	    }    
		return $form;
	}

	/**
    * getSewing School By Town Code
    * @params: $townId as integer
    * @return: $schoolList as an array
   */
	public function getSchoolByTownId($townId) {
		$query =\Drupal::entityQuery('node')
			->condition('type', 'sewing_school')
			->condition(STATUS, 1);
		if($townId) {
			$query->condition('field_town_city', $townId);
		}
		$ids = $query->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);

		foreach($nodes as $node) {
			$schoolList[$node->id()] = $node->getTitle();
		}
		return $schoolList;
	}


	/**
    * getSewing School By User ID
    * @params: $userId as integer
    * @return: $schoolList as an array
   */
	public function getSchoolIdByUserId($userId) {
		if($userId) {
			$query =\Drupal::entityQuery('node')
				->condition('type', 'sewing_school')
				->condition(STATUS, 1)
				->condition('field_sewing_user_id', $userId);
			$ids = $query->execute();
			$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);

			foreach($nodes as $node) {
				$schoolId = $node->id();
			}
			return ($schoolId) ? $schoolId : 0;
		} else {
			return 0;
		}
	}

	/**
    * getSewing School detail By school ID
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

	/**
    * getSewing Course detail By course ID
    * @params: $userId as integer
    * @return: $schoolList as an array
   */
	public function getCourseDetailById($courseId) {
		if($courseId) {
			$node_storage = \Drupal::entityManager()->getStorage('node');
			$courseData = $node_storage->load($courseId);

			return $courseData;
		}
		
	}

	/**
	 * marking school approved with user approval
	 * @param  [type] $schoolID integer
	 * @return [type]           Mix
	 */
	public function approveSchool($schoolID, $data) {
		//load school
		$node = \Drupal\node\Entity\Node::load($schoolID);
		$userId = $node->field_sewing_user_id->target_id;
		//print_r($userId);
		//die('hello');
		//$stateId = $node->field_silai_business_state->target_id;
		$locationId = $node->field_location->target_id;
		$schoolCreatedBy = $node->uid->target_id;
		$schoolCode = $node->field_sewing_school_code->value;
		$currentUser = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
	    $currentUserRoles = $currentUser->getRoles();
	    $currentUserid = $currentUser->id();
	    //load Location
		$nodeLocation = \Drupal\node\Entity\Node::load($locationId);
		$locationCode = $nodeLocation->field_location_code->value;
		// generate School Code
		$prefix = strtoupper($locationCode);
		//print_r($prefix);
		$prevSchoolCode = $this->generate_code($schoolID, 'sewing_school', 'field_sewing_school_code', $prefix);
		if($prevSchoolCode) {
			$prevSeqNo = (int) substr($prevSchoolCode, -3);
			$seqNo = str_pad( $prevSeqNo + 1, 3, "0", STR_PAD_LEFT );
		} else {
			$seqNo = '001';
		}
		
		$schoolCode = strtoupper($locationCode).$seqNo;
		//print_r($schoolCode);
		//die('--hello');
		$userData = [];
		$userData = $this->getUsersByUsername($schoolCode);
		$username = strtoupper($locationCode).$seqNo;
		//approve school and update school code
	    //$node->set(FIELD_SIL_SCHOOL_APPROVAL_STATUS, APPROVED_STATUS);
	    $node->set('field_sew_school_approval_status', $data['remarks']);
	    $date = date('Y-m-d');
        $node->set('field_school_approval_date', $date );
	    $node->set('field_sewing_school_code', $schoolCode);
	    $node->setNewRevision(FALSE);    
	    $node->save();
	    //approve user
	    $user = \Drupal\user\Entity\User::load($userId);
	    //print_r($userId);
	   // die();
	   	if(empty($userData)) {
	    	$user->setUsername($username);
	    	$user->setPassword($username);
	    }
	    $user->set('status', 1);
	    $user->save();

	    $user = \Drupal\user\Entity\User::load($schoolCreatedBy);
      	$userRoles = $user->getRoles(); 
      	$targetRoles = [$userRoles[1]];
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
	      	$this->sewingNotificationAlert($data, $targetUsers);
		} 
	    return true;

	}	
	/**
	 * [function to generate dynamic codes]
	 * @param  [int] $nid  
	 * @param  [string] $type 
	 * @return [string]       
	 */
	public function generate_code($nid, $type, $field = 'field_sewing_school_code', $code='') {
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
	 * [function for get Revenue Head]
	 * @param  
	 * @param  
	 * @return [string]       
	 */
	public function getRevenueHead($revenueType = '') {
		$query =\Drupal::entityQuery('node')
			->condition('type', 'revenue')
			->condition(STATUS, 1);
		if(!empty($revenueType)) {
			$query->condition('nid', $revenueType);
		}	
		$ids = $query->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);

		foreach($nodes as $node) {
			//$key = str_replace(' ', '_', strtolower($node->getTitle()));
			if($node->id() != REVENUE_HEAD_STUDENT_FEE_NID && empty($revenueType)) {
				$revenueList[$node->id()] = $node->getTitle().' <strong>(Tax Applicable '.$node->get('field_revenue_tax_applicable')->getValue()[0]['value'].'%)</strong>';
			} 
			if(!empty($revenueType)) {
				$revenueList[$node->id()] = $node->get('field_revenue_tax_applicable')->getValue()[0]['value'];
			}
		}
		return $revenueList;
	}



	/**
   * getSewing PayableToUIL % By School Code
   * @return string
   */
	public function getGradeMasterData($schoolCode) {
		$schoolData = Node::load($schoolCode);
		$schoolTypeId = $schoolData->field_sewing_school_type->target_id;
		$schoolGradeId = $schoolData->field_sewing_grade->target_id;

		$query =\Drupal::entityQuery('node')
		  ->condition('type', 'grade_master')
		  ->condition(STATUS, 1)
		  ->condition('field_grades_grade', $schoolGradeId)  
		  ->condition('field_school_type', $schoolTypeId);
		$ids = $query->execute();
		$nodes = Node::loadMultiple($ids);
		foreach($nodes as $node) {
		  $gradeData['field_affiliation_fees'] = $node->get('field_affiliation_fees')->getValue()[0]['value'];
		  $gradeData['field_renewal_fees'] = $node->get('field_renewal_fees')->getValue()[0]['value'];
		  $gradeData['field_payable_to_uil'] = $node->get('field_payable_to_uil')->getValue()[0]['value'];
		}
		return $gradeData;
	}


	public function generate_receipt_form_code($schoolID) {
	    $node = \Drupal\node\Entity\Node::load($schoolID);
	    $schoolCode = $node->field_sewing_school_code->value;
	    $currentYear= date("y");
	    $prefix = strtoupper($schoolCode).$currentYear;    
	    $field='receipt_number';
	    $conn = Database::getConnection();
	    $query = $conn->select('usha_generate_fee_receipt', 's')
	     ->condition($field, '%'. db_like($prefix) . '%', 'LIKE')
	     ->fields('s')
	     ->orderBy('id', 'DESC');
	    $school_data = $query->execute()->fetchAssoc();
	    $prevCode=$school_data['receipt_number'];
	    if($prevCode) {
	      $prevSeqNo = (int) substr($prevCode, -4);
	      $seqNo = str_pad( $prevSeqNo + 1, 4, "0", STR_PAD_LEFT );
	    } else {
	      $seqNo = '0001';
	    }    
	     $receiptCode = strtoupper($prefix).$seqNo;
	     //print_r($receiptCode);die();
	     return $receiptCode;
	}

	/**
   * getSewing School Details By School Code
   * @return string
   */
  	public function getSchoolDataBySchoolCode($schoolCode) {
	    $schoolData = Node::load($schoolCode);
	    $masterDataService = \Drupal::service('sewing.master_data');
	    $schoolTypeId = $schoolData->field_sewing_school_type->target_id;
	    $schoolGradeId = $schoolData->field_sewing_grade->target_id;
	    $schooltypeData = Node::load($schoolTypeId);
	    $schooltype = $schooltypeData->getTitle();
	    $gradeData = Node::load($schoolGradeId);
	    $grade = $gradeData->getTitle();
	    $sapCode = $schoolData->field_sewing_sap_code->value;
	    $schoolAdminId = $schoolData->field_sewing_user_id->target_id;
	    $schoolAdminData = User::load($schoolAdminId);
	    $schoolAdmin = $schoolAdminData->getUsername();

	    $query =\Drupal::entityQuery('node')
	        ->condition('type', 'manage_sewing_students')
	        ->condition(STATUS, 1)
	        ->condition('field_sewing_school_code_list', $schoolCode);
	    $ids = $query->execute();
	    $noOfStudents = count($ids);
	    $noOfCourses = $schoolData->field_no_of_courses->value;

	    $gradeData = $masterDataService->getGradeMasterData($schoolCode);
	    $revenueTax= $masterDataService->getRevenueHead($revenueType); 
	    $revenueStudentTax= $masterDataService->getRevenueHead(REVENUE_HEAD_STUDENT_FEE_NID); 
	    
	    $data['schoolType'] = $schooltype;
	    $data['grade'] = $grade;
	    $data['sapCode'] = $sapCode;
	    $data['schoolAdmin'] = $schoolAdmin;
	    $data['noOfStudents'] = $noOfStudents;
	    $data['noOfCourses'] = $noOfCourses;
	    $data['schoolTypeId'] = $schoolTypeId;
	    // if($revenueType == REVENUE_HEAD_AFFILIATION_FEE_NID) {
	    //   $data['affiliationFee'] = $gradeData['field_affiliation_fees']; 
	    // } elseif($revenueType == REVENUE_HEAD_RENEWAL_FEE_NID) {
	    //   $data['renewalFee'] = $gradeData['field_renewal_fees'];
	    // } else {
	    //   $data['revenueFeeType'] = 0;
	    // }
	    $data['revenueType'] = $revenueTax[$revenueType];
	    $data['studentFeeTax'] = $revenueStudentTax[REVENUE_HEAD_STUDENT_FEE_NID];
	    $data['payableUIL'] = $gradeData['field_payable_to_uil'];

	return $data;
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
			case 'manage_sewing_students':
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
	 * Get Location By Country Id
	 * @params: $country_id as Integer
	 * @return: $locationList as an array
	 */
	public function getLocationByCountryId($countryId = NULL) {
		$query = \Drupal::entityQuery('node')
		    ->condition('type', 'manage_locations')
		    ->condition(STATUS, 1);
		if($countryId) {
			$query->condition('field_country', $countryId);
		}    
		$ids = $query->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
			$locationList[$node->id()] = $node->getTitle();
		}
		return $locationList;		
	}
	/**
	 * Get Users by Location
	 * @params: $locationId as integer
	 * @return: $blockList as an array
	*/
	public function getSewingUsersByLocation($locationId, $roles) {
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
	 * Get Items by item group Id
	 * @params: $itemGroupId as integer
	 * @return: $itemList as an array
	*/
	public function getItemsByItemGroup($itemGroupId) {
		$ids = \Drupal::entityQuery('node')
		->condition('type', 'items')
		->condition('field_item_group', $itemGroupId)
		->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
			$itemList[$node->id()] = $node->getTitle();
		}
		return $itemList;
	}

	/**
	 * Get School Admin details by School Code
	 * @params: $schoolCode as integer
	 * @return: $user data as an array
	*/
	public function getTeacherDataBySchoolCode($schoolCode, $editId='') {
		$ids = \Drupal::entityQuery('node');
		$ids->condition('type', 'sewing_teacher_management');
		$ids->condition('field_sewing_school_code_list', $schoolCode);
		$ids->condition('field_sewing_copy_teacher_data', 1);
		$ids->condition('status', 1);
		if(!empty($editId)) {
			$ids->condition('nid', $editId, '!=');
		}	
		$ids = $ids->execute();
		if(empty($ids)) {
			$node = \Drupal\node\Entity\Node::load($schoolCode);
			$user = User::load($node->field_sewing_user_id->target_id);
			$userData['name'] = $user->field_first_name->value;
			$userData['email'] = $user->getEmail();
			$userData['phoneNo'] = $user->field_user_contact_no->value;
		} else {
			$userData['name'] = '';
			$userData['email'] = '';
			$userData['phoneNo'] = '';
			$userData['duplicate'] = 1;
		}
		return $userData;
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
	 * Get Users by Roles
	 * @params: $roles as array
	 * @return: $usersList as an array
	*/
	public function getUsersByRoleSewing($roles = [], $location='') {
		if(!empty($location) && !is_array($location)) {
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
	 * Function to convert a number to a the string literal for the number
	 */
	function numberTowords($num) { 
		$ones = array( 
			1 => "one", 
			2 => "two", 
			3 => "three", 
			4 => "four", 
			5 => "five", 
			6 => "six", 
			7 => "seven", 
			8 => "eight", 
			9 => "nine", 
			10 => "ten", 
			11 => "eleven", 
			12 => "twelve", 
			13 => "thirteen", 
			14 => "fourteen", 
			15 => "fifteen", 
			16 => "sixteen", 
			17 => "seventeen", 
			18 => "eighteen", 
			19 => "nineteen" 
		); 
		$tens = array( 
			1 => "ten",
			2 => "twenty", 
			3 => "thirty", 
			4 => "fourty", 
			5 => "fifty", 
			6 => "sixty", 
			7 => "seventy", 
			8 => "eighty", 
			9 => "ninety" 
		); 
		$hundreds = array( 
			"hundred", 
			"thousand", 
			"million", 
			"billion", 
			"trillion", 
			"quadrillion" 
		); //limit t quadrillion 
		$num = number_format($num,2,".",","); 
		$num_arr = explode(".",$num); 
		$wholenum = $num_arr[0]; 
		$decnum = $num_arr[1]; 
		$whole_arr = array_reverse(explode(",",$wholenum)); 
		krsort($whole_arr); 
		$rettxt = ""; 
		foreach($whole_arr as $key => $i) { 
			if($i < 20){ 
				$rettxt .= $ones[$i]; 
			} elseif($i < 100){ 
				$rettxt .= $tens[substr($i,0,1)]; 
				$rettxt .= " ".$ones[substr($i,1,1)]; 
			}else{ 
				$rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0]; 
				$rettxt .= " ".$tens[substr($i,1,1)]; 
				$rettxt .= " ".$ones[substr($i,2,1)]; 
			} 
			if($key > 0){ 
				$rettxt .= " ".$hundreds[$key]." "; 
			} 
			} 
			if($decnum > 0){ 
				$rettxt .= " and "; 
			if($decnum < 20){ 
				$rettxt .= $ones[$decnum]; 
			}elseif($decnum < 100){ 
				$rettxt .= $tens[substr($decnum,0,1)]; 
				$rettxt .= " ".$ones[substr($decnum,1,1)]; 
			} 
		} 
		return $rettxt; 
	}

	/**
	 * notification message on some points like add school, approve school etc
	 * @param  [Array] $data 
	 * @return [Boolean]   Mix
	 */
	public function sewingNotificationAlert($data, $targetRoles= '') {
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
			if($userRoles[1] == ROLE_SEWING_SSI) {
				$data['location'] = $user->field_user_location->target_id;	
			}
			$query = $database->insert(TABLE_SEWING_CUSTOM_NOTIFICATION)->fields($data)->execute();
		}
	    return true;
	}

	/**
	 * [getRevenueHeadList description]
	 * @return [type] [description]
	 */
	public function getRevenueHeadList() {
		$query =\Drupal::entityQuery('node')
			->condition('type', 'revenue')
			->condition(STATUS, 1);
			
		$ids = $query->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		$revenueList = [];
		foreach($nodes as $node) {
		   $revenueList[$node->id()] = $node->getTitle(); 
		}

		return $revenueList;
	}

	/**
	 * Get Location List
	 * @params: $country_id as Integer
	 * @return: $locationList as an array
	 */
	public function getLocationList($locationIds = [], $countryId = NULL) {
		$query = \Drupal::entityQuery('node')
		    ->condition('type', 'manage_locations')
		    ->condition(STATUS, 1);
		if($locationIds) {
			$query->condition('nid', $locationIds, 'IN');
		}
		if($countryId) {
			$query->condition('field_country', $countryId);
		}     
		$ids = $query->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
			$locationList[$node->id()]['name'] = $node->getTitle();
			$locationList[$node->id()]['code'] = $node->field_location_code->value;
		}
		return $locationList;		
	}

	/**
	 * Get Sewing Courses
	 * @params: $schoolId as Integer
	 * @return: $coursesList as an array
	 */
	public function getSewingCourses($schoolId) {
		$query = \Drupal::entityQuery('node')
		    ->condition('type', 'sewing_school')
		    ->condition(STATUS, 1)
		    ->condition('nid', $schoolId);
		 
		$ids = $query->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
			$gradeId = $node->field_sewing_grade->target_id;
		}
		$query = \Drupal::entityQuery('node')
		    ->condition('type', 'course_master')
		    ->condition(STATUS, 1);
		if($gradeId) {
			$query->condition('field_grade', $gradeId);
		}
		$ids = $query->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
			$coursesList[$node->id()] = $node->field_course_code->value;
		}
		return $coursesList;		
	}
}