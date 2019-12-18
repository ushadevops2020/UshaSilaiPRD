<?php

namespace Drupal\silai\Form; 

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class SchoolSurveyQuetionnaire extends FormBase {


	public function getFormId() {
		return 'silai_custom_school_survey_quetionnaire';
	}

	public function buildForm(array $form, FormStateInterface $form_state) {
		$nid = $form_state->getBuildInfo()['args'][0];
		if(!empty($nid)){
			$node = \Drupal::routeMatch()->getParameter(NODE);
		    $conn = Database::getConnection();
		    $query = $conn->select(TABLE_SILAI_ADD_SCHOOL_DATA, 's')
		            ->condition(NID, $nid)
		            ->fields('s');
		    $school_data = $query->execute()->fetchAssoc();
		}
		
		$cancelRedirectURI = drupal_get_destination();
		$form[HASH_TITLE] = 'Survey Quetionnaire';
		# Basic Information 

	    $form[STATUS_DATE] = array(
	        HASH_TYPE => DATEFIELD,
	        HASH_TITLE => t('Status date (MM/DD/YY)'),
	        HASH_REQUIRED => TRUE,
	        HASH_DEFAULT_VALUE => ($school_data[STATUS_DATE]) ? $school_data[STATUS_DATE] : '',
	        HASH_ATTRIBUTES => [CLASS_CONST => array(STATUS_DATE), 'tabindex' => 1],
	    );
	    $form[PIN_CODE] = array(
	        HASH_TYPE => TEXTFIELD,
	        HASH_TITLE => t('Pin Code'),
	        HASH_REQUIRED => FALSE,
	        HASH_MAXLENGTH => 7,
	        HASH_DEFAULT_VALUE => ($school_data[PIN_CODE]) ? $school_data[PIN_CODE] : '',
	        HASH_ATTRIBUTES => [CLASS_CONST => [ONLY_NUMERIC_VALUE], 'tabindex' => 2],
	    );
	    $form[MARITAL_STATUS] = array(
	        HASH_TYPE => SELECTFIELD,
	        HASH_TITLE => t('Marital Status'),
	        HASH_OPTIONS => MARITAL_STATUS_OPTIONS, 
	        HASH_REQUIRED => TRUE,
	        HASH_DEFAULT_VALUE => ($school_data[MARITAL_STATUS]) ? $school_data[MARITAL_STATUS] : '',
	        HASH_ATTRIBUTES => [CLASS_CONST => array(MARITAL_STATUS), 'tabindex' => 3],
	    );
	    $form[RELIGION] = array(
	        HASH_TYPE => SELECTFIELD,
	        HASH_TITLE => t('Religion'),
	        HASH_OPTIONS => RELIGION_OPTIONS,
	        HASH_REQUIRED => TRUE,
	        HASH_DEFAULT_VALUE => ($school_data[RELIGION]) ? $school_data[RELIGION] : '',
	        HASH_ATTRIBUTES => [CLASS_CONST => array(RELIGION), 'tabindex' => 4],
	    );
	    $form['cast'] = array(
	        HASH_TYPE => TEXTFIELD,
	        HASH_TITLE => t('Caste'),
	        HASH_REQUIRED => FALSE,
	        HASH_DEFAULT_VALUE => ($school_data['cast']) ? $school_data['cast'] : '',
	        HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC], 'tabindex' => 5],
	    );
	    $form[CAST_CATEGORY] = array(
			HASH_TYPE => SELECTFIELD,
			HASH_TITLE => t('Caste Category'),
			HASH_OPTIONS => CAST_CATEGORY_OPTIONS,
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data[CAST_CATEGORY]) ? $school_data[CAST_CATEGORY] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => array(CAST_CATEGORY), 'tabindex' => 6],
		);
		$form[ANY_SPECIAL_CATEGORY] = array(
			HASH_TYPE => SELECTFIELD,
			HASH_TITLE => t('Any Special category/Community'),
			HASH_OPTIONS => ANY_SPECIAL_CATEGORY_OPTIONS,
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data[ANY_SPECIAL_CATEGORY]) ? $school_data[ANY_SPECIAL_CATEGORY] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => array(ANY_SPECIAL_CATEGORY), 'tabindex' => 7],
		);
		$form[FATHER_NAME] = array(
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t("Husband/Father's Name/Mother's Name"),
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data[FATHER_NAME]) ? $school_data[FATHER_NAME] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC], 'tabindex' => 8],
		);
		$form[FATHER_OCCUPATION] = array(
			HASH_TYPE => SELECTFIELD,
			HASH_TITLE => t("Occupation of Husband/Father/Mother's Name"),
			HASH_OPTIONS => FATHER_OCCUPATION_OPTIONS,
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data[FATHER_OCCUPATION]) ? $school_data[FATHER_OCCUPATION] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => array(FATHER_OCCUPATION), 'tabindex' => 9],
		);
		$form[FAMILY_INCOME] = array(
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t("Total family income per month"),
			HASH_REQUIRED => FALSE,
			HASH_MAXLENGTH => 10,
			HASH_DEFAULT_VALUE => ($school_data[FAMILY_INCOME]) ? $school_data[FAMILY_INCOME] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => array(FAMILY_INCOME), 'tabindex' => 10],
		);

		$form[GROUP][BEFORE_SILAI_SCHOOL_HOUSEHOLD][HASH_TREE] = TRUE;
		$form[GROUP][BEFORE_SILAI_SCHOOL_HOUSEHOLD][HASH_PREFIX] = '<div id="before_household_section"><a class= "add-row-btn" id="before_add_household_row">Add Row</a>&nbsp; | &nbsp;<a class= "delete-row-btn" id="before_delete_household_row">Delete Row</a><div id="before_household_row_0">';
    	$form[GROUP][BEFORE_SILAI_SCHOOL_HOUSEHOLD][HASH_SUFFIX] = '</div><div id="before_household_row_1"></div></div>';
    	$before_school_households = explode( ', ', $school_data[BEFORE_SILAI_SCHOOL_HOUSEHOLD]);
    	foreach($before_school_households as $before_school_household){
    		$form[GROUP][BEFORE_SILAI_SCHOOL_HOUSEHOLD][] = array(
				HASH_TYPE => SELECTFIELD,
				HASH_TITLE => t('Before opening of Silai school household core'),
				HASH_OPTIONS => SILAI_SCHOOL_HOUSEHOLD_OPTIONS,
				HASH_REQUIRED => FALSE,
				HASH_DEFAULT_VALUE => $before_school_household,
				HASH_ATTRIBUTES => ['tabindex' => 13],
			);
    	}
		$form[GROUP][AFTER_SILAI_SCHOOL_HOUSEHOLD][HASH_TREE] = TRUE;
		$form[GROUP][AFTER_SILAI_SCHOOL_HOUSEHOLD][HASH_PREFIX] = '<div id="after_household_section"><a class= "add-row-btn" id="after_add_household_row">Add Row</a>&nbsp; | &nbsp;<a class= "delete-row-btn" id="after_delete_household_row">Delete Row</a><div id="after_household_row_0">';
    	$form[GROUP][AFTER_SILAI_SCHOOL_HOUSEHOLD][HASH_SUFFIX] = '</div><div id="after_household_row_1"></div></div>';
    	$after_school_households = explode( ', ', $school_data[AFTER_SILAI_SCHOOL_HOUSEHOLD]);
    	foreach($after_school_households as $after_school_household){
    		$form[GROUP][AFTER_SILAI_SCHOOL_HOUSEHOLD][] = array(
				HASH_TYPE => SELECTFIELD,
				HASH_TITLE => t('After opening of Silai school household core'),
				HASH_OPTIONS => SILAI_SCHOOL_HOUSEHOLD_OPTIONS,
				HASH_REQUIRED => FALSE,
				HASH_DEFAULT_VALUE => $after_school_household,
				HASH_ATTRIBUTES => ['tabindex' => 13],
			);
    	}
	    $form[GROUP][TOTAL_BOYS] = array(
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t('Total Boys'),
			HASH_REQUIRED => FALSE,
			HASH_MAXLENGTH => 3,
			HASH_DEFAULT_VALUE => ($school_data[TOTAL_BOYS]) ? $school_data[TOTAL_BOYS] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => [ONLY_NUMERIC_VALUE], 'tabindex' => 11],
		);
		$form[GROUP][TOTAL_GIRLS] = array(
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t('Total Girls'),
			HASH_REQUIRED => FALSE,
			HASH_MAXLENGTH => 3,
			HASH_DEFAULT_VALUE => ($school_data[TOTAL_GIRLS]) ? $school_data[TOTAL_GIRLS] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => [ONLY_NUMERIC_VALUE], 'tabindex' => 12],
		);

		#Query get children data for coustom table
		$conn = Database::getConnection();
	    $child_query = $conn->select(TABLE_SILAI_ADD_SCHOOL_CHILDREN_DATA, 's')
	            ->condition(NID, $nid)
	            ->fields('s');
	    $child_datas = $child_query->execute()->fetchAll(\PDO::FETCH_OBJ);
	    $child_row = count($child_datas);

	    $form[CHILDREN_DETAILS][HASH_TREE] = TRUE;
	    $form[CHILDREN_DETAILS][HASH_PREFIX] = '<div id="children_section"><a class= "add-row-btn" id="add_children_row">Add Row</a>&nbsp; | &nbsp;<a class= "delete-row-btn" id="delete_children_row">Delete Row</a><div class="children_row_data">';
    	$form[CHILDREN_DETAILS][HASH_SUFFIX] = '</div><div id="children_row"></div></div>';
    	$i = 0;
    	if(!empty($child_row)){
    		foreach ($child_datas as $child_data) {
		    	$form[CHILDREN_DETAILS][$i][CHILDREN_NAME][$i] = array(
					HASH_TYPE => TEXTFIELD,
					HASH_TITLE => t("Children Name"),
					HASH_REQUIRED => FALSE,
					HASH_MAXLENGTH => 30,
					HASH_DEFAULT_VALUE => $child_data->children_name,
					HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC], CLASS_CONST => array(CHILDREN_NAME)],
					HASH_PREFIX => '<div id="new_child_row'.$i.'">',
				);
				$form[CHILDREN_DETAILS][$i][CHILDREN_GENDER][$i] = array(
					HASH_TYPE => SELECTFIELD,
			        HASH_TITLE => t('Children Gender'),
			        HASH_OPTIONS => GENDER_OPTIONS, 
			        HASH_REQUIRED => FALSE,
			        HASH_DEFAULT_VALUE => $child_data->children_gender,
			        HASH_ATTRIBUTES => [CLASS_CONST => array(CHILDREN_GENDER)],
				);
				$form[CHILDREN_DETAILS][$i][CHILDREN_AGE][$i] = array(
					HASH_TYPE => TEXTFIELD,
			        HASH_TITLE => t('Children Age'),
			        HASH_REQUIRED => FALSE,
			        HASH_MAXLENGTH => 3,
			        HASH_DEFAULT_VALUE => $child_data->children_age,
			        HASH_ATTRIBUTES => [CLASS_CONST => [ONLY_NUMERIC_VALUE]],
				);
				$form[CHILDREN_DETAILS][$i][CHILDREN_EDUCATION_LEVEL][$i] = array(
					HASH_TYPE => SELECTFIELD,
					HASH_TITLE => t('Children Education Level'),
					HASH_OPTIONS => CHILDREN_EDUCATION_LEVEL_OPTIONS,
					HASH_REQUIRED => FALSE,
					HASH_DEFAULT_VALUE => $child_data->children_education_level,
					HASH_ATTRIBUTES => [CLASS_CONST => array(CHILDREN_EDUCATION_LEVEL)],
				);
				$form[CHILDREN_DETAILS][$i][CHILDREN_SCHOOL_TYPE][$i] = array(
					HASH_TYPE => RADIOSFIELD,
					HASH_TITLE => t('School/College type'),
					HASH_OPTIONS => array( 
								0 => t('Government'),
								1 => t('Private'),
							),
					HASH_REQUIRED => FALSE,
					HASH_DEFAULT_VALUE => $child_data->children_school_type,
					HASH_ATTRIBUTES => [CLASS_CONST => array(CHILDREN_SCHOOL_TYPE)],
				);
				$form[CHILDREN_DETAILS][$i][CHILDREN_MONTHLY_EXPENSE_OVER_EDUCATION][$i] = array(
					HASH_TYPE => TEXTFIELD,
			        HASH_TITLE => t('Monthly expense over education per month'),
			        HASH_REQUIRED => FALSE,
			        HASH_MAXLENGTH => 10,
			        HASH_DEFAULT_VALUE => $child_data->children_monthly_expense_over_education,
			        HASH_ATTRIBUTES => [CLASS_CONST => [ONLY_NUMERIC_VALUE]],
			        HASH_SUFFIX => '</div>',
				);
				$i++;
		    }
    	}else{
	    	$form[CHILDREN_DETAILS][$i][CHILDREN_NAME][$i] = array(
				HASH_TYPE => TEXTFIELD,
				HASH_TITLE => t("Children Name"),
				HASH_REQUIRED => FALSE,
				HASH_DEFAULT_VALUE =>'',
				HASH_MAXLENGTH => 30,
				HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC], CLASS_CONST => array(CHILDREN_NAME)],
			);
			$form[CHILDREN_DETAILS][$i][CHILDREN_GENDER][$i] = array(
				HASH_TYPE => SELECTFIELD,
		        HASH_TITLE => t('Children Gender'),
		        HASH_OPTIONS => GENDER_OPTIONS,
		        HASH_REQUIRED => FALSE,
		        HASH_DEFAULT_VALUE => '',
		        HASH_ATTRIBUTES => [CLASS_CONST => array(CHILDREN_GENDER)],
			);
			$form[CHILDREN_DETAILS][$i][CHILDREN_AGE][$i] = array(
				HASH_TYPE => TEXTFIELD,
		        HASH_TITLE => t('Children Age'),
		        HASH_REQUIRED => FALSE,
		        HASH_DEFAULT_VALUE => '',
		        HASH_MAXLENGTH => 3,
		        HASH_ATTRIBUTES => [CLASS_CONST => [ONLY_NUMERIC_VALUE]],
			);
			$form[CHILDREN_DETAILS][$i][CHILDREN_EDUCATION_LEVEL][$i] = array(
				HASH_TYPE => SELECTFIELD,
				HASH_TITLE => t('Children Education Level'),
				HASH_OPTIONS => CHILDREN_EDUCATION_LEVEL_OPTIONS,
				HASH_REQUIRED => FALSE,
				HASH_DEFAULT_VALUE => '',
				HASH_ATTRIBUTES => [CLASS_CONST => array(CHILDREN_EDUCATION_LEVEL)],
			);
			$form[CHILDREN_DETAILS][$i][CHILDREN_SCHOOL_TYPE][$i] = array(
				HASH_TYPE => RADIOSFIELD,
				HASH_TITLE => t('School/College type'),
				HASH_OPTIONS => array( 
							0 => t('Government'),
							1 => t('Private'),
						),
				HASH_REQUIRED => FALSE,
				HASH_DEFAULT_VALUE => 0,
				HASH_ATTRIBUTES => [CLASS_CONST => array(CHILDREN_SCHOOL_TYPE)],
			);
			$form[CHILDREN_DETAILS][$i][CHILDREN_MONTHLY_EXPENSE_OVER_EDUCATION][$i] = array(
				HASH_TYPE => TEXTFIELD,
		        HASH_TITLE => t('Monthly expense over education per month'),
		        HASH_REQUIRED => FALSE,
		        HASH_DEFAULT_VALUE => '',
		        HASH_MAXLENGTH => 10,
		        HASH_ATTRIBUTES => [CLASS_CONST => [ONLY_NUMERIC_VALUE]],
		        HASH_SUFFIX => '</div>',
			);
    	}

		$form[USE_MOBILE_PHONE] = array(
			HASH_TYPE => RADIOSFIELD,
			HASH_TITLE => t('Do you use a Mobile Phone'),
			HASH_OPTIONS => RADIOSFIELD_NO_YES_OPTIONS,
			HASH_REQUIRED => TRUE,
			HASH_MAXLENGTH => 11,
			HASH_DEFAULT_VALUE => ($school_data[USE_MOBILE_PHONE]) ? $school_data[USE_MOBILE_PHONE] : 0,
			HASH_ATTRIBUTES => [CLASS_CONST => array(USE_MOBILE_PHONE)],
		);
		$form[ALTERNATIVE_MOBILE_NUMBER] = array(
			HASH_TYPE => NUMBERFIELD,
			HASH_TITLE => t('Alternative Mobile Number'),
			HASH_REQUIRED => FALSE,
			HASH_MAXLENGTH => 11,
			HASH_DEFAULT_VALUE => ($school_data[ALTERNATIVE_MOBILE_NUMBER]) ? $school_data[ALTERNATIVE_MOBILE_NUMBER] : '',
			HASH_STATES => array(
			    VISIBLE => array(
			        ':input[name="use_mobile_phone"]' => array('value' => 1),
			    ),
			    REQUIRED => array(
			        ':input[name="use_mobile_phone"]' => array('value' => 1),
			    ),
		    ),
			HASH_ATTRIBUTES => [CLASS_CONST => array(ALTERNATIVE_MOBILE_NUMBER)],
		);
		$form[TYPE_OF_MOBILE_PHONE] = array(
			HASH_TYPE => SELECTFIELD,
			HASH_TITLE => t('Type of Mobile Phone'),
			HASH_OPTIONS => TYPE_OF_MOBILE_PHONE_OPTIONS,
			HASH_DEFAULT_VALUE => ($school_data[TYPE_OF_MOBILE_PHONE]) ? $school_data[TYPE_OF_MOBILE_PHONE] : '',
			HASH_STATES => array(
			    VISIBLE => array(
			        ':input[name="use_mobile_phone"]' => array('value' => 1),
			    ),
			    REQUIRED => array(
			        ':input[name="use_mobile_phone"]' => array('value' => 1),
			    ),
		    ),
			HASH_ATTRIBUTES => [CLASS_CONST => array(TYPE_OF_MOBILE_PHONE)],
		);
		$form[USE_INTERNET] = array(
			HASH_TYPE => RADIOSFIELD,
			HASH_TITLE => t('Do you use Internet?'),
			HASH_OPTIONS => RADIOSFIELD_NO_YES_OPTIONS,
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data[USE_INTERNET]) ? $school_data[USE_INTERNET] : 0,
			HASH_ATTRIBUTES => [CLASS_CONST => array(USE_INTERNET)],
		);
		$form[USE_WHATSAPP] = array(
			HASH_TYPE => RADIOSFIELD,
			HASH_TITLE => t('Do you see WhatsApp?'),
			HASH_OPTIONS => RADIOSFIELD_NO_YES_OPTIONS,
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data[USE_WHATSAPP]) ? $school_data[USE_WHATSAPP] : 0,
			HASH_ATTRIBUTES => [CLASS_CONST => array(USE_WHATSAPP)],
		);
		$form[HAVE_EMAIL_ID] = array(
			HASH_TYPE => RADIOSFIELD,
			HASH_TITLE => t('Do you have e-mail id?'),
			HASH_OPTIONS => RADIOSFIELD_NO_YES_OPTIONS,
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data[HAVE_EMAIL_ID]) ? $school_data[HAVE_EMAIL_ID] : 0,
			HASH_ATTRIBUTES => [CLASS_CONST => array(HAVE_EMAIL_ID)],
		);
		$form[MENTION_EMAIL] = array(
			HASH_TYPE => 'email',
			HASH_TITLE => t('If yes (please mention email address)'),
			HASH_REQUIRED => FALSE,
			HASH_MAXLENGTH => 55,
			HASH_DEFAULT_VALUE => ($school_data[MENTION_EMAIL]) ? $school_data[MENTION_EMAIL] : '',
			HASH_STATES => array(
			    VISIBLE => array(
			        ':input[name="have_email_id"]' => array('value' => 1),
			    ),
			    REQUIRED => array(
			        ':input[name="have_email_id"]' => array('value' => 1),
			    ),
		    ),
			HASH_ATTRIBUTES => [CLASS_CONST => array(MENTION_EMAIL)],
		);
		$form[COMPUTER_IN_FAMILY] = array(
			HASH_TYPE => RADIOSFIELD,
			HASH_TITLE => t('Computer/Laptop in family'),
			HASH_OPTIONS => RADIOSFIELD_NO_YES_OPTIONS,
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data[COMPUTER_IN_FAMILY]) ? $school_data[COMPUTER_IN_FAMILY] : 0,
			HASH_ATTRIBUTES => [CLASS_CONST => array(COMPUTER_IN_FAMILY)],
		);
		$form[RATION_CARD] = array(
			HASH_TYPE => RADIOSFIELD,
			HASH_TITLE => t('Does the family has Ration Card?'),
			HASH_OPTIONS => RADIOSFIELD_NO_YES_OPTIONS,
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data[RATION_CARD]) ? $school_data[RATION_CARD] : 0,
			HASH_ATTRIBUTES => [CLASS_CONST => array(RATION_CARD)],
		);
		$form[TYPE_OF_RATION_CARD] = array(
			HASH_TYPE => SELECTFIELD,
			HASH_TITLE => t('Type of Ration Card'),
			HASH_OPTIONS => TYPE_OF_RATION_CARD_OPTIONS,
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data[TYPE_OF_RATION_CARD]) ? $school_data[TYPE_OF_RATION_CARD] : '',
			HASH_STATES => array(
			    VISIBLE => array(
			        ':input[name="ration_card"]' => array('value' => 1),
			    ),
			    REQUIRED => array(
			        ':input[name="ration_card"]' => array('value' => 1),
			    ),
		    ),
			HASH_ATTRIBUTES => [CLASS_CONST => array(TYPE_OF_RATION_CARD)],
		);
		$form[HAVE_BANK_ACCOUNT] = array(
			HASH_TYPE => RADIOSFIELD,
			HASH_TITLE => t('Do you have a Bank Account?'),
			HASH_OPTIONS => RADIOSFIELD_NO_YES_OPTIONS,
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data[HAVE_BANK_ACCOUNT]) ? $school_data[HAVE_BANK_ACCOUNT] : 0,
			HASH_ATTRIBUTES => [CLASS_CONST => array(HAVE_BANK_ACCOUNT)],
		);
		$form[BANK_NAME] = array(
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t("Bank Name"),
			HASH_REQUIRED => FALSE,
			HASH_MAXLENGTH => 55,
			HASH_DEFAULT_VALUE => ($school_data[BANK_NAME]) ? $school_data[BANK_NAME] : '',
			HASH_STATES => array(
			    VISIBLE => array(
			        ':input[name="have_bank_account"]' => array('value' => 1),
			    ),
			    REQUIRED => array(
			        ':input[name="have_bank_account"]' => array('value' => 1),
			    ),
		    ),
			HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
		);
		$form[HAVE_PAN_CARD] = array(
			HASH_TYPE => RADIOSFIELD,
			HASH_TITLE => t('Do you have a PAN Card?'),
			HASH_OPTIONS => RADIOSFIELD_NO_YES_OPTIONS,
			HASH_REQUIRED => TRUE,
			HASH_DEFAULT_VALUE => ($school_data[HAVE_PAN_CARD]) ? $school_data[HAVE_PAN_CARD] : 0,
			HASH_ATTRIBUTES => [CLASS_CONST => array(HAVE_PAN_CARD)],
		);
		$form[HAVE_AADHAR_CARD] = array(
			HASH_TYPE => RADIOSFIELD,
			HASH_TITLE => t('Do you have an Aadhar Card?'),
			HASH_OPTIONS => RADIOSFIELD_NO_YES_OPTIONS,
			HASH_REQUIRED => TRUE,
			HASH_DEFAULT_VALUE => ($school_data[HAVE_AADHAR_CARD]) ? $school_data[HAVE_AADHAR_CARD] : 0,
			HASH_ATTRIBUTES => [CLASS_CONST => array(HAVE_AADHAR_CARD)],
		);
		$form[AADHAR_NUMBER] = array(
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t("If yes (please mention Aadhar number)"),
			//HASH_REQUIRED => TRUE,
			HASH_MAXLENGTH => 12,
			HASH_DEFAULT_VALUE => ($school_data[AADHAR_NUMBER]) ? $school_data[AADHAR_NUMBER] : '',
			HASH_STATES => array(
			    VISIBLE => array(
			        ':input[name="have_aadhar_card"]' => array('value' => 1),
			    ),
			    REQUIRED => array(
			        ':input[name="have_aadhar_card"]' => array('value' => 1),
			    ),
		    ),
			HASH_ATTRIBUTES => [CLASS_CONST => [ONLY_NUMERIC_VALUE]],
		);
		$form[HAVE_GAS_CONNECTION] = array(
			HASH_TYPE => RADIOSFIELD,
			HASH_TITLE => t('Do you have a Gas Connection?'),
			HASH_OPTIONS => RADIOSFIELD_NO_YES_OPTIONS,
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data[HAVE_GAS_CONNECTION]) ? $school_data[HAVE_GAS_CONNECTION] : 0,
			HASH_ATTRIBUTES => [CLASS_CONST => array(HAVE_GAS_CONNECTION)],
		);
		$form[ASSOCIATED_WITH_ANY_MFI] = array(
			HASH_TYPE => RADIOSFIELD,
			HASH_TITLE => t('Are you associated with any MFI (Micro Finance Institute)?'),
			HASH_OPTIONS => RADIOSFIELD_NO_YES_OPTIONS,
			HASH_REQUIRED => TRUE,
			HASH_DEFAULT_VALUE => ($school_data[ASSOCIATED_WITH_ANY_MFI]) ? $school_data[ASSOCIATED_WITH_ANY_MFI] : 0,
			HASH_ATTRIBUTES => [CLASS_CONST => array(ASSOCIATED_WITH_ANY_MFI)],
		);
		$form[MFI_NUMBER] = array(
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t("If Yes (Provide the MFI name)"),
			HASH_REQUIRED => FALSE,
			HASH_MAXLENGTH => 55,
			HASH_DEFAULT_VALUE => ($school_data[MFI_NUMBER]) ? $school_data[MFI_NUMBER] : '',
			HASH_STATES => array(
			    VISIBLE => array(
			        ':input[name="associated_with_any_mfi"]' => array('value' => 1),
			    ),
		    ),
			HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
		);
		$form[OTHER_MFIS_WORKING_YOUR_AREA] = array(
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t("Provide the name of other MFIs working in your area"),
			HASH_REQUIRED => FALSE,
			HASH_MAXLENGTH => 55,
			HASH_DEFAULT_VALUE => ($school_data[OTHER_MFIS_WORKING_YOUR_AREA]) ? $school_data[OTHER_MFIS_WORKING_YOUR_AREA] : '',
			HASH_STATES => array(
			    VISIBLE => array(
			        ':input[name="associated_with_any_mfi"]' => array('value' => 1),
			    ),
		    ),
			HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
		);
		$form[GETTING_BENEFITTED_GOVERNMENT_SCHEMES] = array(
			HASH_TYPE => RADIOSFIELD,
			HASH_TITLE => t('Are you getting benefitted from any government schemes?'),
			HASH_OPTIONS => RADIOSFIELD_NO_YES_OPTIONS,
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data[GETTING_BENEFITTED_GOVERNMENT_SCHEMES]) ? $school_data[GETTING_BENEFITTED_GOVERNMENT_SCHEMES] : 0,
			HASH_ATTRIBUTES => [CLASS_CONST => array(GETTING_BENEFITTED_GOVERNMENT_SCHEMES)],
		);
		$form[KINDLY_GIVE_GOVERNMENT_BENEFITTED_DETAILS] = array(
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t("If yes, kindly give details"),
			HASH_REQUIRED => FALSE,
			HASH_MAXLENGTH => 100,
			HASH_DEFAULT_VALUE => ($school_data[KINDLY_GIVE_GOVERNMENT_BENEFITTED_DETAILS]) ? $school_data[KINDLY_GIVE_GOVERNMENT_BENEFITTED_DETAILS] : '',
			HASH_STATES => array(
			    VISIBLE => array(
			        ':input[name="getting_benefitted_government_schemes"]' => array('value' => 1),
			    ),
		    ),
			HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
		);
		$form[ELECTRICITY_STATUS_IN_HOME] = array(
			HASH_TYPE => RADIOSFIELD,
			HASH_TITLE => t('Electricity status in Home'),
			HASH_OPTIONS => RADIOSFIELD_NO_YES_OPTIONS,
			HASH_REQUIRED => TRUE,
			HASH_DEFAULT_VALUE => ($school_data[ELECTRICITY_STATUS_IN_HOME]) ? $school_data[ELECTRICITY_STATUS_IN_HOME] : 0,
			HASH_ATTRIBUTES => [CLASS_CONST => array(ELECTRICITY_STATUS_IN_HOME)],
		);
		$form[AVERAGE_ELECTRICITY_HOURS] = array(
			HASH_TYPE => SELECTFIELD,
			HASH_TITLE => t('Average electricity hours per day'),
			HASH_OPTIONS => AVERAGE_ELECTRICITY_HOURS_OPTIONS,
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data[AVERAGE_ELECTRICITY_HOURS]) ? $school_data[AVERAGE_ELECTRICITY_HOURS] : '',
			HASH_STATES => array(
			    VISIBLE => array(
			        ':input[name="electricity_status_in_home"]' => array('value' => 1),
			    ),
		    ),
			HASH_ATTRIBUTES => [CLASS_CONST => array(AVERAGE_ELECTRICITY_HOURS)],
		);
		$form[HAVE_SOLAR_PANEL] = array(
			HASH_TYPE => RADIOSFIELD,
			HASH_TITLE => t('Do you have Solar panel?'),
			HASH_OPTIONS => RADIOSFIELD_NO_YES_OPTIONS,
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data[HAVE_SOLAR_PANEL]) ? $school_data[HAVE_SOLAR_PANEL] : 0,
			HASH_ATTRIBUTES => [CLASS_CONST => array(HAVE_SOLAR_PANEL)],
		);
		$form[USAGE_OF_DRINKING_WATER] = array(
			HASH_TYPE => SELECTFIELD,
			HASH_TITLE => t('Usage of drinking water'),
			HASH_OPTIONS => USAGE_OF_DRINKING_WATER_OPTIONS,
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data[USAGE_OF_DRINKING_WATER]) ? $school_data[USAGE_OF_DRINKING_WATER] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => array(USAGE_OF_DRINKING_WATER)],
		);
		$form[APPLIANCES_IN_THE_HOME] = array(
			HASH_TYPE => SELECTFIELD,
			HASH_TITLE => t('Appliances in the Home'),
			HASH_OPTIONS => APPLIANCES_IN_THE_HOME_OPTIONS,
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data[APPLIANCES_IN_THE_HOME]) ? explode( ', ', $school_data[APPLIANCES_IN_THE_HOME] ) : '',
			'#multiple' => TRUE,
			HASH_ATTRIBUTES => [CLASS_CONST => array(APPLIANCES_IN_THE_HOME)],
		);
		$form[TYPE_OF_YOUR_HOUSE] = array(
			HASH_TYPE => SELECTFIELD,
			HASH_TITLE => t('Type of Your house'),
			HASH_OPTIONS => TYPE_OF_YOUR_HOUSE_OPTIONS,
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data[TYPE_OF_YOUR_HOUSE]) ? $school_data[TYPE_OF_YOUR_HOUSE] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => array(TYPE_OF_YOUR_HOUSE)],
		);
		 $form['completed_training_from_usha'] = array(
			HASH_TYPE => RADIOSFIELD,
			HASH_TITLE => t('Have you completed training from Usha?'),
			HASH_OPTIONS => RADIOSFIELD_NO_YES_OPTIONS,
			HASH_REQUIRED => TRUE,
			HASH_DEFAULT_VALUE => ($school_data['completed_training_from_usha']) ? $school_data['completed_training_from_usha'] : 0,
			HASH_ATTRIBUTES => [CLASS_CONST => array('completed_training_from_usha')],
		);
		$form['if_yes_got_trainined'] = array(
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t("If yes(for how many days, you got trainined)"),
			HASH_MAXLENGTH => 55,
			HASH_DEFAULT_VALUE => ($school_data['if_yes_got_trainined']) ? $school_data['if_yes_got_trainined'] : '',
			HASH_STATES => array(
			    VISIBLE => array(
			        ':input[name="completed_training_from_usha"]' => array('value' => 1),
			    ),
			    REQUIRED => array(
			        ':input[name="completed_training_from_usha"]' => array('value' => 1),
			    ),
		    ),
			HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
		);
		$form['signage_in_the_school'] = array(
			HASH_TYPE => RADIOSFIELD,
			HASH_TITLE => t('Signage in the School'),
			HASH_OPTIONS => RADIOSFIELD_NO_YES_OPTIONS,
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data['signage_in_the_school']) ? $school_data['signage_in_the_school'] : 0,
			HASH_ATTRIBUTES => [CLASS_CONST => array('signage_in_the_school')],
		);
		$form['type_of_signage_present'] = array(
			HASH_TYPE => SELECTFIELD,
			HASH_TITLE => t('Type of signage present in school'),
			HASH_OPTIONS => TYPE_OF_SIGNAGE_PRESENT_OPTIONS,
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data['type_of_signage_present']) ? $school_data['type_of_signage_present'] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => array('type_of_signage_present')],
		);
		$form['condition_of_signboard'] = array(
			HASH_TYPE => SELECTFIELD,
			HASH_TITLE => t('Condition of signboard'),
			HASH_OPTIONS => CONDITION_OF_SIGNBOARD_OPTIONS,
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data['condition_of_signboard']) ? $school_data['condition_of_signboard'] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => array('condition_of_signboard')],
		);
		
		$form['how_many_non_usha_black_machines'] = array(
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t("How many non Usha black machines you have?"),
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data['how_many_non_usha_black_machines']) ? $school_data['how_many_non_usha_black_machines'] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
		);
		$form['usha_black_machine_you_have'] = array(
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t("How many Usha black machines you have?"),
			HASH_REQUIRED => TRUE,
			HASH_DEFAULT_VALUE => ($school_data['usha_black_machine_you_have']) ? $school_data['usha_black_machine_you_have'] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
		);
		$form['model_of_usha_black_machines'] = array(
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t("Model(s) of Usha black machines"),
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data['model_of_usha_black_machines']) ? $school_data['model_of_usha_black_machines'] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
		);
		$form['usha_white_machine_you_have'] = array(
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t("How many Usha white machines you have?"),
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data['usha_white_machine_you_have']) ? $school_data['usha_white_machine_you_have'] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
		);
		$form['model_of_usha_white_machines'] = array(
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t("Model(s) of Usha white machines"),
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data['model_of_usha_white_machines']) ? $school_data['model_of_usha_white_machines'] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
		);
		$form['where_is_the_silai_school'] = array(
			HASH_TYPE => SELECTFIELD,
			HASH_TITLE => t('Where is the silai school situated?'),
			HASH_OPTIONS => WHERE_IS_THE_SILAI_SCHOOL_OPTIONS,
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data['where_is_the_silai_school']) ? $school_data['where_is_the_silai_school'] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => array('where_is_the_silai_school')],
		);
		$form['average_learners_attending'] = array(
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t("Total learners enrolled in school till date"), 
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data['average_learners_attending']) ? $school_data['average_learners_attending'] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
		);
		$form['how_many_learners_you_have_trained'] = array(
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t("How many learners you have trained through Silai school till date?"),
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data['how_many_learners_you_have_trained']) ? $school_data['how_many_learners_you_have_trained'] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
		);
		$form['average_fee_charged'] = array(
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t("Average fee charged per learner"),
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data['average_fee_charged']) ? $school_data['average_fee_charged'] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
		);
		$form['monthly_income_learners_fee'] = array(
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t("Total income from learners fee Till date"),
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data['monthly_income_learners_fee']) ? $school_data['monthly_income_learners_fee'] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
		);
		$form['monthly_income_stitching'] = array(
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t("Total income from stitching till date"),
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data['monthly_income_stitching']) ? $school_data['monthly_income_stitching'] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
		);
		$form['income_from_sewing_machine_repairing'] = array(
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t("Total income from sewing machine repairing till date"),
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data['income_from_sewing_machine_repairing']) ? $school_data['income_from_sewing_machine_repairing'] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
		);
		$form['monthly_income_from_sale_of_dresses'] = array(
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t("Total income from sale of dresses till date"),
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data['monthly_income_from_sale_of_dresses']) ? $school_data['monthly_income_from_sale_of_dresses'] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
		);
		$form['monthly_income_from_silai_schools'] = array(
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t("Consolidated income of teacher till date"),
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data['monthly_income_from_silai_schools']) ? $school_data['monthly_income_from_silai_schools'] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
		);
		$form[STITCHING_RELATED_ORDER_WORK_COMPLETED] = array(
			HASH_TYPE => RADIOSFIELD,
			HASH_TITLE => t('Any stitching related order work completed/taken?'),
			HASH_OPTIONS => RADIOSFIELD_NO_YES_OPTIONS,
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data[STITCHING_RELATED_ORDER_WORK_COMPLETED]) ? $school_data[STITCHING_RELATED_ORDER_WORK_COMPLETED] : 0,
			HASH_ATTRIBUTES => [CLASS_CONST => array(STITCHING_RELATED_ORDER_WORK_COMPLETED)],
		);
		$form['if_yes_order_completed'] = array(
			HASH_TYPE => TEXTFIELD,
			HASH_TITLE => t("If yes (Share details of the order completed/taken)"),
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data['if_yes_order_completed']) ? $school_data['if_yes_order_completed'] : '',
			HASH_STATES => array(
			    VISIBLE => array(
			        ':input[name="stitching_related_order_work_completed"]' => array('value' => 1),
			    ),
			    REQUIRED => array(
			        ':input[name="stitching_related_order_work_completed"]' => array('value' => 1),
			    ),
		    ),
			HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]],
		);
		$form['additional_notes'] = array(
			HASH_TYPE => TEXTAREAFIELD,
			HASH_TITLE => t("Additional notes"),
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data['additional_notes']) ? $school_data['additional_notes'] : '',
			HASH_ATTRIBUTES => [CLASS_CONST => array('additional_notes')],
		);

		$form['aadhar_attachment'] = array(
			HASH_TYPE => FILEFIELD,
			HASH_TITLE => t("Aadhar attachment"),
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data['aadhar_attachment']) ? array($school_data['aadhar_attachment']) : '',
			HASH_ATTRIBUTES => [CLASS_CONST => array('aadhar_attachment')],
			HASH_UPLOAD_LOCATION => 'public://my_files/',
		);
		$form['pan_attachment'] = array(
			HASH_TYPE => FILEFIELD,
			HASH_TITLE => t("Case Study attachment"),
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data['pan_attachment']) ? array($school_data['pan_attachment']) : '',
			HASH_ATTRIBUTES => [CLASS_CONST => array('pan_attachment')],
			HASH_UPLOAD_LOCATION => 'public://my_files/',
		);
		$form['teacher_photograph_attachment'] = array(
			HASH_TYPE => FILEFIELD,
			HASH_TITLE => t("Teacher photograph attachment"),
			HASH_REQUIRED => FALSE,
			HASH_DEFAULT_VALUE => ($school_data['teacher_photograph_attachment']) ? array($school_data['teacher_photograph_attachment']) : '',
			HASH_ATTRIBUTES => [CLASS_CONST => array('teacher_photograph_attachment')],
			HASH_UPLOAD_LOCATION => 'public://my_files/',
		);
		# Cancle and save button   - 
		$form[ACTIONS] = array(HASH_TYPE => ACTIONS);
	    $form[ACTIONS]['cancel'] = array(
	    HASH_TYPE => 'button',
	    HASH_VALUE => t('Cancel'),
	    HASH_WEIGHT => -1,
	    HASH_ATTRIBUTES => array('onClick' => 'window.location.href = "/'.$cancelRedirectURI['destination'].'"; event.preventDefault();'),
	    );
		$form[ACTIONS]['submit'] = array(
			HASH_TYPE => 'submit',
			HASH_VALUE => $this->t('Submit'),
		);
		return $form;
	}
	
	public function validateForm(array &$form, FormStateInterface $form_state) {
		$field = $form_state->getValues();

		if($field['use_mobile_phone'] == 1){
			if(empty($field['alternative_mobile_number'])){
				$message = 'Alternative Mobile Number* Field is required.';
	    		$form_state->setErrorByName('use_mobile_phone', $message);
			}
			if(empty($field['type_of_mobile_phone'])){
				$message = 'Type of Mobile Phone* Field is required.';
	    		$form_state->setErrorByName('type_of_mobile_phone', $message);
			} 
		}
		if($field['have_email_id'] == 1){
			if(empty($field['mention_email'])){
				$message = 'If yes (please mention email address)* Field is required.';
	    		$form_state->setErrorByName('mention_email', $message);
			}
		}
		if($field['ration_card'] == 1){
			if(empty($field['type_of_ration_card'])){
				$message = 'Type of Ration Card* Field is required.';
	    		$form_state->setErrorByName('type_of_ration_card', $message);
			}
		}
		if($field[HAVE_BANK_ACCOUNT] == 1){
			if(empty($field[BANK_NAME])){
				$message = 'Bank Name* Field is required.';
	    		$form_state->setErrorByName(BANK_NAME, $message);
			}
		}
		if($field[HAVE_AADHAR_CARD] == 1){
			if(empty($field[AADHAR_NUMBER])){
				$message = 'If yes (please mention Aadhar number)* Field is required.';
	    		$form_state->setErrorByName(AADHAR_NUMBER, $message);
			}
		}
		if($field[ASSOCIATED_WITH_ANY_MFI] == 1){
			if(empty($field[MFI_NUMBER])){
				$message = 'If Yes (Provide the MFI name)* Field is required.';
	    		$form_state->setErrorByName(MFI_NUMBER, $message);
			}
			if(empty($field[OTHER_MFIS_WORKING_YOUR_AREA])){
				$message = 'Provide the name of other MFIs working in your area* Field is required.';
	    		$form_state->setErrorByName(OTHER_MFIS_WORKING_YOUR_AREA, $message);
			}
		}
		if($field[GETTING_BENEFITTED_GOVERNMENT_SCHEMES] == 1){
			if(empty($field[KINDLY_GIVE_GOVERNMENT_BENEFITTED_DETAILS])){
				$message = 'If yes, kindly give details* Field is required.';
	    		$form_state->setErrorByName(KINDLY_GIVE_GOVERNMENT_BENEFITTED_DETAILS, $message);
			}
		}
		if($field[ELECTRICITY_STATUS_IN_HOME] == 1){
			if(empty($field[AVERAGE_ELECTRICITY_HOURS])){
				$message = 'Average electricity hours per day* Field is required.';
	    		$form_state->setErrorByName(AVERAGE_ELECTRICITY_HOURS, $message);
			}
		}
		if($field['completed_training_from_usha'] == 1){
			if(empty($field['if_yes_got_trainined'])){
				$message = 'If yes(for how many days, you got trainined)* Field is required.';
	    		$form_state->setErrorByName('if_yes_got_trainined', $message);
			}
		}
		if($field['stitching_related_order_work_completed'] == 1){
			if(empty($field['if_yes_order_completed'])){
				$message = 'If yes (Share details of the order completed/taken)* Field is required.';
	    		$form_state->setErrorByName('if_yes_order_completed', $message);
			}
		}

    }
    public function submitForm(array &$form, FormStateInterface $form_state) {
    	$field = $form_state->getValues();
		$CustomFields = \Drupal::request()->request;
		#Get Node Id
		$nid = $form_state->getBuildInfo()['args'][0];

	    $node = \Drupal::routeMatch()->getParameter(NODE);
	    $conn = Database::getConnection();
	    $query = $conn->select(TABLE_SILAI_ADD_SCHOOL_DATA, 's')
	            ->condition(NID, $nid)
	            ->fields('s');
	    $school_data = $query->execute()->fetchAssoc();
	   
	    $conn = Database::getConnection();
	    $child_query = $conn->select(TABLE_SILAI_ADD_SCHOOL_CHILDREN_DATA, 's')
	            ->condition(NID, $nid)
	            ->fields('s');
	    $child_datas = $child_query->execute()->fetchAll(\PDO::FETCH_OBJ);
		$dataArray = array(
                NID              => $nid,
                STATUS_DATE       => $field[STATUS_DATE],
                PIN_CODE              => $field[PIN_CODE],
                MARITAL_STATUS        => $field[MARITAL_STATUS],
                RELIGION              => $field[RELIGION],
                'cast'                  => $field['cast'],
                CAST_CATEGORY         => $field[CAST_CATEGORY],
                ANY_SPECIAL_CATEGORY  => $field[ANY_SPECIAL_CATEGORY],
                HAVE_BANK_ACCOUNT     => $field[HAVE_BANK_ACCOUNT],
                BANK_NAME             => $field[BANK_NAME],
                HAVE_PAN_CARD         => $field[HAVE_PAN_CARD],
                HAVE_AADHAR_CARD      => $field[HAVE_AADHAR_CARD],
                AADHAR_NUMBER         => $field[AADHAR_NUMBER],
                ASSOCIATED_WITH_ANY_MFI  => $field[ASSOCIATED_WITH_ANY_MFI],
                MFI_NUMBER            => $field[MFI_NUMBER],
                FATHER_NAME     => $field[FATHER_NAME],
                FATHER_OCCUPATION     => $field[FATHER_OCCUPATION],
                FAMILY_INCOME         => $field[FAMILY_INCOME],
                RATION_CARD           => $field[RATION_CARD],
                TYPE_OF_RATION_CARD   => $field[TYPE_OF_RATION_CARD],
                HAVE_GAS_CONNECTION   => $field[HAVE_GAS_CONNECTION],
                OTHER_MFIS_WORKING_YOUR_AREA      => $field[OTHER_MFIS_WORKING_YOUR_AREA],
                GETTING_BENEFITTED_GOVERNMENT_SCHEMES      => $field[GETTING_BENEFITTED_GOVERNMENT_SCHEMES],
                KINDLY_GIVE_GOVERNMENT_BENEFITTED_DETAILS => $field[KINDLY_GIVE_GOVERNMENT_BENEFITTED_DETAILS],
                ELECTRICITY_STATUS_IN_HOME        => $field[ELECTRICITY_STATUS_IN_HOME],
                AVERAGE_ELECTRICITY_HOURS         => $field[AVERAGE_ELECTRICITY_HOURS],
                HAVE_SOLAR_PANEL                  => $field[HAVE_SOLAR_PANEL],
                USAGE_OF_DRINKING_WATER           => $field[USAGE_OF_DRINKING_WATER],
                ### check array
                APPLIANCES_IN_THE_HOME            => implode( ', ', $CustomFields->get(APPLIANCES_IN_THE_HOME) ),
                TYPE_OF_YOUR_HOUSE                => $field[TYPE_OF_YOUR_HOUSE],
                ### check array
                BEFORE_SILAI_SCHOOL_HOUSEHOLD     => implode( ', ', $CustomFields->get(BEFORE_SILAI_SCHOOL_HOUSEHOLD) ),
                AFTER_SILAI_SCHOOL_HOUSEHOLD      => implode( ', ', $CustomFields->get(AFTER_SILAI_SCHOOL_HOUSEHOLD) ),
                TOTAL_BOYS                        => $field[TOTAL_BOYS],
                TOTAL_GIRLS                       => $field[TOTAL_GIRLS],
                USE_MOBILE_PHONE                  => $field[USE_MOBILE_PHONE],
                ALTERNATIVE_MOBILE_NUMBER         => $field[ALTERNATIVE_MOBILE_NUMBER],
                TYPE_OF_MOBILE_PHONE              => $field[TYPE_OF_MOBILE_PHONE],
                USE_INTERNET                      => $field[USE_INTERNET],
                USE_WHATSAPP                      => $field[USE_WHATSAPP],
                HAVE_EMAIL_ID                     => $field[HAVE_EMAIL_ID],
                MENTION_EMAIL                     => $field[MENTION_EMAIL],
                COMPUTER_IN_FAMILY                => $field[COMPUTER_IN_FAMILY],
                'completed_training_from_usha'      => $field['completed_training_from_usha'],
                'if_yes_got_trainined'              => $field['if_yes_got_trainined'],
                'usha_black_machine_you_have'       => $field['usha_black_machine_you_have'],
                'model_of_usha_black_machines'      => $field['model_of_usha_black_machines'],
                'usha_white_machine_you_have'       => $field['usha_white_machine_you_have'],
                'model_of_usha_white_machines'      => $field['model_of_usha_white_machines'],
                ### check array
                'aadhar_attachment'                 => $field['aadhar_attachment'][0],
                'pan_attachment'                    => $field['pan_attachment'][0],
                'teacher_photograph_attachment'     => $field['teacher_photograph_attachment'][0],
                'signage_in_the_school'             => $field['signage_in_the_school'],
                'type_of_signage_present'           => $field['type_of_signage_present'],
                'condition_of_signboard'            => $field['condition_of_signboard'],
                'how_many_learners_you_have_trained'    => $field['how_many_learners_you_have_trained'],
                'how_many_non_usha_black_machines'      => $field['how_many_non_usha_black_machines'],
                'where_is_the_silai_school'             => $field['where_is_the_silai_school'],
                'average_learners_attending'            => $field['average_learners_attending'],
                'average_fee_charged'               => $field['average_fee_charged'],
                'monthly_income_learners_fee'       => $field['monthly_income_learners_fee'],
                'monthly_income_stitching'          => $field['monthly_income_stitching'],
                'income_from_sewing_machine_repairing' => $field['income_from_sewing_machine_repairing'],
                'monthly_income_from_sale_of_dresses' => $field['monthly_income_from_sale_of_dresses'],
                'monthly_income_from_silai_schools' => $field['monthly_income_from_silai_schools'],
                STITCHING_RELATED_ORDER_WORK_COMPLETED => $field[STITCHING_RELATED_ORDER_WORK_COMPLETED],
                'if_yes_order_completed'            => $field['if_yes_order_completed'],
                'additional_notes'                  => $field['additional_notes'],
                'created'                           => time(),
                
            ); 
		if($school_data[NID] == $nid){
			$database = \Drupal::database();
			$query_update = $database->update(TABLE_SILAI_ADD_SCHOOL_DATA)->fields($dataArray)->condition(NID, $nid)->execute();

	        $query_delete = $database->delete(TABLE_SILAI_ADD_SCHOOL_CHILDREN_DATA)->condition(NID, $nid)->execute();

	        $child_num = count($CustomFields->get(CHILDREN_DETAILS));
	        $i = 0;
	        for($i = 0; $i < $child_num; $i++){
	        	$childDataArray = array(
                    NID               => $nid,
                    CHILDREN_NAME       => $CustomFields->get(CHILDREN_DETAILS)[$i][CHILDREN_NAME][$i],
                    CHILDREN_GENDER     => $CustomFields->get(CHILDREN_DETAILS)[$i][CHILDREN_GENDER][$i],
                    CHILDREN_AGE        => $CustomFields->get(CHILDREN_DETAILS)[$i][CHILDREN_AGE][$i],
                    CHILDREN_EDUCATION_LEVEL        => $CustomFields->get(CHILDREN_DETAILS)[$i][CHILDREN_EDUCATION_LEVEL][$i],
                    CHILDREN_SCHOOL_TYPE        => $CustomFields->get(CHILDREN_DETAILS)[$i][CHILDREN_SCHOOL_TYPE][$i],
                    CHILDREN_MONTHLY_EXPENSE_OVER_EDUCATION        => $CustomFields->get(CHILDREN_DETAILS)[$i][CHILDREN_MONTHLY_EXPENSE_OVER_EDUCATION][$i],
                    'created'                           => time(),
                );
                $database = \Drupal::database();
	        	$query_insert = $database->insert(TABLE_SILAI_ADD_SCHOOL_CHILDREN_DATA)->fields($childDataArray)->execute();

	        }
		}else{
			if(!empty($nid)) {
	            $database = \Drupal::database();
	            $query = $database->insert(TABLE_SILAI_ADD_SCHOOL_DATA)->fields($dataArray)->execute(); 
	     
	            $child_num = count($CustomFields->get(CHILDREN_DETAILS));
	            $i = 0;
	            for ($i = 0; $i < $child_num; $i++){
	                $childDataArray = array(
	                    NID               => $nid,
	                    CHILDREN_NAME       => $CustomFields->get(CHILDREN_DETAILS)[$i][CHILDREN_NAME][$i],
	                    CHILDREN_GENDER     => $CustomFields->get(CHILDREN_DETAILS)[$i][CHILDREN_GENDER][$i],
	                    CHILDREN_AGE        => $CustomFields->get(CHILDREN_DETAILS)[$i][CHILDREN_AGE][$i],
	                    CHILDREN_EDUCATION_LEVEL        => $CustomFields->get(CHILDREN_DETAILS)[$i][CHILDREN_EDUCATION_LEVEL][$i],
	                    CHILDREN_SCHOOL_TYPE        => $CustomFields->get(CHILDREN_DETAILS)[$i][CHILDREN_SCHOOL_TYPE][$i],
	                    CHILDREN_MONTHLY_EXPENSE_OVER_EDUCATION        => $CustomFields->get(CHILDREN_DETAILS)[$i][CHILDREN_MONTHLY_EXPENSE_OVER_EDUCATION][$i],
	                    'created'                           => time(),
	                );
	                $database = \Drupal::database();
	                $query = $database->insert(TABLE_SILAI_ADD_SCHOOL_CHILDREN_DATA)->fields($childDataArray)->execute(); 
	            }
	        }
		}
	} 
}



















