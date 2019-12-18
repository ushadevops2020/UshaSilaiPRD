<?php

namespace Drupal\sewing_school\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Form\FormBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Database\Database;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityInterface;
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\Request;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\TablePosition;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
//use PHPExcel\PHPExcel;

/**
 * Defines SewingReportController class.
 */   
class SewingReportController extends ControllerBase {
	
	public function sewingStudentCountReport(){
		$action = $_REQUEST['action'];

		# Start filter form
		$masterDataService = \Drupal::service('location_master.master_data');
		$locationListArr = array('_none' => '--Select--');
		$locationList = $masterDataService->getLocationByCountryId();
		if(!empty($locationList)) {
			$locationListArr = $locationListArr + $locationList;
		}
		$optionsArr['action'] = 'export';
		if($_REQUEST['location'])  {
			$optionsArr['location'] = $_REQUEST['location'];
		}
		if($_REQUEST['from_date'])  {
			$optionsArr['from_date'] = $_REQUEST['from_date'];
		}
		if($_REQUEST['to_date'])  {
			$optionsArr['to_date'] = $_REQUEST['to_date'];
		}            
		$form = [];
		$form['form'] = [
			'#type'  => 'form',
		];
		$form['form']['filters'] = [
			'#type'  => 'fieldset',
			'#open'  => true,
		];

		/* $form['form']['export_link'] = [
			'#title' => $this->t('Export'),
			'#type'  => 'link',
			'#url' => Url::fromRoute('sewing_statistics.sewing_school_type wise_report', $optionsArr),
			HASH_ATTRIBUTES => [CLASS_CONST => ['export-link']]
		]; */
		$form['form']['filters']['location'] = [
			'#title'         => $this->t('Location'),
			'#type'          => 'select',
			'#name'          => 'location',
			'#value' => $_REQUEST['location'],
			'#options'       => $locationListArr,
          
		];
		$form['form']['filters']['from_date'] = [
			'#title'         => $this->t('From Date'),
			'#type'          => 'date',
			'#name'          => 'from_date',
			'#value' => $_REQUEST['from_date'],
		];
		$form['form']['filters']['to_date'] = [
			'#title'         => $this->t('To Date'),
			'#type'          => 'date',
			'#name'          => 'to_date',
			'#value' => $_REQUEST['to_date'],
		];      
		$form['form']['filters']['actions'] = [
			'#type'       => 'actions'
		];
		$form['form']['filters']['actions'][HASH_PREFIX] = '<div class="filter-btn">';
      
		$form['form']['filters']['actions']['submit'] = [
			'#type'  => 'submit',
			'#value' => $this->t('Search'),
		];  
		$form['form']['filters']['actions'][HASH_SUFFIX] = '</div>';
		$form['form']['filters']['actions']['reset'] = array(
			'#type' => 'button',
			'#button_type' => 'reset',
			'#value' => $this->t('Reset'),
			'#validate' => array(),
			'#attributes' => array(
                'onclick' => 'resetPage(3);',
            ),
		);
		# END filter form

		$header = [
			'location' => t('Location Code'),
			'locationName' => t('Location Name'),
		];
		$masterDataService = \Drupal::service('sewing.master_data');
		$statisticsMasterDataService = \Drupal::service('sewing_statistics.master_data');
		# location Ids listing 
		if($_REQUEST && $_REQUEST['location'] && $_REQUEST['location'] != UNDERSCORE_NONE) {
			$locationIds = [$_REQUEST['location']];
			$LocationIds =  $masterDataService->getLocationList($_REQUEST['location']);
		} else {
			$LocationIds =  $masterDataService->getLocationList();
		}
    
		$schoolTypeList =  $statisticsMasterDataService->getSchoolTypeList($_REQUEST);
		$revenueHeads = $statisticsMasterDataService->getRevenueHeadList();
    
		foreach ($schoolTypeList as $key => $value) {
			$headerName = preg_replace('/\s+/', '', $value);
			$header[$headerName.$key] = strtoupper('TOTAL ENROLEMENTS IN '.$value);
		}
		$header['totalEnrol'] = t('TOTAL ENROLEMENTS');
		$output = [];
		/* if($action && $action == 'export') {
			$excelReport =  $statisticsMasterDataService->createLSTWiseReport($LocationIds, $revenueHeads, $schoolTypeList, $_REQUEST);

        exit;
		} else { */
			if(!empty($LocationIds)) {
				#Iterate through locations
				$a = 0;
				foreach ($LocationIds as $key => $value) {
					$output[$key]['location'] = $value['code'];
					$output[$key]['locationName'] = $value['name'];
					$a = 0;
					#Iterate through School Type List for getting schooltypeCount
					foreach ($schoolTypeList as $key2 => $value2) {
						$headerName = preg_replace('/\s+/', '', $value2);
						$studentsEnroled = $this->getSchoolTypeWiseEnrolements($key2, $key, $_REQUEST);
						$output[$key][$headerName.$key2] = ($studentsEnroled) ? $studentsEnroled : 0; 
						$a = $a + $studentsEnroled;
					}
					$output[$key]['totalEnrol'] = $a;
					$b = $b + $a;
				}
				$output[1111111111]['location'] = 'Total Students:-';
				$output[1111111111]['locationName'] = ' -- ';
				foreach ($schoolTypeList as $key => $value) {
					$headerName = preg_replace('/\s+/', '', $value);
					$output[1111111111][$headerName] = ' -- '; 
				}
				$output[1111111111]['totalAmt'] = ceil($b);
			}
		/* } */
    
		$form['table'] = [
            '#type' => 'table',
            '#header' => $header,
            '#rows' => $output,
            '#empty' => t('No Result found'),
        ];
                  
		$form['pager'] = array(
            '#type' => 'pager'
        );
		return $form;
	}
	/**
	* School Revenue report
	*/
	public function sewingLocationWiseSchoolReport() {
		$action = $_REQUEST['action'];

		# Start filter form
		$masterDataService = \Drupal::service('location_master.master_data');
		$locationListArr = array('_none' => '--Select--');
		$locationList = $masterDataService->getLocationByCountryId();
		if(!empty($locationList)) {
			$locationListArr = $locationListArr + $locationList;
		}

		$optionsArr['action'] = 'export';
		if($_REQUEST['location'])  {
			$optionsArr['location'] = $_REQUEST['location'];
		}
		if($_REQUEST['from_date'])  {
			$optionsArr['from_date'] = $_REQUEST['from_date'];
		}
		if($_REQUEST['to_date'])  {
			$optionsArr['to_date'] = $_REQUEST['to_date'];
		}            
		$form = [];
		$form['form'] = [
			'#type'  => 'form',
		];

		$form['form']['filters'] = [
			'#type'  => 'fieldset',
			'#open'  => true,
		];
		$form['form']['filters']['location'] = [
			'#title'         => $this->t('Location'),
			'#type'          => 'select',
			'#name'          => 'location',
			'#value' => $_REQUEST['location'],
			'#options'       => $locationListArr,
          
		];
		$form['form']['filters']['from_date'] = [
			'#title'         => $this->t('From Date'),
			'#type'          => 'date',
			'#name'          => 'from_date',
			'#value' => $_REQUEST['from_date'],
		];
		$form['form']['filters']['to_date'] = [
			'#title'         => $this->t('To Date'),
			'#type'          => 'date',
			'#name'          => 'to_date',
			'#value' => $_REQUEST['to_date'],
		];      
		$form['form']['filters']['actions'] = [
			'#type'       => 'actions'
		];
		$form['form']['filters']['actions'][HASH_PREFIX] = '<div class="filter-btn">';
      
		$form['form']['filters']['actions']['submit'] = [
			'#type'  => 'submit',
			'#value' => $this->t('Search'),
			//HASH_ATTRIBUTES => [CLASS_CONST => ['location-search']]
		];  
		$form['form']['filters']['actions'][HASH_SUFFIX] = '</div>';
		$form['form']['filters']['actions']['reset'] = array(
			'#type' => 'button',
			'#button_type' => 'reset',
			'#value' => $this->t('Reset'),
			'#validate' => array(),
			'#attributes' => array(
                'onclick' => 'resetPage(3);',
            ),
		);
		/* $form['export_link'] = [
			'#title' => $this->t('Export'),
			'#type'  => 'link',
			'#url' => Url::fromRoute('sewing_school.sewing_school_report_count', $optionsArr),
			HASH_ATTRIBUTES => [CLASS_CONST => ['export-link']]
		]; */
      # END filter form

		$header = [
		'location' => t('Location Code'),
		'locationName' => t('Location Name'),
		];
		$masterDataService = \Drupal::service('sewing.master_data');
		$statisticsMasterDataService = \Drupal::service('sewing_statistics.master_data');

     # location Ids listing 
		if($_REQUEST && $_REQUEST['location'] && $_REQUEST['location'] != UNDERSCORE_NONE) {
			$locationIds = [$_REQUEST['location']];
			$LocationIds =  $masterDataService->getLocationList($_REQUEST['location']);
		} else {
			$LocationIds =  $masterDataService->getLocationList();
		}
    
		$schoolTypeList =  $statisticsMasterDataService->getSchoolTypeList($_REQUEST);
		$revenueHeads = $statisticsMasterDataService->getRevenueHeadList();

		foreach ($schoolTypeList as $key => $value) {
			$headerName = preg_replace('/\s+/', '', $value);
			$header[$headerName] = strtoupper($value); 
		}
		$header['Total'] = 'Total';
		$output = [];

		if(!empty($LocationIds)) {
        #Iterate through locations
		$b = 0;
			foreach ($LocationIds as $key => $value) {
				$output[$key]['location'] = $value['code'];
				$output[$key]['locationName'] = $value['name'];
				#Iterate through School Type List for getting schooltypeCount
				$a = 0;
				foreach ($schoolTypeList as $row => $data) {
					$locationId = $key;
					$schoolType = $row;
					$query = \Drupal::entityQuery('node');
						$query->condition('type', 'sewing_school');
						$query->condition(STATUS, 1);
						$query->condition('field_sew_school_approval_status', 1);
						$query->condition('field_location', $locationId);
						$query->condition('field_sewing_school_type', $schoolType);
						if($_REQUEST['from_date']){
							$query->condition('field_school_creation_date', $_REQUEST['from_date'], '>=');
						}
						if($_REQUEST['to_date']){
							$query->condition('field_school_creation_date', $_REQUEST['to_date'], '<=');
						}
					$locationWiseSchoolTypes = $query->count()->execute();
					//return $ids;
					//$locationWiseSchoolTypes = $statisticsMasterDataService->getLocationWiseSchoolTypeCount($key, $row);
					$schoolTypeKeyName  = preg_replace('/\s+/', '', $data);
					$output[$key][$schoolTypeKeyName] = $locationWiseSchoolTypes;
					$a = $a + $locationWiseSchoolTypes;
				}
				$output[$key]['Total'] = $a;
				$b = $b + $a;
			}
		}
		$output[1111111111]['location'] = 'Total :-';
		$output[1111111111]['locationName'] = ' -- ';
		foreach ($schoolTypeList as $key => $value) {
			$headerName = preg_replace('/\s+/', '', $value);
			$output[1111111111][$headerName] = ' -- '; 
		}
		$output[1111111111]['Total'] = $b;
		
    
		$form['table'] = [
            '#type' => 'table',
            '#header' => $header,
            '#rows' => $output,
            '#empty' => t('No Result found'),
        ];
                  
		$form['pager'] = array(
            '#type' => 'pager'
        );
    return $form;
 
	} 
	public function sewingRevenueCollectionReport(){
		$action = $_REQUEST['action'];

		# Start filter form
		$masterDataService = \Drupal::service('location_master.master_data');
		$locationListArr = array('_none' => '--Select--');
		$locationList = $masterDataService->getLocationByCountryId();
		if(!empty($locationList)) {
			$locationListArr = $locationListArr + $locationList;
		}
		$optionsArr['action'] = 'export';
		if($_REQUEST['location'])  {
			$optionsArr['location'] = $_REQUEST['location'];
		}
		if($_REQUEST['from_date'])  {
			$optionsArr['from_date'] = $_REQUEST['from_date'];
		}
		if($_REQUEST['to_date'])  {
			$optionsArr['to_date'] = $_REQUEST['to_date'];
		}            
		$form = [];
		$form['form'] = [
			'#type'  => 'form',
		];
		$form['form']['filters'] = [
			'#type'  => 'fieldset',
			'#open'  => true,
		];
		$form['form']['filters']['location'] = [
			'#title'         => $this->t('Location'),
			'#type'          => 'select',
			'#name'          => 'location',
			'#value' => $_REQUEST['location'],
			'#options'       => $locationListArr,
          
		];
		$form['form']['filters']['from_date'] = [
			'#title'         => $this->t('From Date'),
			'#type'          => 'date',
			'#name'          => 'from_date',
			'#value' => $_REQUEST['from_date'],
		];
		$form['form']['filters']['to_date'] = [
			'#title'         => $this->t('To Date'),
			'#type'          => 'date',
			'#name'          => 'to_date',
			'#value' => $_REQUEST['to_date'],
		];      
		$form['form']['filters']['actions'] = [
			'#type'       => 'actions'
		];
		$form['form']['filters']['actions'][HASH_PREFIX] = '<div class="filter-btn">';
		$form['form']['filters']['actions']['submit'] = [
			'#type'  => 'submit',
			'#value' => $this->t('Search'),
          //HASH_ATTRIBUTES => [CLASS_CONST => ['location-search']]
		];  
		$form['form']['filters']['actions'][HASH_SUFFIX] = '</div>';
		$form['form']['filters']['actions']['reset'] = array(
			'#type' => 'button',
			'#button_type' => 'reset',
			'#value' => $this->t('Reset'),
			'#validate' => array(),
			'#attributes' => array(
                'onclick' => 'resetPage(3);',
            ),
		);
		$form['export_link'] = [
			'#title' => $this->t('Export'),
			'#type'  => 'link',
			'#url' => Url::fromRoute('sewing_school.sewing_revenue_collection_report', $optionsArr),
			HASH_ATTRIBUTES => [CLASS_CONST => ['export-link']]
		];
		# END filter form
		$header = [
			'location' => t('Location Code'),
			'locationName' => t('Location Name'),
		];
		$masterDataService = \Drupal::service('sewing.master_data');
		$statisticsMasterDataService = \Drupal::service('sewing_statistics.master_data');

		//print_r($_REQUEST);die;
		# location Ids listing 
		if($_REQUEST && $_REQUEST['location'] && $_REQUEST['location'] != UNDERSCORE_NONE) {
			$locationIds = [$_REQUEST['location']];
			$LocationIds =  $masterDataService->getLocationList($_REQUEST['location']);
		} else {
			$LocationIds =  $masterDataService->getLocationList();
		}
		$schoolTypeList =  $statisticsMasterDataService->getSchoolTypeList($_REQUEST);
    
		$revenueHeads = $statisticsMasterDataService->getRevenueHeadList();
		foreach ($revenueHeads as $row => $data) {
			foreach ($schoolTypeList as $key => $value) {
				$headerKey = preg_replace('/\s+/', '', $value);
				//$headerValue = explode(' ', $value);
				//$header[$data.$headerKey] = strtoupper($data.' Received '. $headerValue[0].' Schools');
				$header[$data.$headerKey] = strtoupper($data.' Received By '. $value);
			}
		}
		//$header['othersfee'] = t('OTHERS FEE');
		$header['totalAmt'] = t('TOTAL AMOUNT');
		$header['servicetax'] = t('SERVICE TAX');
		$header['netAmt'] = t('NET AMOUNT');
		$output = [];
		if($action && $action == 'export') {
			$excelReport = $this->revenueCollectionReportCSV($LocationIds, $_REQUEST);
			exit;
		} else {
			if(!empty($LocationIds)) {
			#Iterate through locations
				foreach ($LocationIds as $key => $value) {
					$output[$key]['location'] = $value['code'];
					$output[$key]['locationName'] = $value['name'];
					$totalAmount = 0;
					$totalServiceTax = 0;
					foreach ($revenueHeads as $row1 => $data1) {
						foreach ($schoolTypeList as $key1 => $value1) {
							$headerKey = preg_replace('/\s+/', '', $value['name'].$data1.$value1);
							if($row1 == REVENUE_HEAD_STUDENT_FEE_NID) {
								$feeReceived = $this->getStudentTypeReceivedFee($row1, $key1, $key, $_REQUEST);
								$totalAmount += $feeReceived[$row1][$key1]->fee_value;
								$totalServiceTax += $feeReceived[$row1][$key1]->tax;
					
								$output[$key][$headerKey] = $feeReceived[$row1][$key1]->fee_value;
							} else {
								$feeReceived = $this->getReceivedFee($row1, $key1, $key, $_REQUEST);
								$totalAmount += $feeReceived[$row1][$key1]->fee_value;
								$totalServiceTax += $feeReceived[$row1][$key1]->tax;
						
								$output[$key][$headerKey] = $feeReceived[$row1][$key1]->fee_value;
							}
						}
					}
					//$output[$key]['othersfee'] = '';
					$output[$key]['totalAmt'] = ceil($totalAmount);
					$output[$key]['servicetax'] = ceil($totalServiceTax);
					$output[$key]['netAmt'] = ceil($totalAmount - $totalServiceTax);
				}
			}
		}
    
		$form['table'] = [
            '#type' => 'table',
            '#header' => $header,
            '#rows' => $output,
            '#empty' => t('No Result found'),
        ];        
		$form['pager'] = array(
            '#type' => 'pager'
        );
        return $form;
	}
//
	function revenueCollectionReportCSV($LocationIds, $requestData) {
		try {

			$LocationIds = ($LocationIds) ? $LocationIds : [];
			$fromDate = ($requestData['from_date']) ? date('d/m/Y', strtotime($requestData['from_date'])) : '';
			$toDate = ($requestData['to_date']) ? date('d/m/Y', strtotime($requestData['to_date'])) : '';

			$spreadsheet = new Spreadsheet();
			$activeSheet = $spreadsheet->getActiveSheet();
			
			$spreadsheet->getActiveSheet()->setCellValue('A1', 'LOCATION CODE')
			->setCellValue('B1', 'LOCATION NAME');
			$column = 'C'; 
			
			$statisticsMasterDataService = \Drupal::service('sewing_statistics.master_data');
			$schoolTypeList =  $statisticsMasterDataService->getSchoolTypeList($_REQUEST);
			$revenueHeads = $statisticsMasterDataService->getRevenueHeadList();
			
			foreach ($revenueHeads as $key => $value) {
				foreach ($schoolTypeList as $key1 => $value1) {
					$spreadsheet->getActiveSheet()->setCellValue($column.'1', strtoupper($value.' RECEIVED '.$value1));
					$column++;
				}
					
			}

			//$spreadsheet->getActiveSheet()->setCellValue(++$column.'1', strtoupper('TOTAL AMT'));
			$spreadsheet->getActiveSheet()->setCellValue($column.'1', strtoupper('TOTAL AMT'));
			$spreadsheet->getActiveSheet()->setCellValue(++$column.'1', strtoupper('SERVICE TAX'));
			$spreadsheet->getActiveSheet()->setCellValue(++$column.'1', strtoupper('NET AMT'));

			++$column;

			if(!empty($LocationIds)) {
				#Iterate through locations
				$row = 2;
				foreach ($LocationIds as $key => $value) {
				  $locationCode = $value['code'];
				  $locationName = $value['name'];
				  $spreadsheet->getActiveSheet()->setCellValue('A'.$row, $locationCode);
					$spreadsheet->getActiveSheet()->setCellValue('B'.$row, $locationName);

				  #Iterate through School Type List for getting schooltypeCount
				  $column = 'C';
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

					//$spreadsheet->getActiveSheet()->setCellValue($column.$row, '');
					//$spreadsheet->getActiveSheet()->setCellValue(++$column.$row,$totalAmount);
					$spreadsheet->getActiveSheet()->setCellValue($column.$row,$totalAmount);
					$spreadsheet->getActiveSheet()->setCellValue(++$column.$row,$totalServiceTax);
					$spreadsheet->getActiveSheet()->setCellValue(++$column.$row,ceil($totalAmount - $totalServiceTax));

					++$column;
					 $row++;

				}
			} 

			$filename = "Revenue-Collection-Report";
			header("Content-Type: application/xls");    
			header("Content-Disposition: attachment; filename=$filename.xls");  
			header("Pragma: no-cache"); 
			header("Expires: 0");
			$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
			$writer->save("php://output");
			
		} catch(Exception $e) {

		}
	}
	function getLocationWiseSchoolTypeCount($locationId, $schoolType) {
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
	function getSchoolTypeWiseEnrolements($schoolType, $locationId, $requestData = []) {
		$connection = \Drupal::database();
		$query = $connection->select('node__field_sewing_school_code_list', 'sscl');
		$query->fields('sscl');
		$query->addJoin('INNER', 'node__field_student_admission_date', 'sadate', 'sscl.entity_id = sadate.entity_id');
		//$query->addJoin('INNER', 'node__field_sew_student_approval_statu', 'sastatus', 'sscl.entity_id = sastatus.entity_id');
		$query->addJoin('INNER', 'node__field_sewing_school_type', 'sst', 'sscl.field_sewing_school_code_list_target_id = sst.entity_id');
		$query->addJoin('INNER', 'node__field_location', 'nfl', 'sst.entity_id = nfl.entity_id AND nfl.bundle = :sewingSchool', [":sewingSchool" => "sewing_school"]);
		//$query->addExpression('COUNT(sscl.entity_id)', 'count');
		$query->condition('sst.field_sewing_school_type_target_id', $schoolType);
		$query->condition('nfl.field_location_target_id', $locationId);
		$query->condition('sscl.bundle', 'manage_sewing_students');
		
		//$query->condition('sastatus.field_sew_student_approval_statu_value', 1, "=");
		
		if($requestData && $requestData['from_date']) {
			$fromDate = date('Y-m-d' ,strtotime($requestData['from_date']));
			$query->condition('sadate.field_student_admission_date_value', $fromDate, ">=");
		}
		if($requestData && $requestData['to_date']) {
			$toDate = date('Y-m-d' ,strtotime($requestData['to_date']));
			$query->condition('sadate.field_student_admission_date_value', $toDate, "<=");
		}
		//$query->groupBy('sscl.field_sewing_school_code_list_target_id');
		$rowCounts = $query->countQuery()->execute()->fetchField();
		
		return $rowCounts;
	}
	function getReceivedFee($feeType, $schoolType, $locationId, $requestData = []) {
		
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
	function getStudentTypeReceivedFee($feeType, $schoolType, $locationId, $requestData = []) {
		
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
//

}

