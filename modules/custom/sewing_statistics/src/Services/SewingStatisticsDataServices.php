<?php
/**
 * @file providing the service that for master Data.
*/

namespace  Drupal\sewing_statistics\Services;
use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;


use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\TablePosition;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SewingStatisticsDataServices {
	public function __construct() {
		
	}

	public function getSchoolTotalStudents($schoolId) {
		$query =\Drupal::entityQuery('node')
        ->condition('type', 'manage_sewing_students')
        ->condition(STATUS, 1)
        ->condition('field_sewing_school_code_list', $schoolId);
		$ids = $query->execute();
		
		return $ids;
	}


	public function getWActivityData($locationId) {
			$query =\Drupal::entityQuery('node')
	        ->condition('type', 'training')
	        ->condition(STATUS, 1)
	        ->condition('field_training_location', $locationId);
			$ids = $query->execute();
			
			$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);

			$wActivityData[$locationId] = [
				'schoolCount' => [],
				'attendees' => '',
				'numberOfSchools' => '',
				'numberOfSale' => '',
				'prospectusGenSale' => '',
			];
			$attendees = 0;
			$numberOfSchools = 0;
			$numberOfSale = 0;
			$prospectusGenSale = 0;

			foreach($nodes as $node) {
				if($node->field_sewing_school_name->target_id)
					$wActivityData[$locationId]['schoolCount'][] = $node->field_sewing_school_name->target_id;

				if($node->field_no_of_attendees->value) {
					$attendees += $attendees + $node->field_no_of_attendees->value;
					$wActivityData[$locationId]['attendees'] = $attendees;
				}

				if($node->field_training_conf_no_of_sch_ad->value) {
					$numberOfSchools += $numberOfSchools + $node->field_training_conf_no_of_sch_ad->value;
					$wActivityData[$locationId]['numberOfSchools'] = $numberOfSchools;
				}

				if($node->field_training_conf_no_of_sale->value) {
					$numberOfSale += $numberOfSale + $node->field_training_conf_no_of_sale->value;
					$wActivityData[$locationId]['numberOfSale'] = $numberOfSale;
				}

				if($node->field_training_prosp_gen_for_sal->value){
					$prospectusGenSale += $prospectusGenSale + $node->field_training_prosp_gen_for_sal->value;
					$wActivityData[$locationId]['prospectusGenSale'] = $prospectusGenSale;
				}
			}
			return $wActivityData;
			
	}


	public function getSchoolFee($schoolId, $requestData = []) {
		// /$schoolId = 1052;
		$connection = \Drupal::database();
	    $query = $connection->select('usha_generate_fee_receipt', 'ufr');
	    $query->fields('ufr', ['school_id', 'revenue_head_type']);
	    $query->addExpression('SUM(ufr.total_pay_to_uil)', 'fee_value');
	    $query->addExpression('SUM(ufr.tax)', 'tax');
	    $query->addExpression('COUNT(ufr.revenue_head_type)', 'count');
	    $query->condition('ufr.school_id', $schoolId);
	    if($requestData && $requestData['from_date']) {
	        $query->condition('ufr.created_date', strtotime($requestData['from_date']), ">=");
	    }
	    if($requestData && $requestData['to_date']) {
	        $query->condition('ufr.created_date', strtotime($requestData['to_date']), "<=");
	    }
	    $query->groupBy('ufr.revenue_head_type');
		$results = $query->execute()->fetchAll();
		//print_r($results);die;
		$feeDetail = [];
		foreach ($results as $key => $value) {
			$feeDetail[$value->school_id][$value->revenue_head_type] = $value;
			
		}
		
		return $feeDetail;
	}


	public function getSewingSchoolsNodes($requestData = []) {
		// /print_r($requestData);die;
		$query =\Drupal::entityQuery('node')
	      ->condition('type', 'sewing_school')
	      ->condition(STATUS, 1)
	      ->condition('field_sew_school_approval_status', 1);
	      if($requestData && ($requestData['location']) && $requestData['location'] != UNDERSCORE_NONE) {
	        	$query->condition('field_location', $requestData['location']);
	    	}
	      if($requestData && ($requestData['schoolType']) && $requestData['schoolType'] != UNDERSCORE_NONE) {
	        	$query->condition('field_sewing_school_type', $requestData['schoolType']);
	    	}
	      //if($requestData && $requestData['from_date'])
	      //  $query->condition('field_school_creation_date', $requestData['from_date'], ">=");
	     // if($requestData && $requestData['to_date'])
	     //   $query->condition('field_school_creation_date', $requestData['to_date'], "<=");
	      if(!$requestData || ($requestData && !$requestData['action']) || ($requestData && $requestData['page'])) {
	      	$query->pager(10);
	      }
	    $ids = $query->execute();
	   
	    $nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
	    
	    return $nodes;
	}

	public function getWorkshopAndActivity($requestData = []) {
		
		$connection = \Drupal::database();
	    $query = $connection->select('node__field_training_location', 'nftl');
	    $query->fields('nftl', ['field_training_location_target_id']);
	    $query->addExpression('COUNT(nftl.field_training_location_target_id)', 'count');
	    if($requestData && $requestData['location'] && $requestData['location'] != UNDERSCORE_NONE)
	        $query->condition('field_training_location_target_id', $requestData['location']);
	    $query->groupBy('nftl.field_training_location_target_id');
		$results = $query->execute()->fetchAll();
		
		$trainingDetail = [];
		foreach ($results as $key => $value) {
			$trainingDetail[$value->field_training_location_target_id] = $value;
			
		}
		
		return $trainingDetail;
		
	}


	public function createSchoolWiseRevenueExcel($schoolNodes, $requestData) 
	{
		try {

			$schoolNodes = ($schoolNodes) ? $schoolNodes : [];
			$fromDate = ($requestData['from_date']) ? date('d/m/Y', strtotime($requestData['from_date'])) : '';
			$toDate = ($requestData['to_date']) ? date('d/m/Y', strtotime($requestData['to_date'])) : '';

		    $spreadsheet = new Spreadsheet();
		    $activeSheet = $spreadsheet->getActiveSheet();
		    $spreadsheet->getProperties()->setTitle('School wise Revenue Report');
		    $spreadsheet->setActiveSheetIndex(0);
		    $spreadsheet->getActiveSheet()->setCellValue('A1', 'USHA INTERNATIONAL LTD.')
		        ->setCellValue('A2', 'SEWING SCHOOL WISE REVENUE SUMMARY');
		    $spreadsheet->getActiveSheet()->setCellValue('A3', 'FROM :')
		        ->setCellValue('C3', $fromDate)
		        ->setCellValue('D3', 'TO :')
		        ->setCellValue('E3', $toDate);
		    $spreadsheet->getActiveSheet()->setCellValue('A4', 'SCHOOL TYPE : AF AFFILIATED');
		    $spreadsheet->getActiveSheet()->setCellValue('A5', 'RUNDATE :')
		    ->setCellValue('C5', date('d/m/Y'));
		    //$spreadsheet->getActiveSheet()->mergeCells('B1:C1');
		   	//$spreadsheet->getActiveSheet()->mergeCells('A6:Z6');
		   	$spreadsheet->getActiveSheet()->setCellValue('J6', 'AMOUNT RECEIVED DURING THE PERIOD')->setCellValue('M6', 'REVENUE');

		   	//$spreadsheet->getActiveSheet()->mergeCells('A8:Z8');
		   	$spreadsheet->getActiveSheet()->setCellValue('H8', 'PROSPECTUS')
		   	->setCellValue('I8', 'NO OF')
		   	->setCellValue('J8', '----------------------------------------')
		   	//->setCellValue('M8', 'FROM SALE OF')
		   	->setCellValue('O8', '');
		   	//->setCellValue('P8', 'TOTAL')
		   	//->setCellValue('Q8', 'SERVICE')
		   	//->setCellValue('R8', 'NET AMT');

		   	$spreadsheet->getActiveSheet()->setCellValue('A9', 'LOCATION')
		   	->setCellValue('D9', 'SCHOOL TYPE')
		   	->setCellValue('E9', 'NAME OF SCHOOL')
		   	->setCellValue('F9', 'TOWN')
		   	->setCellValue('H9', 'ISSUED')
		   	->setCellValue('I9', 'ADMISSION');

		   	$revenueHeads = $this->getRevenueHeadList();
		   	$column = 'J';
		   	foreach ($revenueHeads as $key => $value) {
		   		$spreadsheet->getActiveSheet()->setCellValue($column.'9', strtoupper($value));
		   		$column++;
		   	}
		   	// $spreadsheet->setCellValue('J9', 'STUDENT FEE')
		   	// ->setCellValue('K9', 'RENEWAL FEE')
		   	// ->setCellValue('L9', 'AFFILIATION FEE')
		   	// ->setCellValue('M9', 'PROSPECTUS')
		   	// ->setCellValue('N9', 'OTHERS FEE')
		   	// ->setCellValue('O9', 'FORM FEE');

		   	$spreadsheet->getActiveSheet()->setCellValue($column.'9', ' TOTAL REVENUE(RS.)')
		   	->setCellValue(++$column.'9', 'SERVICE TAX')
		   	->setCellValue(++$column.'9', 'NET AMT');

		   	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(50);
		    $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(50);
		    //$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(50);
		    //$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(50);
		    
		    if($schoolNodes) {
		    	$row = 10;
		    	foreach($schoolNodes as $node) {  
					$locationData = Node::load($node->field_location->target_id);
					$locationName = $locationData->getTitle();

					$townData = Node::load($node->field_town_city->target_id);
					$townName = $townData->getTitle();

					//$masterDataService = \Drupal::service('sewing_statistics.master_data');
					$admissionCounts =  $this->getSchoolTotalStudents($node->id());
					$schoolFee =  $this->getSchoolFee($node->id(), $requestData);
					$schoolTypeData = Node::load($node->field_sewing_school_type->target_id);
      				$schoolType = $schoolTypeData->getTitle();
					// $totalRevenue = ceil($schoolFee[$node->id()][REVENUE_HEAD_STUDENT_FEE_NID]->fee_value + $schoolFee[$node->id()][REVENUE_HEAD_RENEWAL_FEE_NID]->fee_value + $schoolFee[$node->id()][REVENUE_HEAD_AFFILIATION_FEE_NID]->fee_value + $schoolFee[$node->id()][REVENUE_HEAD_PROSPECTUS_FEE_NID]->fee_value);

					$totalTax = ceil($schoolFee[$node->id()][REVENUE_HEAD_STUDENT_FEE_NID]->tax + $schoolFee[$node->id()][REVENUE_HEAD_RENEWAL_FEE_NID]->tax + $schoolFee[$node->id()][REVENUE_HEAD_AFFILIATION_FEE_NID]->tax + $schoolFee[$node->id()][REVENUE_HEAD_PROSPECTUS_FEE_NID]->tax);

					

					$spreadsheet->getActiveSheet()->setCellValue('A'.$row, $locationName);
					$spreadsheet->getActiveSheet()->setCellValue('B'.$row, $node->field_sewing_school_code->value);
					$spreadsheet->getActiveSheet()->setCellValue('D'.$row, $schoolType);
					$spreadsheet->getActiveSheet()->setCellValue('E'.$row, $node->getTitle());
					$spreadsheet->getActiveSheet()->setCellValue('F'.$row, $townName);
					$spreadsheet->getActiveSheet()->setCellValue('H'.$row, '');
					$spreadsheet->getActiveSheet()->setCellValue('I'.$row, count($admissionCounts));

					$column = 'J';
					$totalRevenue = 0;
					$totalTax = 0;
					foreach ($revenueHeads as $key => $value) {

						if($key == REVENUE_HEAD_STUDENT_FEE_NID) {
			              $schoolStudentFee = $this->getSchoolStudentTypeFee($node->id(), $key, $requestData);

			              $feeValue = ($schoolStudentFee[$node->id()][$key]->total_student_fee) ? $schoolStudentFee[$node->id()][$key]->total_student_fee : 0;
							$spreadsheet->getActiveSheet()->setCellValue($column.$row, $feeValue);

							$totalRevenue = ceil($totalRevenue + $schoolStudentFee[$node->id()][$key]->total_student_fee);

							$totalTax = ceil( $totalTax + $schoolStudentFee[$node->id()][$key]->tax );
			            } else {
				           $feeValue = ($schoolFee[$node->id()][$key]->fee_value) ? $schoolFee[$node->id()][$key]->fee_value : 0;
							$spreadsheet->getActiveSheet()->setCellValue($column.$row, $feeValue);

							$totalRevenue = ceil($totalRevenue + $schoolFee[$node->id()][$key]->fee_value);

							$totalTax = ceil( $totalTax + $schoolFee[$node->id()][$key]->tax );
				        }

						$column++;
					}

					// $spreadsheet->getActiveSheet()->setCellValue('J'.$row, $schoolFee[$node->id()][REVENUE_HEAD_STUDENT_FEE_NID]->fee_value);
					// $spreadsheet->getActiveSheet()->setCellValue('K'.$row, $schoolFee[$node->id()][REVENUE_HEAD_RENEWAL_FEE_NID]->fee_value);
					// $spreadsheet->getActiveSheet()->setCellValue('L'.$row, $schoolFee[$node->id()][REVENUE_HEAD_AFFILIATION_FEE_NID]->fee_value);
					// $spreadsheet->getActiveSheet()->setCellValue('M'.$row, $schoolFee[$node->id()][REVENUE_HEAD_PROSPECTUS_FEE_NID]->fee_value);
					// $spreadsheet->getActiveSheet()->setCellValue('N'.$row, '');
					// $spreadsheet->getActiveSheet()->setCellValue('O'.$row, '');
					$spreadsheet->getActiveSheet()->setCellValue($column.$row, $totalRevenue);
					$spreadsheet->getActiveSheet()->setCellValue(++$column.$row, $totalTax);
					$spreadsheet->getActiveSheet()->setCellValue(++$column.$row, ceil($totalRevenue - $totalTax));
					//$column = 'A';
					// create php excel object
					// foreach ($fieldArr as $key => $value) {
					// 	$spreadsheet->getActiveSheet()->setCellValue($column.$row, $value);
					// 	$column++;
					// }

					$row++;	
	        	}
	        	$column1 = 'J';
	        	foreach ($revenueHeads as $key => $value) {
	        		$column1++;
	        	}

	        	$styleArray = [
				    'font' => [
				        'bold' => true,
				    ]
				];

	        	$spreadsheet->getActiveSheet()->setCellValue('A'.$row, 'Total Result');
	        	$spreadsheet->getActiveSheet()->getStyle('A'.$row)->applyFromArray($styleArray);
	        	$spreadsheet->getActiveSheet()->setCellValueExplicit($column1.$row,'=SUM('.$column1.'10:'.$column1.($row-1).')',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	        	$spreadsheet->getActiveSheet()->setCellValue($column1.$row, $spreadsheet->getActiveSheet()->getCell($column1.$row)->getCalculatedValue());
	        	$spreadsheet->getActiveSheet()->getStyle($column1.$row)->applyFromArray($styleArray);

	        	$spreadsheet->getActiveSheet()->setCellValueExplicit(++$column1.$row,'=SUM('.$column1.'10:'.$column1.($row-1).')', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	        	$spreadsheet->getActiveSheet()->setCellValue($column1.$row, $spreadsheet->getActiveSheet()->getCell($column1.$row)->getCalculatedValue());
	        	$spreadsheet->getActiveSheet()->getStyle($column1.$row)->applyFromArray($styleArray);

	        	$spreadsheet->getActiveSheet()->setCellValueExplicit(++$column1.$row,'=SUM('.$column1.'10:'.$column1.($row-1).')', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	        	$spreadsheet->getActiveSheet()->setCellValue($column1.$row, $spreadsheet->getActiveSheet()->getCell($column1.$row)->getCalculatedValue());
	        	$spreadsheet->getActiveSheet()->getStyle($column1.$row)->applyFromArray($styleArray);
	        	
	        	
	    	}

		    $filename = "schoolRevenueReport";
			header("Content-Type: application/xls");    
			header("Content-Disposition: attachment; filename=$filename.xls");  
			header("Pragma: no-cache"); 
			header("Expires: 0");
			$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
			$writer->save("php://output");
			
	    } catch(Exception $e) {

		}
	}

	public function createLocationWiseWActivityExcel($workShopActivityNodes, $requestData) 
	{
		try {

			$workShopActivityNodes = ($workShopActivityNodes) ? $workShopActivityNodes : [];

			$fromDate = ($requestData['from_date']) ? date('d/m/Y', strtotime($requestData['from_date'])) : '';
			$toDate = ($requestData['to_date']) ? date('d/m/Y', strtotime($requestData['to_date'])) : '';

		    $spreadsheet = new Spreadsheet();
		    $activeSheet = $spreadsheet->getActiveSheet();
		    $spreadsheet->getProperties()->setTitle('School wise Revenue Report');
		    $spreadsheet->setActiveSheetIndex(0);

		   	//$spreadsheet->getActiveSheet()->mergeCells('A8:Z8');
		   	$spreadsheet->getActiveSheet()->setCellValue('A1', 'LOCATION')
		   	->setCellValue('B1', 'Count - SCHOOL CODE')
		   	->setCellValue('C1', 'Sum - FOOTFALL')
		   	->setCellValue('D1', 'Sum - CONFIRMED NO.OF SCHOOL ADMISSION')
		   	->setCellValue('E1', 'Sum - CONFIRMED NO.OF SALE')
		   	->setCellValue('F1', 'Sum - Prospects generated for Sale');
		   	

		   	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(50);
		    $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(50);
		    //$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(50);
		    //$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(50);
		    
		    if($workShopActivityNodes) {
		    	$row = 2;
		    	foreach($workShopActivityNodes as $key => $node) {  
					$locationId = $key;
					$locationData = Node::load($locationId);
					$locationName = $locationData->getTitle();

					$wActivityData =  $this->getWActivityData($locationId);

					$schoolCount = Count($wActivityData[$locationId]['schoolCount']);
					$attendees = ($wActivityData[$locationId]['attendees']) ? $wActivityData[$locationId]['attendees'] : 0;
					$numberOfSchools = ($wActivityData[$locationId]['numberOfSchools']) ? $wActivityData[$locationId]['numberOfSchools'] : 0;
					$numberOfSale = ($wActivityData[$locationId]['numberOfSale']) ? $wActivityData[$locationId]['numberOfSale'] : 0;
					$prospectusGenSale = ($wActivityData[$locationId]['prospectusGenSale']) ? $wActivityData[$locationId]['prospectusGenSale'] : 0;


					$spreadsheet->getActiveSheet()->setCellValue('A'.$row, $locationName);
					$spreadsheet->getActiveSheet()->setCellValue('B'.$row, $schoolCount);
					$spreadsheet->getActiveSheet()->setCellValue('C'.$row, $attendees);
					$spreadsheet->getActiveSheet()->setCellValue('D'.$row, $numberOfSchools);
					$spreadsheet->getActiveSheet()->setCellValue('E'.$row, $numberOfSale);
					$spreadsheet->getActiveSheet()->setCellValue('F'.$row, $prospectusGenSale);
					//$column = 'A';
					// create php excel object
					// foreach ($fieldArr as $key => $value) {
					// 	$spreadsheet->getActiveSheet()->setCellValue($column.$row, $value);
					// 	$column++;
					// }

					$row++;	
	        	}
	        	$styleArray = [
				    'font' => [
				        'bold' => true,
				    ]
				];

	        	$spreadsheet->getActiveSheet()->setCellValue('A'.$row, 'Total Result');
	        	$spreadsheet->getActiveSheet()->setCellValueExplicit('B'.$row,'=SUM(B2:B'.($row-1).')',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	        	$spreadsheet->getActiveSheet()->setCellValueExplicit('C'.$row,'=SUM(C2:C'.($row-1).')', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	        	$spreadsheet->getActiveSheet()->setCellValueExplicit('D'.$row,'=SUM(D2:D'.($row-1).')', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	        	$spreadsheet->getActiveSheet()->setCellValueExplicit('E'.$row,'=SUM(E2:E'.($row-1).')', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	        	$spreadsheet->getActiveSheet()->setCellValueExplicit('F'.$row,'=SUM(F2:F'.($row-1).')', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

	        	$spreadsheet->getActiveSheet()->setCellValue('B'.$row, $spreadsheet->getActiveSheet()->getCell('B'.$row)->getCalculatedValue());
	        	$spreadsheet->getActiveSheet()->setCellValue('C'.$row, $spreadsheet->getActiveSheet()->getCell('C'.$row)->getCalculatedValue());
	        	$spreadsheet->getActiveSheet()->setCellValue('D'.$row, $spreadsheet->getActiveSheet()->getCell('D'.$row)->getCalculatedValue());
	        	$spreadsheet->getActiveSheet()->setCellValue('E'.$row, $spreadsheet->getActiveSheet()->getCell('E'.$row)->getCalculatedValue());
	        	$spreadsheet->getActiveSheet()->setCellValue('F'.$row, $spreadsheet->getActiveSheet()->getCell('F'.$row)->getCalculatedValue());

	        	$spreadsheet->getActiveSheet()->getStyle('A'.$row)->applyFromArray($styleArray);
	        	$spreadsheet->getActiveSheet()->getStyle('B'.$row)->applyFromArray($styleArray);
	        	$spreadsheet->getActiveSheet()->getStyle('C'.$row)->applyFromArray($styleArray);
	        	$spreadsheet->getActiveSheet()->getStyle('D'.$row)->applyFromArray($styleArray);
	        	$spreadsheet->getActiveSheet()->getStyle('E'.$row)->applyFromArray($styleArray);
	        	$spreadsheet->getActiveSheet()->getStyle('F'.$row)->applyFromArray($styleArray);
	    	}



		    $filename = "workshopActivityReport";
			header("Content-Type: application/xls");    
			header("Content-Disposition: attachment; filename=$filename.xls");  
			header("Pragma: no-cache"); 
			header("Expires: 0");
			$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
			$writer->save("php://output");
			
	    } catch(Exception $e) {

		}
	}

	/**
	 * get Revenue Head List
	 * @return Array 
	 */
	public function getRevenueHeadList() {
		$query =\Drupal::entityQuery('node')
			->condition('type', 'revenue');
			//->condition(STATUS, 1);
			
		$ids = $query->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		$revenueList = [];
		foreach($nodes as $node) {
		   $revenueList[$node->id()] = $node->getTitle(); 
		}

		return $revenueList;
	}


	/**
	 * Get School Type List
	 * @return: $schoolTypeList as an array
	*/
	public function getSchoolTypeList($requestData = []) {
		$query = \Drupal::entityQuery('node')
		->condition('type', 'school_type_master')
		->condition(STATUS, 1);
		//if($requestData && $requestData['from_date']) {
	    //    $query->condition('created', strtotime($requestData['from_date']), ">=");
	    //}
	    //if($requestData && $requestData['to_date']) {
	    //    $query->condition('created', strtotime($requestData['to_date']), "<=");
	    //}
		$ids = $query->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
			$schoolTypeList[$node->id()] = $node->getTitle();
		}
		return $schoolTypeList;
	}


	/**
	 * Get Location wise school type count
	 * @params: $locationId as integer
	 * @params: $schoolType as integer
	 * @return: $ids as integer
	*/
	public function getLocationWiseSchoolTypeCount($locationId, $schoolType) {
		if($locationId && $schoolType) {
			$query = \Drupal::entityQuery('node')
			    ->condition('type', 'sewing_school')
			    ->condition(STATUS, 1)
		        ->condition('field_sew_school_approval_status', 1)
		        ->condition('field_location', $locationId)
		        ->condition('field_sewing_school_type', $schoolType);
			   
			$ids = $query->count()->execute();
			return $ids;
		}
	
	} 

	/**
	 * get total fee for respective revenue heads for a perticular school type in a location 
	 * @param  int $feeType    
	 * @param  int $schoolType 
	 * @param  int $locationId 
	 * @return array             
	 */
	public function getReceivedFee($feeType, $schoolType, $locationId, $requestData = []) {
		
		$connection = \Drupal::database();
	    $query = $connection->select('usha_generate_fee_receipt', 'ufr');
	    $query->addJoin('INNER', 'node__field_sewing_school_type', 'sst', 'ufr.school_id = sst.entity_id');
	    $query->addJoin('INNER', 'node__field_location', 'nfl', 'sst.entity_id = nfl.entity_id AND nfl.bundle = :sewingSchool', [':sewingSchool' => 'sewing_school']);
	    $query->fields('ufr', ['revenue_head_type']);
	    $query->addExpression('SUM(ufr.total_pay_to_uil)', 'fee_value');
	    $query->addExpression('SUM(ufr.tax)', 'tax');
	    //$query->addExpression('COUNT(ufr.revenue_head_type)', 'count');
	    $query->condition('sst.field_sewing_school_type_target_id', $schoolType);
	    $query->condition('nfl.field_location_target_id', $locationId);
	    $query->condition('ufr.revenue_head_type', $feeType);
	    if($requestData && $requestData['from_date']) {
	        $query->condition('ufr.created_date', strtotime($requestData['from_date']), ">=");
	    }
	    if($requestData && $requestData['to_date']) {
	        $query->condition('ufr.created_date', strtotime($requestData['to_date']), "<=");
	    }
	    //$query->groupBy('ufr.revenue_head_type');
		$results = $query->execute()->fetchAll();
		
		$feeDetail = [];
		foreach ($results as $key => $value) {
			$feeDetail[$feeType][$schoolType] = $value;
			
		}
		
		return $feeDetail;
	}

	/**
	 * Get no. of enroled student
	 * @param  int $schoolType 
	 * @param  int $locationId 
	 * @return int             
	 */
	public function getSchoolTypeWiseEnrolements($schoolType, $locationId) {
		$connection = \Drupal::database();
	    $query = $connection->select('node__field_sewing_school_code_list', 'sscl');
	    $query->fields('sscl');
	    $query->addJoin('INNER', 'node__field_sewing_school_type', 'sst', 'sscl.field_sewing_school_code_list_target_id = sst.entity_id');
	    $query->addJoin('INNER', 'node__field_location', 'nfl', 'sst.entity_id = nfl.entity_id AND nfl.bundle = :sewingSchool', [":sewingSchool" => "sewing_school"]);
	    //$query->addExpression('COUNT(sscl.entity_id)', 'count');
	    $query->condition('sst.field_sewing_school_type_target_id', $schoolType);
	    $query->condition('nfl.field_location_target_id', $locationId);
	    $query->condition('sscl.bundle', 'manage_sewing_students');
	    //$query->groupBy('sscl.field_sewing_school_code_list_target_id');
	    $rowCounts = $query->countQuery()->execute()->fetchField();
		
		return $rowCounts;
	}

	public function createLSTWiseReport($LocationIds, $revenueHeads, $schoolTypeList, $requestData) 
	{
		try {

			$LocationIds = ($LocationIds) ? $LocationIds : [];
			$fromDate = ($requestData['from_date']) ? date('d/m/Y', strtotime($requestData['from_date'])) : '';
			$toDate = ($requestData['to_date']) ? date('d/m/Y', strtotime($requestData['to_date'])) : '';

		    $spreadsheet = new Spreadsheet();
		    $activeSheet = $spreadsheet->getActiveSheet();
		    $spreadsheet->getProperties()->setTitle('Sewing School MIS Report');
		    $spreadsheet->setActiveSheetIndex(0);
		    $spreadsheet->getActiveSheet()->setCellValue('A1', 'USHA INTERNATIONAL LTD.');
		    $spreadsheet->getActiveSheet()->mergeCells('A2:K2');
		    $spreadsheet->getActiveSheet()->setCellValue('A2', 'LOCATIONWISE REVENUE SUMMARY FOR THE PERIOD FROM : '.$fromDate.' TO '.$toDate);
		   
		    $spreadsheet->getActiveSheet()->setCellValue('A3', 'RUNDATE :')
		    ->setCellValue('B3', date('d/m/Y'));
		    
		    # 6TH row start
		    if($schoolTypeList && $revenueHeads) {
		    $column = 'C';
		    foreach ($schoolTypeList as $key => $value) {
		    	$column++;
		    }
		   	$j= 0;
		   	$count = count($revenueHeads);
		    foreach ($revenueHeads as $key => $value) {
		    	if ($j == 0) {
		    			$startColumn = $column;
		    		}  else {
		    		$startColumn = ++$column;	
		    		}
		    		$j++;
		    	
		    	$i = 0;
				$len = count($schoolTypeList);
		    	foreach ($schoolTypeList as $key1 => $value1) {
		    		if ($i != $len - 1) {
		    			$column++;
		    		} 
		    		$i++;
		    	}
		    	
		    	$spreadsheet->getActiveSheet()->mergeCells($startColumn.'6:'.$column.'6');
		    	$spreadsheet->getActiveSheet()->setCellValue($startColumn.'6', strtoupper('TOTAL '.$value.' RECEIVED'));
		    	#styling
		    	$spreadsheet->getActiveSheet()->getStyle($startColumn.'6:'.$column.'6')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
		    	$spreadsheet->getActiveSheet()->getStyle($startColumn.'6:'.$column.'6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		        
		    }
		    
		    ++$column;++$column;++$column;++$column;++$column;

	    	$startColumn = $column;
	    	foreach ($schoolTypeList as $key1 => $value1) {
	    		++$column;
	    	}
	    	$spreadsheet->getActiveSheet()->mergeCells($startColumn.'6:'.$column.'6');
	    	$spreadsheet->getActiveSheet()->setCellValue($startColumn.'6', strtoupper('TOTAL ENROLEMENTS'));
	    	#styling
	    	$spreadsheet->getActiveSheet()->getStyle($startColumn.'6:'.$column.'6')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
	    	$spreadsheet->getActiveSheet()->getStyle($startColumn.'6:'.$column.'6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
	    }
		    # 6TH row end



		    # 7TH row start
		    if($schoolTypeList && $revenueHeads) {
		    $column = 'C';
		    foreach ($schoolTypeList as $key => $value) {
		    	$spreadsheet->getActiveSheet()->setCellValue($column.'7', strtoupper('No. Of'));
		    	$column++;
		    	
		    }
		   	$j= 0;
		   	$count = count($revenueHeads);
		    foreach ($revenueHeads as $key => $value) {
		    	if ($j == 0) {
		    			$startColumn = $column;
		    		}  else {
		    		$startColumn = ++$column;	
		    		}
		    		$j++;
		    	
		    	$i = 0;
				$len = count($schoolTypeList);
		    	foreach ($schoolTypeList as $key1 => $value1) {
		    		if ($i != $len - 1) {
		    			$column++;
		    		} 
		    		$i++;

		    	}
		    	$spreadsheet->getActiveSheet()->mergeCells($startColumn.'7:'.$column.'7');
		    	$spreadsheet->getActiveSheet()->setCellValue($startColumn.'7', '<-------------------------------------------->');
		    	#styling
		    	$spreadsheet->getActiveSheet()->getStyle($startColumn.'7:'.$column.'7')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
		    	$spreadsheet->getActiveSheet()->getStyle($startColumn.'7:'.$column.'7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
		    }

		    ++$column;++$column;++$column;++$column;++$column;

	    	$startColumn = $column;
			foreach ($schoolTypeList as $key1 => $value1) {
				$column++;
	    	}

	    	$spreadsheet->getActiveSheet()->mergeCells($startColumn.'7:'.$column.'7');
	    		$spreadsheet->getActiveSheet()->setCellValue($startColumn.'7', '<-------------------------------------------->');
	    	#styling
	    	$spreadsheet->getActiveSheet()->getStyle($startColumn.'7:'.$column.'7')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
	    	$spreadsheet->getActiveSheet()->getStyle($startColumn.'7:'.$column.'7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		    }
		    # 7TH row end
		    
		    # 8TH row start
		    $spreadsheet->getActiveSheet()->setCellValue('A8', 'LOCATION')
		    ->setCellValue('B8', 'NAME');
		    $column = 'C';
		    foreach ($schoolTypeList as $key => $value) {
		    	$spreadsheet->getActiveSheet()->setCellValue($column.'8', strtoupper($value.' SCHOOL'));
		    	$column++;
		    	
		    }
		    foreach ($revenueHeads as $key => $value) {
		    	foreach ($schoolTypeList as $key1 => $value1) {
		    		$spreadsheet->getActiveSheet()->setCellValue($column.'8', strtoupper($value1.' SCHOOL'));
		    		$column++;

		    	}
		    		
		    }

		    $spreadsheet->getActiveSheet()->setCellValue($column.'8', strtoupper('OTHERS FEE'));
		    $spreadsheet->getActiveSheet()->setCellValue(++$column.'8', strtoupper('TOTAL AMT'));
		    $spreadsheet->getActiveSheet()->setCellValue(++$column.'8', strtoupper('SERVICE TAX'));
		   	$spreadsheet->getActiveSheet()->setCellValue(++$column.'8', strtoupper('NET AMT'));

		   	++$column;
		   	foreach ($schoolTypeList as $key1 => $value1) {
		    		$spreadsheet->getActiveSheet()->setCellValue($column.'8', strtoupper($value1.' SCHOOL'));
		    		$column++;

		    	}
		    # 8TH row end
		    
		    # 9TH row start
		    if(!empty($LocationIds)) {
	        	#Iterate through locations
	        	$row = 9;
	          	foreach ($LocationIds as $key => $value) {
		          $locationCode = $value['code'];
		          $locationName = $value['name'];
		          $spreadsheet->getActiveSheet()->setCellValue('A'.$row, $locationCode);
					$spreadsheet->getActiveSheet()->setCellValue('B'.$row, $locationName);

		          #Iterate through School Type List for getting schooltypeCount
		          $column = 'C';
		          foreach ($schoolTypeList as $key2 => $value2) {
		            $locationWiseSchoolTypes = $this->getLocationWiseSchoolTypeCount($key, $key2);
		            
		            $spreadsheet->getActiveSheet()->setCellValue($column.$row, $locationWiseSchoolTypes);
	    			$column++;
		          }
		          $totalAmount = 0;
		          $totalServiceTax = 0;
		          foreach ($revenueHeads as $row1 => $data1) {
		            foreach ($schoolTypeList as $key1 => $value1) {
		            	 if($row1 == REVENUE_HEAD_STUDENT_FEE_NID) {
			                $feeReceived = $this->getStudentTypeReceivedFee($row1, $key1, $key, $requestData);

			                $totalAmount += $feeReceived[$row1][$key1]->fee_value;
			                $totalServiceTax += $feeReceived[$row1][$key1]->tax;
			              
			                $spreadsheet->getActiveSheet()->setCellValue($column.$row, $feeReceived[$row1][$key1]->fee_value);
		                } else {
			              $feeReceived = $this->getReceivedFee($row1, $key1, $key, $requestData);
			              $totalAmount += $feeReceived[$row1][$key1]->fee_value;
			              $totalServiceTax += $feeReceived[$row1][$key1]->tax;
			              
			              $spreadsheet->getActiveSheet()->setCellValue($column.$row, $feeReceived[$row1][$key1]->fee_value);
		          	  	}
		    		  $column++;
		            }
		          }

					$spreadsheet->getActiveSheet()->setCellValue($column.$row, '');
				    $spreadsheet->getActiveSheet()->setCellValue(++$column.$row,$totalAmount);
				    $spreadsheet->getActiveSheet()->setCellValue(++$column.$row,$totalServiceTax);
				   	$spreadsheet->getActiveSheet()->setCellValue(++$column.$row,ceil($totalAmount - $totalServiceTax));

				   	++$column;
					foreach ($schoolTypeList as $key2 => $value2) {
						$studentsEnroled = $this->getSchoolTypeWiseEnrolements($key2, $key);
						$spreadsheet->getActiveSheet()->setCellValue($column.$row, $studentsEnroled);
		    			$column++;
					}

					 $row++;

        	  	}
	        } 

		    $filename = "locationWiseSchoolMisReport";
			header("Content-Type: application/xls");    
			header("Content-Disposition: attachment; filename=$filename.xls");  
			header("Pragma: no-cache"); 
			header("Expires: 0");
			$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
			$writer->save("php://output");
			
	    } catch(Exception $e) {

		}
	}

	/**
	 * Get School Student type fee
	 * @param  int $schoolType 
	 * @param  int $locationId 
	 * @return int             
	 */
	public function getSchoolStudentTypeFee($schoolId, $studentFeeRevenueHead, $requestData = []) {
		// /$schoolId = 1052;
		$connection = \Drupal::database();
	    $query = $connection->select('usha_generate_fee_receipt', 'ufr');
	    $query->fields('ufr', ['revenue_head_type']);
	    $query->addExpression('SUM(ufr.total_pay_to_uil)', 'total_student_fee');
	    $query->addExpression('SUM(ufr.tax)', 'tax');
	    //$query->addExpression('COUNT(ufr.revenue_head_type)', 'count');
	    $query->condition('ufr.school_id', $schoolId);
	    $query->condition('ufr.want_to_add_student_fee', 1);
	    if($requestData && $requestData['from_date']) {
	        $query->condition('ufr.created_date', strtotime($requestData['from_date']), ">=");
	    }
	    if($requestData && $requestData['to_date']) {
	        $query->condition('ufr.created_date', strtotime($requestData['to_date']), "<=");
	    }
	    $query->groupBy('ufr.school_id');
		$results = $query->execute()->fetchAll();
		//print_r($results);die;
		$feeDetail = [];
		foreach ($results as $key => $value) {
			$feeDetail[$schoolId][$studentFeeRevenueHead] = $value;
			
		}
		
		return $feeDetail;
	}

	/**
	 * get total fee for respective revenue heads for a perticular school type in a location 
	 * @param  int $feeType    
	 * @param  int $schoolType 
	 * @param  int $locationId 
	 * @return array             
	 */
	public function getStudentTypeReceivedFee($feeType, $schoolType, $locationId, $requestData = []) {
		
		$connection = \Drupal::database();
	    $query = $connection->select('usha_generate_fee_receipt', 'ufr');
	    $query->addJoin('INNER', 'node__field_sewing_school_type', 'sst', 'ufr.school_id = sst.entity_id');
	    $query->addJoin('INNER', 'node__field_location', 'nfl', 'sst.entity_id = nfl.entity_id AND nfl.bundle = :sewingSchool', [':sewingSchool' => 'sewing_school']);
	    //$query->fields('ufr', ['revenue_head_type']);
	    //$query->addExpression('SUM(ufr.total_student_fee)', 'fee_value');
	    $query->addExpression('SUM(ufr.total_pay_to_uil)', 'fee_value');
	    $query->addExpression('SUM(ufr.tax)', 'tax');
	    //$query->addExpression('COUNT(ufr.revenue_head_type)', 'count');
	    $query->condition('sst.field_sewing_school_type_target_id', $schoolType);
	    $query->condition('nfl.field_location_target_id', $locationId);
	    if($requestData && $requestData['from_date']) {
	        $query->condition('ufr.created_date', strtotime($requestData['from_date']), ">=");
	    }
	    if($requestData && $requestData['to_date']) {
	        $query->condition('ufr.created_date', strtotime($requestData['to_date']), "<=");
	    }

	    //$query->condition('ufr.revenue_head_type', $feeType);
	    $query->condition('ufr.want_to_add_student_fee', 1);
	    //$query->groupBy('ufr.revenue_head_type');
		$results = $query->execute()->fetchAll();
		
		$feeDetail = [];
		foreach ($results as $key => $value) {
			$feeDetail[$feeType][$schoolType] = $value;
			
		}
		
		return $feeDetail;
	}
}