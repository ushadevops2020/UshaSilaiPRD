<?php

namespace Drupal\silai_reports\Controller;

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
class SilaiSchoolCountReportController extends ControllerBase {
	
	public function silaiSchoolCountReport(){
		$action = $_REQUEST['action'];
		# Start filter form
		$reportMasterDataService = \Drupal::service('silai_reports.master_data');
		$locationListArr = array('_none' => '--Select--');
		$locationList = $reportMasterDataService->getSilaiLocation();
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
		/* $form['form']['filters']['location'] = [
			'#title'         => $this->t('Location'),
			'#type'          => 'select',
			'#name'          => 'location',
			'#value' => $_REQUEST['location'],
			'#options'       => $locationListArr,
          
		]; */
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
		# location Ids listing 
		if($_REQUEST && $_REQUEST['location'] && $_REQUEST['location'] != UNDERSCORE_NONE) {
			$locationIds = [$_REQUEST['location']];
			$LocationIds =  $reportMasterDataService->getSilaiLocationList($_REQUEST['location']);
		} else {
			$LocationIds =  $reportMasterDataService->getSilaiLocationList();
		}
		# Get School Type Array
		$schoolTypeList =  $reportMasterDataService->getSilaiSchoolTypeList();
		foreach ($schoolTypeList as $key => $value) {
			$headerName = preg_replace('/\s+/', '', $value);
			$header[$headerName.$key] = strtoupper($value);
		}
		$header['total'] = t('TOTAL');
		$output = [];
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
						
						$db = Database::getConnection();
						$query = $db->select('node_field_data', 'n');
						
						$query->join('node__field_sil_school_approval_status', 's_status', 's_status.entity_id = n.nid');
						$query->join('node__field_school_type', 's_type', 's_type.entity_id = n.nid');
						//$query->join('node__field_silai_business_state', 's_state', 's_state.entity_id = n.nid');
						$query->join('node__field_silai_location', 's_location', 's_location.entity_id = n.nid');
						
						$query->condition('s_status.bundle', ['silai_school']);
						$query->condition('s_type.bundle', ['silai_school']);
						//$query->condition('s_state.bundle', ['silai_school']);
						
						$query->condition('n.type', ['silai_school']);
						$query->condition('s_status.field_sil_school_approval_status_value', 1);
						$query->condition('s_type.field_school_type_target_id', $key2);
						$query->condition('s_location.field_silai_location_target_id', $key);
						
						if($_REQUEST['from_date']) {
							$query->condition('n.created', strtotime($_REQUEST['from_date']), ">=");
						}
						if($_REQUEST['to_date']) {
							$query->condition('n.created', strtotime($_REQUEST['to_date']), "<=");
						}
						
						$query->addExpression('COUNT(n.nid)', 'schoolCount'); 
						$schooCounts = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
						//$schoolDetails = [];
						foreach ($schooCounts as $key3 => $value3) {
							$schoolDetails = $value3->schoolCount;
							
						}
						//print_r($schoolDetails);echo ', ';
						//die;
						
						$output[$key][$headerName.$key2] = ($schoolDetails) ? $schoolDetails : 0; 
						$a = $a + $schoolDetails;
					}
					$output[$key]['total'] = $a; 
					$b = $b + $a;
				}
				$output[1111111111]['location'] = 'Total :-';
				$output[1111111111]['locationName'] = ' -- ';
				foreach ($schoolTypeList as $key => $value) {
					$headerName = preg_replace('/\s+/', '', $value);
					$output[1111111111][$headerName] = ' -- '; 
				}
				$output[1111111111]['total'] = ceil($b);
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



}

