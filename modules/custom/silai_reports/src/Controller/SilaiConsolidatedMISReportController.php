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
 * Defines SilaiConsolidatedMISReportController class.
 */   
class SilaiConsolidatedMISReportController extends ControllerBase {
	
	public function silaiConsolidatedMISReport(){
		$action = $_REQUEST['action'];
		$optionsArr['action'] = 'export';         
		$form = [];
		$form['form'] = [
			'#type'  => 'form',
		];
		$form['form']['export_link'] = [
			'#title' => $this->t('Export'),
			'#type'  => 'link',
			'#url' => Url::fromRoute('silai_reports.silai_consolidated_mis_report', $optionsArr),
			HASH_ATTRIBUTES => [CLASS_CONST => ['export-link']]
		];    
		$header = [
			//'nid' => t('Node ID'),
			'schoolCode' => t('School Code'),
			'schoolType' => t('School Type'),
			//'machineCount' => t('Machine Count'),
			'enrolledLearner' => t('Enrolled Learner'),
			'learnerCompletedTillDate' => t('Learner Completed Till Date'),
			//'averageFee' => t('Average Fee'),
			'incomeFromLearnerFee' => t('Income From Fee'),
			'incomeFromJobwork' => t('Income From Jobwork'),
			'incomeFromRepairing' => t('Income From Repairing'),
			'totalIncome' => t('Total Income'),
		];

		if($action && $action == 'export') {
			//die('die Command');
			$excelReport =  $this->silaiConsolidatedMISReportExcel();
			exit;
		} else {
			$masterDataService = \Drupal::service('silai_reports.master_data');
			$schoolNodes =  $masterDataService->getSilaiSchoolsNodes($_REQUEST);

			foreach($schoolNodes as $node) {
				// MIS Query
				$connection = Database::getConnection();
				$misQry = $connection->select('usha_monthly_mis', 'f')
				->fields('f', array(
							//'whether_entrepreneur_machine',
							//'brand_of_machine',
							'no_of_learners',
							'no_of_learners_course_completed',
							//'fee_charged_learners_month',
							'income_from_learners_fee',
							'income_from_tailoring',
							'income_from_sewing_machine_repairing',
							'total_income',
						)
				)->condition('school_code', $node->id());
				$misData = $misQry->execute();
				$misRows = $misData->fetchAll(\PDO::FETCH_OBJ);
				//$machineCount = 0;
				$misLearnerCount = 0;
				$mislearnerCompletedTillDate = 0;
				//$misAverageFee = 0;
				$incomeFromLearnerFee = 0;
				$incomeFromJobwork = 0;
				$incomeFromRepairing = 0;
				$totalIncome = 0;
				foreach($misRows as $misRow){
					//$machineCount = $misRow->whether_entrepreneur_machine + $misRow->brand_of_machine + $machineCount;
					$misLearnerCount = $misLearnerCount + $misRow->no_of_learners;
					$mislearnerCompletedTillDate = $mislearnerCompletedTillDate + $misRow->no_of_learners_course_completed;
					//$misAverageFee = $misAverageFee + $misRow->fee_charged_learners_month;
					$incomeFromLearnerFee = $incomeFromLearnerFee + $misRow->income_from_learners_fee;
					$incomeFromJobwork = $incomeFromJobwork + $misRow->income_from_tailoring;
					$incomeFromRepairing = $incomeFromRepairing + $misRow->income_from_sewing_machine_repairing;
					$totalIncome = $totalIncome + $misRow->total_income;
				}
				// School Survey Query
				$surveyQuery = $connection->select(TABLE_SILAI_ADD_SCHOOL_DATA, 's')
						->condition(NID, $node->id())
						->fields('s');
				$surveyData = $surveyQuery->execute()->fetchAssoc();
				
				$output[$node->id()] = [
					//'nid' => $node->id(),
					'schoolCode' => $node->field_school_code->value,
					'schoolType' => Node::load($node->field_school_type->target_id)->getTitle(),
					//'machineCount' => $machineCount,
					'enrolledLearner' => $misLearnerCount + $surveyData['average_learners_attending'],
					'learnerCompletedTillDate' => $mislearnerCompletedTillDate + $surveyData['how_many_learners_you_have_trained'],
					//'averageFee' => $misAverageFee,
					'incomeFromLearnerFee' => $incomeFromLearnerFee + $surveyData['monthly_income_learners_fee'],
					'incomeFromJobwork' => $incomeFromJobwork + $surveyData['monthly_income_stitching'],
					'incomeFromRepairing' => $incomeFromRepairing + $surveyData['income_from_sewing_machine_repairing'], 
					'totalIncome' => $totalIncome + $surveyData['monthly_income_from_silai_schools'],
				];

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
	function silaiConsolidatedMISReportExcel() {
		//die('hello');
		try {

			$spreadsheet = new Spreadsheet();
			$activeSheet = $spreadsheet->getActiveSheet();
			$spreadsheet->getActiveSheet()->setCellValue('A1', 'School Code')
			->setCellValue('B1', 'School Type')
			->setCellValue('C1', 'Enrolled Learner')
			->setCellValue('D1', 'Learner Completed Till Date')
			->setCellValue('E1', 'Income From Fee')
			->setCellValue('F1', 'Income From Jobwork')
			->setCellValue('G1', 'Income From Repairing')
			->setCellValue('H1', 'Total Income');
			//print_r($spreadsheet);
			//die('hello');
			$masterDataService = \Drupal::service('silai_reports.master_data');
			$schoolNodes =  $masterDataService->getSilaiSchoolsNodesAll();
			$exlRow = 2;
			foreach($schoolNodes as $node) {
				// MIS Query
				$connection = Database::getConnection();
				$misQry = $connection->select('usha_monthly_mis', 'f')
				->fields('f', array(
							//'whether_entrepreneur_machine',
							//'brand_of_machine',
							'no_of_learners',
							'no_of_learners_course_completed',
							//'fee_charged_learners_month',
							'income_from_learners_fee',
							'income_from_tailoring',
							'income_from_sewing_machine_repairing',
							'total_income',
						)
				)->condition('school_code', $node->id());
				$misData = $misQry->execute();
				$misRows = $misData->fetchAll(\PDO::FETCH_OBJ);
				//$machineCount = 0;
				$misLearnerCount = 0;
				$mislearnerCompletedTillDate = 0;
				//$misAverageFee = 0;
				$incomeFromLearnerFee = 0;
				$incomeFromJobwork = 0;
				$incomeFromRepairing = 0;
				$totalIncome = 0;
				foreach($misRows as $misRow){
					//$machineCount = $misRow->whether_entrepreneur_machine + $misRow->brand_of_machine + $machineCount;
					$misLearnerCount = $misLearnerCount + $misRow->no_of_learners;
					$mislearnerCompletedTillDate = $mislearnerCompletedTillDate + $misRow->no_of_learners_course_completed;
					//$misAverageFee = $misAverageFee + $misRow->fee_charged_learners_month;
					$incomeFromLearnerFee = $incomeFromLearnerFee + $misRow->income_from_learners_fee;
					$incomeFromJobwork = $incomeFromJobwork + $misRow->income_from_tailoring;
					$incomeFromRepairing = $incomeFromRepairing + $misRow->income_from_sewing_machine_repairing;
					$totalIncome = $totalIncome + $misRow->total_income;
				}
				// School Survey Query
				$surveyQuery = $connection->select(TABLE_SILAI_ADD_SCHOOL_DATA, 's')
						->condition(NID, $node->id())
						->fields('s');
				$surveyData = $surveyQuery->execute()->fetchAssoc();
				
				$spreadsheet->getActiveSheet()->setCellValue('A'.$exlRow, $node->field_school_code->value);
				$spreadsheet->getActiveSheet()->setCellValue('B'.$exlRow, Node::load($node->field_school_type->target_id)->getTitle());
				$spreadsheet->getActiveSheet()->setCellValue('C'.$exlRow, $misLearnerCount + $surveyData['average_learners_attending']);
				$spreadsheet->getActiveSheet()->setCellValue('D'.$exlRow, $mislearnerCompletedTillDate + $surveyData['how_many_learners_you_have_trained']);
				$spreadsheet->getActiveSheet()->setCellValue('E'.$exlRow, $incomeFromLearnerFee + $surveyData['monthly_income_learners_fee']);
				$spreadsheet->getActiveSheet()->setCellValue('F'.$exlRow, $incomeFromJobwork + $surveyData['monthly_income_stitching']);
				$spreadsheet->getActiveSheet()->setCellValue('G'.$exlRow, $incomeFromRepairing + $surveyData['income_from_sewing_machine_repairing']);
				$spreadsheet->getActiveSheet()->setCellValue('H'.$exlRow, $totalIncome + $surveyData['monthly_income_from_silai_schools']);
				$exlRow++;
			}
			$filename = "Consolidated-MIS-Report";
			header("Content-Type: application/xls");    
			header("Content-Disposition: attachment; filename=$filename.xls");  
			header("Pragma: no-cache"); 
			header("Expires: 0");
			$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xls");
			$writer->save("php://output");
			
		} catch(Exception $e) {

		}
	}


}

