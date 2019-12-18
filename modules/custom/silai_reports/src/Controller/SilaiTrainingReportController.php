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
class SilaiTrainingReportController extends ControllerBase {
	
	public function silaiTrainingTraineeFeedbackReport(){
		$action = $_REQUEST['action'];

		# Start filter form
		$optionsArr['action'] = 'export';        
		$form = [];
		$form['export_link'] = [
			'#title' => $this->t('Export'),
			'#type'  => 'link',
			'#url' => Url::fromRoute('silai_reports.silai_training_trainee_feedback_report', $optionsArr),
			HASH_ATTRIBUTES => [CLASS_CONST => ['export-link']]
		];
		# END filter form

		$header = [
			'training_id' => t('Training Id'),
			'trainee_school_code' => t('School Code'),
			'trainee_name' => t('Trainee Name'),
			'trainee_contact' => t('Contact Number'),
			'trainee_email' => t('Email'),
			'trainee_remark' => t('Remark'),
			'Communication' => t('Communication'),
			'Entrepreneurship' => t('Entrepreneurship'),
			'Creativity' => t('Creativity'),
			'Problem_Solving' => t('Problem Solving'),
			'Confidence' => t('Confidence'),
			'Leadership_Quality' => t('Leadership Quality'),
			'Functional_Numeracy' => t('Functional Numeracy'),
			'Measurement_Skills' => t('Measurement Skills'),
			'Drafting' => t('Drafting'),
			'Pattern_Making' => t('Pattern Making'),
			'Cutting' => t('Cutting'),
			'Quality_of_Stitching' => t('Quality of Stitching'),
			'Embroidery_Skills' => t('Embroidery Skills'),
			'Willing_to_learn_new_things' => t('Willing to learn new things'),
			'Willingness_to_travel_within_district' => t('Willingness to travel within district'),
			'Average_Rating' => t('Average Rating'),
		];

		$output = [];
		if($action && $action == 'export') {
			//die;
			$excelReport = $this->trainingTraineeFeedbackCSV();
			exit;
		} else {
			$connection = Database::getConnection();
			$check_qry = $connection->select('silai_trainee_feedback', 'n')
			->fields('n', array(
						'nid',
						 'silai_r_communication',
						 'silai_r_entrepreneurship',
						 'silai_r_creativity',
						 'silai_r_problem_solving',
						 'silai_r_confidence',
						 'silai_r_leadership_quality',
						 'silai_r_functional_numeracy',
						 'silai_r_measurement_skills',
						 'silai_r_drafting',
						 'silai_r_pattern_making',
						 'silai_r_cutting',
						 'silai_r_quality_of_stitching',
						 'silai_r_embroidery_skills',
						 'silai_r_willing_learn_new_things',
						 'silai_r_willingness_travel_within_district',
						 'silai_r_average_rating',
					)
			);
			$check_data = $check_qry->execute();
			$rows = $check_data->fetchAll(\PDO::FETCH_OBJ);
			foreach($rows as $row){
				$traineeData = Node::load($row->nid);
				$trainingData = Node::load($traineeData->field_silai_trainer_id->value);
				if(!empty($trainingData)){
					$output[$row->nid]['training_id'] = $traineeData->field_silai_trainer_id->value;
					$output[$row->nid]['trainee_school_code'] = Node::load($traineeData->field_training_school_code->target_id)->field_school_code->value;
					$output[$row->nid]['trainee_name'] = $traineeData->getTitle();
					$output[$row->nid]['trainee_contact'] = $traineeData->field_silai_contact_number->value;
					$output[$row->nid]['trainee_email'] = $traineeData->field_silai_email_id->value;
					$output[$row->nid]['trainee_remark'] = $traineeData->field_silai_remark->value;
					$output[$row->nid]['Communication'] = $row->silai_r_communication;
					$output[$row->nid]['Entrepreneurship'] = $row->silai_r_entrepreneurship;
					$output[$row->nid]['Creativity'] = $row->silai_r_creativity;
					$output[$row->nid]['Problem_Solving'] = $row->silai_r_problem_solving;
					$output[$row->nid]['Confidence'] = $row->silai_r_confidence;
					$output[$row->nid]['Leadership_Quality'] = $row->silai_r_leadership_quality;
					$output[$row->nid]['Functional_Numeracy'] = $row->silai_r_functional_numeracy;
					$output[$row->nid]['Measurement_Skills'] = $row->silai_r_measurement_skills;
					$output[$row->nid]['Drafting'] = $row->silai_r_drafting;
					$output[$row->nid]['Pattern_Making'] = $row->silai_r_pattern_making;
					$output[$row->nid]['Cutting'] = $row->silai_r_cutting;
					$output[$row->nid]['Quality_of_Stitching'] = $row->silai_r_quality_of_stitching;
					$output[$row->nid]['Embroidery_Skills'] = $row->silai_r_embroidery_skills;
					$output[$row->nid]['Willing_to_learn_new_things'] = $row->silai_r_willing_learn_new_things;
					$output[$row->nid]['Willingness_to_travel_within_district'] = $row->silai_r_willingness_travel_within_district;
					$output[$row->nid]['Average_Rating'] = $row->silai_r_average_rating;
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

	function trainingTraineeFeedbackCSV() {
		//die('hello');
		try {

			$LocationIds = ($LocationIds) ? $LocationIds : [];
			$fromDate = ($requestData['from_date']) ? date('d/m/Y', strtotime($requestData['from_date'])) : '';
			$toDate = ($requestData['to_date']) ? date('d/m/Y', strtotime($requestData['to_date'])) : '';

			$spreadsheet = new Spreadsheet();
			$activeSheet = $spreadsheet->getActiveSheet();
			$spreadsheet->getActiveSheet()->setCellValue('A1', 'Training Id')
			->setCellValue('B1', 'School Code')
			->setCellValue('C1', 'Trainee Name')
			->setCellValue('D1', 'Contact Number')
			->setCellValue('E1', 'Email')
			->setCellValue('F1', 'Remark')
			->setCellValue('G1', 'Communication')
			->setCellValue('H1', 'Entrepreneurship')
			->setCellValue('I1', 'Creativity')
			->setCellValue('J1', 'Problem Solving')
			->setCellValue('K1', 'Confidence')
			->setCellValue('L1', 'Leadership Quality')
			->setCellValue('M1', 'Functional Numeracy')
			->setCellValue('N1', 'Measurement Skills')
			->setCellValue('O1', 'Drafting')
			->setCellValue('P1', 'Pattern Making')
			->setCellValue('Q1', 'Cutting')
			->setCellValue('R1', 'Quality of Stitching')
			->setCellValue('S1', 'Embroidery Skills')
			->setCellValue('T1', 'Willing to learn new things')
			->setCellValue('U1', 'Willingness to travel within district')
			->setCellValue('V1', 'Average Rating');

			$connection = Database::getConnection();
			$check_qry = $connection->select('silai_trainee_feedback', 'n')
			->fields('n', array(
						'nid',
						 'silai_r_communication',
						 'silai_r_entrepreneurship',
						 'silai_r_creativity',
						 'silai_r_problem_solving',
						 'silai_r_confidence',
						 'silai_r_leadership_quality',
						 'silai_r_functional_numeracy',
						 'silai_r_measurement_skills',
						 'silai_r_drafting',
						 'silai_r_pattern_making',
						 'silai_r_cutting',
						 'silai_r_quality_of_stitching',
						 'silai_r_embroidery_skills',
						 'silai_r_willing_learn_new_things',
						 'silai_r_willingness_travel_within_district',
						 'silai_r_average_rating',
					)
			);
			$check_data = $check_qry->execute();
			$rows = $check_data->fetchAll(\PDO::FETCH_OBJ);
			$exlRow = 2;
			foreach($rows as $row){
				$traineeData = Node::load($row->nid);
				$trainingData = Node::load($traineeData->field_silai_trainer_id->value);
				if(!empty($trainingData)){
					$spreadsheet->getActiveSheet()->setCellValue('A'.$exlRow, $traineeData->field_silai_trainer_id->value);
					$spreadsheet->getActiveSheet()->setCellValue('B'.$exlRow, Node::load($traineeData->field_training_school_code->target_id)->field_school_code->value);
					$spreadsheet->getActiveSheet()->setCellValue('C'.$exlRow, $traineeData->getTitle());
					$spreadsheet->getActiveSheet()->setCellValue('D'.$exlRow, $traineeData->field_silai_contact_number->value);
					$spreadsheet->getActiveSheet()->setCellValue('E'.$exlRow, $traineeData->field_silai_email_id->value);
					$spreadsheet->getActiveSheet()->setCellValue('F'.$exlRow, $traineeData->field_silai_remark->value);
					$spreadsheet->getActiveSheet()->setCellValue('G'.$exlRow, $row->silai_r_communication);
					$spreadsheet->getActiveSheet()->setCellValue('H'.$exlRow, $row->silai_r_entrepreneurship);
					$spreadsheet->getActiveSheet()->setCellValue('I'.$exlRow, $row->silai_r_creativity);
					$spreadsheet->getActiveSheet()->setCellValue('J'.$exlRow, $row->silai_r_problem_solving);
					$spreadsheet->getActiveSheet()->setCellValue('K'.$exlRow, $row->silai_r_confidence);
					$spreadsheet->getActiveSheet()->setCellValue('L'.$exlRow, $row->silai_r_leadership_quality);
					$spreadsheet->getActiveSheet()->setCellValue('M'.$exlRow, $row->silai_r_functional_numeracy);
					$spreadsheet->getActiveSheet()->setCellValue('N'.$exlRow, $row->silai_r_measurement_skills);
					$spreadsheet->getActiveSheet()->setCellValue('O'.$exlRow, $row->silai_r_drafting);
					$spreadsheet->getActiveSheet()->setCellValue('P'.$exlRow, $row->silai_r_pattern_making);
					$spreadsheet->getActiveSheet()->setCellValue('Q'.$exlRow, $row->silai_r_cutting);
					$spreadsheet->getActiveSheet()->setCellValue('R'.$exlRow, $row->silai_r_quality_of_stitching);
					$spreadsheet->getActiveSheet()->setCellValue('S'.$exlRow, $row->silai_r_embroidery_skills);
					$spreadsheet->getActiveSheet()->setCellValue('T'.$exlRow, $row->silai_r_willing_learn_new_things);
					$spreadsheet->getActiveSheet()->setCellValue('U'.$exlRow, $row->silai_r_willingness_travel_within_district);
					$spreadsheet->getActiveSheet()->setCellValue('V'.$exlRow, $row->silai_r_average_rating);
					
					$exlRow++;
				}
			}
			$filename = "Training-Trainee-Feedback-Report";
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

