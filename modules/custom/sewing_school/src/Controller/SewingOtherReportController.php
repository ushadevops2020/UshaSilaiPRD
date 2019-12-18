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
 * Defines SewingOtherReportController class.
 */   
class SewingOtherReportController extends ControllerBase {
	public function sewingRevenueHeadWiseRevenueCollection() {
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

		foreach ($revenueHeads as $key => $value) {
			$headerName = preg_replace('/\s+/', '', $value);
			$header[$headerName] = strtoupper($value); 
		}
		$header['totalAmt'] = t('TOTAL AMOUNT');
		$header['servicetax'] = t('SERVICE TAX');
		$header['netAmt'] = t('NET AMOUNT');
		$output = [];

		if(!empty($LocationIds)) {
        #Iterate through locations
			foreach ($LocationIds as $key => $value) {
				$location = $key;
				$output[$location]['location'] = $value['code'];
				$output[$location]['locationName'] = $value['name'];
				#Iterate through School Type List for getting schooltypeCount
				$totalAmount = 0;
				$totalServiceTax = 0;
				foreach ($revenueHeads as $row => $data) {
					if($row == REVENUE_HEAD_STUDENT_FEE_NID) {
						/************************/
						$connection = \Drupal::database();
						$query = $connection->select('usha_generate_fee_receipt', 'ufr');
						$query->addJoin('INNER', 'node__field_sewing_school_type', 'sst', 'ufr.school_id = sst.entity_id');
						$query->addJoin('INNER', 'node__field_location', 'nfl', 'sst.entity_id = nfl.entity_id AND nfl.bundle = :sewingSchool', [':sewingSchool' => 'sewing_school']);
						$query->addExpression('SUM(ufr.total_pay_to_uil)', 'fee_value');
						$query->addExpression('SUM(ufr.tax)', 'tax');
						//$query->condition('sst.field_sewing_school_type_target_id', $schoolType);
						$query->condition('nfl.field_location_target_id', $location);
						if($_REQUEST && $_REQUEST['from_date']) {
							$query->condition('ufr.created_date', strtotime($_REQUEST['from_date']), ">=");
						}
						if($_REQUEST && $_REQUEST['to_date']) {
							$query->condition('ufr.created_date', strtotime($_REQUEST['to_date']), "<=");
						}
						$query->condition('ufr.want_to_add_student_fee', 1);
						$results = $query->execute()->fetchAll();
						
						$feeDetail = [];
						foreach ($results as $key => $value) {
							$feeDetail[$row] = $value;
							
						}
						
						$feeReceived = $feeDetail;
						
						/************************/
						
						$totalAmount += $feeReceived[$row]->fee_value;
						$totalServiceTax += $feeReceived[$row]->tax;
						$revenueHeadKeyName  = preg_replace('/\s+/', '', $data);
						$output[$location][$revenueHeadKeyName] = $feeReceived[$row]->fee_value;
						
						/* $totalAmount += 2;
						$totalServiceTax += 2;
						$revenueHeadKeyName  = preg_replace('/\s+/', '', $data);
						$output[$locationId][$revenueHeadKeyName] = 2; */
					} else {
						/***************/
						$connection = \Drupal::database();
						$query = $connection->select('usha_generate_fee_receipt', 'ufr');
						$query->addJoin('INNER', 'node__field_sewing_school_type', 'sst', 'ufr.school_id = sst.entity_id');
						$query->addJoin('INNER', 'node__field_location', 'nfl', 'sst.entity_id = nfl.entity_id AND nfl.bundle = :sewingSchool', [':sewingSchool' => 'sewing_school']);
						$query->fields('ufr', ['revenue_head_type']);
						$query->addExpression('SUM(ufr.total_pay_to_uil)', 'fee_value');
						$query->addExpression('SUM(ufr.tax)', 'tax');
						//$query->condition('sst.field_sewing_school_type_target_id', $schoolType);
						$query->condition('nfl.field_location_target_id', $location);
						$query->condition('ufr.revenue_head_type', $row);
						if($_REQUEST && $_REQUEST['from_date']) {
							$query->condition('ufr.created_date', strtotime($_REQUEST['from_date']), ">=");
						}
						if($_REQUEST && $_REQUEST['to_date']) {
							$query->condition('ufr.created_date', strtotime($_REQUEST['to_date']), "<=");
						}
						$results = $query->execute()->fetchAll();
						
						$feeDetail = [];
						foreach ($results as $key => $value) {
							$feeDetail[$row] = $value;
							
						}
						//print_r($feeDetail);
						//echo '<br>';
						$feeReceived = $feeDetail;
						/***************/
						$totalAmount += $feeReceived[$row]->fee_value;
						$totalServiceTax += $feeReceived[$row]->tax;
						$revenueHeadKeyName  = preg_replace('/\s+/', '', $data);
						$output[$location][$revenueHeadKeyName] = $feeReceived[$row]->fee_value;
						
						/* $totalAmount += 4;
						$totalServiceTax += 4;
						$revenueHeadKeyName  = preg_replace('/\s+/', '', $data);
						$output[$locationId][$revenueHeadKeyName] = 4; */
					}
				}
				$output[$location]['totalAmt'] = ceil($totalAmount);
				$output[$location]['servicetax'] = ceil($totalServiceTax);
				$output[$location]['netAmt'] = ceil($totalAmount - $totalServiceTax);
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
	public function sewingSchoolTypeWiseRevenueCollection() {
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
		$header['totalAmt'] = t('TOTAL AMOUNT');
		$header['servicetax'] = t('SERVICE TAX');
		$header['netAmt'] = t('NET AMOUNT');
		$output = [];
	if(!empty($_REQUEST['from_date'])){
		if(!empty($LocationIds)) {
        #Iterate through locations
		$xx = 0;
		$yy = 0;
		$zz = 0;
			foreach ($LocationIds as $key => $value) {
				$location = $key;
				$output[$location]['location'] = $value['code'];
				$output[$location]['locationName'] = $value['name'];
				#Iterate through School Type List for getting schooltypeCount
				$totalAmount = 0;
				$totalServiceTax = 0;
				
				/************************************/
				foreach ($schoolTypeList as $key1 => $value1) {
					$output1 = 0;
					$output2 = 0;
					$aa = 0;
					$bb = 0;
					$headerKey = preg_replace('/\s+/', '', $value1);
					foreach ($revenueHeads as $row1 => $data1) {
						if($row1 == REVENUE_HEAD_STUDENT_FEE_NID) {
							$feeReceived = $this->getStudentTypeReceivedFee($row1, $key1, $location, $_REQUEST);
							$totalAmount += $feeReceived[$row1][$key1]->fee_value;
							$totalServiceTax += $feeReceived[$row1][$key1]->tax;
				
							$output1 = $feeReceived[$row1][$key1]->fee_value;
							$aa = $output1 + $aa;
						} else {
							$feeReceived = $this->getReceivedFee($row1, $key1, $location, $_REQUEST);
							$totalAmount += $feeReceived[$row1][$key1]->fee_value;
							$totalServiceTax += $feeReceived[$row1][$key1]->tax;
					
							$output2 = $feeReceived[$row1][$key1]->fee_value;
							$bb = $output2 + $bb;
						}
					}
					$output[$location][$headerKey] = $aa + $bb;
				}
				/***********************************/
				$output[$location]['totalAmt'] = ceil($totalAmount);
				$output[$location]['servicetax'] = ceil($totalServiceTax);
				$output[$location]['netAmt'] = ceil($totalAmount - $totalServiceTax);
				$xx = $xx + ceil($totalAmount);
				$yy = $yy + ceil($totalServiceTax);
				$zz = $zz + ceil($totalAmount - $totalServiceTax);
			}
			$output[1111111111]['location'] = 'Total Amount:-';
			$output[1111111111]['locationName'] = ' -- ';
			foreach ($schoolTypeList as $key => $value) {
				$headerName = preg_replace('/\s+/', '', $value);
				$output[1111111111][$headerName] = ' -- '; 
			}
			$output[1111111111]['totalAmt'] = ceil($xx);
			$output[1111111111]['servicetax'] = ceil($yy);
			$output[1111111111]['netAmt'] = ceil($zz);
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
	

}

