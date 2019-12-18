<?php
const AES_CRED = 'aes.cred';
const STATUS  = 'status';
const USER_NAME  = 'user_name';
const IMPORT_LOG_PATH = "/var/www/html/usha_uat/log";
const ROLE_SEWING_HO_USER  = 'sewing_ho_user';
const ACTIONS  = 'actions';
const TEXTFIELD = 'textfield';
const ENTITYAUTOCOMPLETEFIELD = 'entity_autocomplete';
const HASH_TYPE = '#type';
const HASH_PREFIX = '#prefix';
const HASH_SUFFIX = '#suffix';
const HASH_TITLE = '#title';
const HASH_REQUIRED = '#required';
const HASH_OPTIONS = '#options';
const HASH_MULTIPLE = '#multiple';
const HASH_MAXLENGTH = '#maxlength';
const HASH_MINLENGTH = '#minlength';
const HASH_TAG = '#tags';
const VALUE = 'value';
const TITLE = 'title';
const HASH_VALUE = '#value';
const HASH_DEFAULT_VALUE = '#default_value';
const UNDERSCORE_NONE = '_none';
const SELECT_VALUE = '- Select a value -';
const FIELD_FIRST_NAME = 'field_first_name';
const FIELD_LAST_NAME = 'field_last_name';
const FIELD_USER_EMAIL = 'field_user_email';
const FIELD_USER_ID = 'field_user_id';
const FILED_USER_CONTACT_NO = 'field_user_contact_no';
const FILED_PROFILE = 'field_profile';
const FILED_USER_LOCATION = 'field_user_location';
const SILAI_HO_USER = 'silai_ho_user';
const HO_USER = 'Ho User';
const FIELD_USER_STATUS = 'field_user_status';
const SEWING_DOMAIN = 'usha_sewing_tk';
const SILAI_DOAMIN = 'usha_silai_tk';
const HASH_VALIDATE = '#validate';
const HASH_ATTRIBUTES = '#attributes';
const HASH_OPEN = '#open';
const HASH_ROWS = '#rows';
const CLASS_CONST = 'class';
const ALPHANUMERIC = 'alphanumeric';
const ONLY_NUMERIC_VALUE = 'only-numeric-value';
const NUMERIC_WITH_DECIMAL = 'numeric-validation';
const TYPE = 'type';
const NODE = 'node';
const NID = 'nid';
const HASH_PLACEHOLDER = '#placeholder';
const ROLE_SEWING_SSI = 'sewing_ssi';
const ROLE_SEWING_SCHOOL_ADMIN = 'sewing_school_admin';
const ROLE_SEWING_HO_ADMIN = 'sewing_ho_admin';
const ROLE_SEWING_SCHOOL_TEACHER = 'sewing_school_teacher';
const ROLE_SILAI_HO_ADMIN = 'silai_ho_admin';
const ROLE_SILAI_SCHOOL_ADMIN = 'silai_school_admin';
const ROLE_SILAI_NGO_ADMIN =  'ngo_admin';
const ROLE_SILAI_LEARNER = 'silai_learner';
const ROLE_SILAI_PC = 'pc';
const SEWING_SSI = 'SSI';
const SEWING_SCHOOL_ADMIN = 'School Admin';
const SEWING_SCHOOL_TEACHER = 'School Teacher';
const SEWING_HO_ADMIN = 'Ho Admin';
const SILAI_HO_ADMIN = 'Silai Ho Admin';
const SILAI_HO_USER_TEXT = 'Silai Ho User';
const POPUP_FORM_ARRAY = ['node_manage_countries_form', 'node_manage_countries_edit_form', 'node_manage_silai_countries_form', 'node_manage_silai_countries_edit_form', 'node_manage_locations_form', 'node_manage_locations_edit_form', 'node_manage_silai_locations_form', 'node_manage_silai_locations_edit_form', 'node_manage_business_states_form', 'node_manage_business_states_edit_form', 'node_silai_business_states_form', 'node_silai_business_states_edit_form', 'node_manage_districts_form', 'node_manage_districts_edit_form', 'node_silai_district_form', 'node_silai_district_edit_form', 'node_manage_towns_form', 'node_manage_towns_edit_form','node_silai_town_form', 'node_silai_town_edit_form', 'node_silai_blocks_form', 'node_silai_blocks_edit_form', 'node_silai_villages_form', 'node_silai_villages_edit_form', 'node_silai_inventory_form', 'node_silai_inventory_edit_form', 'node_silai_school_type_master_form', 'node_silai_school_type_master_edit_form', 'node_silai_item_group_form', 'node_silai_item_group_edit_form', 'node_silai_items_form', 'node_silai_items_edit_form', 'node_silai_dealer_form', 'node_silai_dealer_edit_form', 'node_trainer_silai_form', 'node_trainer_silai_edit_form', 'node_silai_training_form', 'node_silai_training_edit_form', 'node_manage_agreements_form', 'node_manage_agreements_edit_form', 'node_nfa_form', 'node_nfa_edit_form', 'node_manage_inventory_form', 'node_manage_inventory_edit_form'];

const USER_GROUP = 'User_Group';
const HASH_WEIGHT = '#weight';
const HASH_STATES = '#states';
const VISIBLE = 'visible';
const REQUIRED = 'required';
const GROUP = 'group';
const GROUP_SKILLS = 'group_skills';
const HASH_PLAIN_TEXT = '#plain_text';
const HASH_UPLOAD_LOCATION = '#upload_location';
const AGREEMENT = 'agreement';

const HASH_TYPE_DETAILS = 'details';
const DATEFIELD = 'date';
const NUMBERFIELD = 'number';
const TEXTAREAFIELD = 'textarea';
const SELECTFIELD = 'select';
const RADIOSFIELD = 'radios';  
const FILEFIELD = 'managed_file';
const INVENTORYSTATUSARRAY = ['1' => 'Pending', '2' => 'Received By PC', '3' => 'Forwarded By PC', '4' => 'Received By NGO', '5' => 'Forwarded By NGO', '6' => 'Received By School']; 
const FIELD_USER_APPROVAL_STATUS = 'field_user_approval_status';
const APPROVE_BUTTON_VALUE = 'Approve'; 
const REJECT_BUTTON_VALUE = 'Reject'; 
const HASH_ACCESS = '#access';
const CHILDREN_DETAILS = 'children_details';
const TABLE_SILAI_ADD_SCHOOL_CHILDREN_DATA = 'silai_add_school_children_data';
const FIELD_USER_PASSWORD = 'field_user_password';
const TABLE_SILAI_TRAINEE_FEEDBACK = 'silai_trainee_feedback';
const TABLE_CUSTOM_MANAGE_INVENTORY = 'custom_manage_inventory';
const TABLE_CUSTOM_MANAGE_INVENTORY_SEWING = 'custom_manage_inventory_sewing';
const TABLE_SILAI_ASSOCIATED_USER = 'silai_ngo_associated_user';
const TABLE_SILAI_NGO_PAYMENT_DETAIL = 'silai_ngo_payment_detail';
const TABLE_SILAI_ADD_SCHOOL_DATA = 'silai_add_school_data';
const TABLE = 'table';
const TABLE_NODE_FIELD_DATA = 'node_field_data';
const LEFT_FIELD = 'left_field';
const FIELD = 'field';
const FILTER = 'filter';
const RELATIONSHIP = 'relationship';
const HELP = 'help';
const JOIN = 'join';
const NUMERIC = 'numeric';
const ARGUMENT = 'argument';
const STRING_TEXT = 'string';
const ID = 'id';
const HANDLER = 'handler';
const BASE = 'base';
const STANDARD = 'standard';
const SORT = 'sort';
const LABEL = 'label';
const TEXT_BASE_FIELD = 'base field';
const VIEW_HANDLER_RELATIONSHIP = 'views_handler_relationship';
const USERID = 'userId';
const LABEL_FORWARD_INVENTORY = 'Forward Inventory';
const QTY_SEND = 'qty_send';
const REF_ID = 'ref_id';
const REFID = 'refId';
const FIELD_HIDDEN = 'hidden';
const FIELD_HIDDEN_REFID = 'field_hidden_refid';
const FIELD_SILAI_INCOMING_ITEM_QTY = 'field_silai_incoming_item_qty';
const FIELD_SEWING_INCOMING_ITEM_QTY = 'field_sewing_incoming_item_qty';
const FIELD_SILAI_ITEM_RECEIVED = 'field_silai_item_received';
const FIELD_SEWING_ITEM_RECEIVED = 'field_sewing_item_received';
const RECEIVER_ID = 'receiver_id';
const QTY_RECEIVED = 'qty_received';
const TOTAL_FORWARDED = 'total_forwarded';
const SENDER_ROLE = 'sender_role';
const PARENT_REF_ID = 'parent_ref_id';
const FIELD_HIDDEN_NID = 'field_hidden_nid';
const TEXT_QUANTITY = 'Quantity';
const PENDING_STATUS = '0';
const APPROVED_STATUS = '1';
const REJECTED_STATUS = '2';
const STATUS_DATE = 'status_date';
const PIN_CODE = 'pin_code';
const MARITAL_STATUS = 'marital_status';
const TEXT_ANY_OTHER = 'Any other';
const RELIGION = 'religion';
const CAST_CATEGORY = 'cast_category';
const ANY_SPECIAL_CATEGORY = 'any_special_category';
const FATHER_NAME = 'father_name';
const FATHER_OCCUPATION = 'father_occupation';
const FAMILY_INCOME = 'family_income';
const HASH_TREE = '#tree';
const BEFORE_SILAI_SCHOOL_HOUSEHOLD = 'before_silai_school_household';
const AFTER_SILAI_SCHOOL_HOUSEHOLD = 'after_silai_school_household';
const RATION_CARD = 'ration_card';
const TYPE_OF_RATION_CARD = 'type_of_ration_card';
const STITCHING_RELATED_ORDER_WORK_COMPLETED = 'stitching_related_order_work_completed';
const COMPUTER_IN_FAMILY = 'computer_in_family';
const MENTION_EMAIL = 'mention_email';
const USE_WHATSAPP = 'use_whatsapp';
const HAVE_EMAIL_ID = 'have_email_id';
const USE_INTERNET = 'use_internet';
const TYPE_OF_MOBILE_PHONE = 'type_of_mobile_phone';
const ALTERNATIVE_MOBILE_NUMBER = 'alternative_mobile_number';
const USE_MOBILE_PHONE = 'use_mobile_phone';
const CHILDREN_SCHOOL_TYPE = 'children_school_type';
const CHILDREN_MONTHLY_EXPENSE_OVER_EDUCATION = 'children_monthly_expense_over_education';
const CHILDREN_EDUCATION_LEVEL = 'children_education_level';
const CHILDREN_AGE = 'children_age';
const CHILDREN_GENDER = 'children_gender';
const CHILDREN_NAME = 'children_name';
const TOTAL_BOYS = 'total_boys';
const TOTAL_GIRLS = 'total_girls';
const DISABLED = 'disabled';
const FIELD_SILAI_FRD_TO = 'field_silai_frd_to';
const FIELD_SEWING_FRD_TO = 'field_sewing_frd_to';
const FIELD_SILAI_ITEM_SENT = 'field_silai_item_sent';
const FIELD_SEWING_ITEM_SENT = 'field_sewing_item_sent';
const RECEIVER_ROLE = 'receiver_role';
const AGREEMENT_AMOUNT = 'agreement_amount';
const INSTALLMENT = 'installment';
const PAYMENT_MODE = 'payment_mode';
const AMOUNT = 'amount';
const SILAI_SCHOOL = 'silai_school';
const FIELD_SIL_SCHOOL_APPROVAL_STATUS = 'field_sil_school_approval_status';
const AGREEMENT_DETAIL = 'agreement_detail';
const BANK_DRAWN = 'bank_drawn';
const CHEQUE_NO = 'cheque_no';
const FIELD_NGO_USER_ID = 'field_ngo_user_id';  
const TYPE_OF_YOUR_HOUSE = 'type_of_your_house';  
const HAVE_BANK_ACCOUNT = 'have_bank_account';  
const BANK_NAME = 'bank_name';  
const HAVE_PAN_CARD = 'have_pan_card';  
const HAVE_AADHAR_CARD = 'have_aadhar_card';  
const AADHAR_NUMBER = 'aadhar_number';  
const HAVE_GAS_CONNECTION = 'have_gas_connection';  
const ASSOCIATED_WITH_ANY_MFI = 'associated_with_any_mfi';  
const MFI_NUMBER = 'mfi_number';  
const OTHER_MFIS_WORKING_YOUR_AREA = 'other_mifs_working_your_area';  
const GETTING_BENEFITTED_GOVERNMENT_SCHEMES = 'getting_benefitted_government_schemes';  
const KINDLY_GIVE_GOVERNMENT_BENEFITTED_DETAILS = 'kindly_give_government_benefitted_details';  
const ELECTRICITY_STATUS_IN_HOME = 'electricity_status_in_home';  
const AVERAGE_ELECTRICITY_HOURS = 'average_electricity_hours';  
const HAVE_SOLAR_PANEL = 'have_solar_panel';  
const USAGE_OF_DRINKING_WATER = 'usage_of_drinking_water';  
const APPLIANCES_IN_THE_HOME = 'appliances_in_the_home';  
const HASH_UPLOAD_VALIDATORS = '#upload_validators';
const HASH_UPLOAD_LOCATION = '#upload_location';
const HASH_MARKUP  = '#markup';
const HASH_BUTTON_TYPE = '#button_type';

const MARITAL_STATUS_OPTIONS = ['' => SELECT_VALUE, '4' => 'Divorce', '2' => 'Married', '5' => 'Separated', '1' => 'Unmarried', '3' => 'Widow',   '6' => TEXT_ANY_OTHER ];
const RELIGION_OPTIONS = ['' => SELECT_VALUE, '6' => 'Buddhism', '4' => 'Christian', '1' => 'Hindu', '5' => 'Jain', '2' => 'Muslim', '3' => 'Sikh', '7' => 'Others'];
const CAST_CATEGORY_OPTIONS = ['' => SELECT_VALUE, '1' => 'General', '2' => 'OBC', '3' => 'SC', '4' => 'ST', '5' => 'Others'];
const ANY_SPECIAL_CATEGORY_OPTIONS = ['' => SELECT_VALUE, '4' => 'Migrated', '1' => 'Physically Challenged', '2' => 'PLHIV', '5' => 'Refugee', '3' => 'SW', '6' => 'Violence victim', '7' => TEXT_ANY_OTHER];
const FATHER_OCCUPATION_OPTIONS = ['' => SELECT_VALUE,
									'4' => 'Both cultivator and occasional labourer',
									'2' => 'Cultivator (own land)',
									'10' => 'Clerical Jobs/police/ teachers',
									'3' => 'Daily wage Labourer',
									'11' => 'Higher Jobs (Professor, Inspector, Engineer, doctor, lawyer etc.)',
									'1' => 'Housewife/ Male adult member not able to work',
									'13' => 'In case of students/out of school',
									'9' => 'Office Job (Class IV) –(Pvt security, peon, office attendant  etc. )',
									'8' => 'Petty business (shop)-(tailoring shop, tea shop, pressing cloth (laundry), vegetable and fruit shop, stationery shop’ meat/poultry/fish shop, vegetable vender, Kabari, Bakery; weaver, contractor)',
									'7' => 'Skilled labourer- (driver (auto, bus/car), cycle mechanic, electrician, painter, masan, beldar (construction) etc.',
									'5' => 'Traditional Services- (Cobbler (Muchi), dhobi, nai  etc)',
									'6' => 'Unskilled Labourer- (Sweeper, gardner (mali), helper, domestic servent, cleaner etc.)',
									'14' => 'Un employed',
									'12' => TEXT_ANY_OTHER];
const SILAI_SCHOOL_HOUSEHOLD_OPTIONS = ['' => SELECT_VALUE, '7' => 'Children caring', '4' => 'Doing laundry', '3' => 'Feeding pets', '6' => 'Look after parents/husband', '5' => 'Preparing meals', '1' => 'Sweeping and cleaning', '2' => 'Washing dishes', '8' => TEXT_ANY_OTHER];
const GENDER_OPTIONS = ['' => SELECT_VALUE, '1' => 'Male', '2' => 'Female'];
const CHILDREN_EDUCATION_LEVEL_OPTIONS = ['' => SELECT_VALUE, '1' => 'Not enrolled', '2' => 'Pre-primary/ Nursery', '3' => 'Literate/ Class I', '4' => 'Class 2', '5' => 'Class 3', '6' => 'Class 4', '7' => 'Class 5', '8' => 'Class 6', '9' => 'Class 7', '10' => 'Class 8', '11' => 'Class 9', '12' => 'Class 10', '13' => 'Class 11', '14' => 'Class 12', '15' => 'Class 12 pass or (Intermediate.)', '16' => 'Graduation (Not completed)', '17' => 'Graduation (completed)', '18' => 'Post-graduation (not completed)', '19' => 'Post-graduation (completed)', '20' => 'Technical/professionals Qual.', '21' => 'Any other technical/vocational course after a degree'];
const RADIOSFIELD_NO_YES_OPTIONS = [0 => 'No', 1 => 'Yes'];
const TYPE_OF_MOBILE_PHONE_OPTIONS = ['' => SELECT_VALUE, '3' => 'Android', '1' => 'Basic phone', '4' => 'iPhone', '2' => 'Smartphone', '5' => TEXT_ANY_OTHER];
const TYPE_OF_RATION_CARD_OPTIONS = ['' => SELECT_VALUE, '1' => 'Above Poverty Line (APL)', '3' => 'Antodya Anna Yojana (AAY)',  '5' => 'Central Below Poverty Line (CBPL)', '2' => 'State Below Poverty Line (SBPL)', '4' => 'Other Priority Households (OPH)'];
const AVERAGE_ELECTRICITY_HOURS_OPTIONS = ['' => SELECT_VALUE, '1' => 'No electricity', '2' => '0-6 hours', '3' => '6-12 hours', '4' => '12-18 hours', '5' => '18-24 hours'];
const USAGE_OF_DRINKING_WATER_OPTIONS = ['' => SELECT_VALUE, '1' => 'Direct supply', '4' => 'Filter', '2' => 'Hand Pump', '3' => 'RO', '5' => TEXT_ANY_OTHER];
const APPLIANCES_IN_THE_HOME_OPTIONS = ['' => SELECT_VALUE, '4' => 'AC', '5' => 'Cooler', '6' => 'Electric Geiser', '9' => 'Electric Iron/Induction', '3' => 'Fan', '7' => 'Juicer Mixer Grinder (JMG)', '8' => 'Mixer Grinder (MG)', '1' => 'TV/ Fridge', '2' => 'Washing Machine', '10' => 'Water purifier',	'11' => TEXT_ANY_OTHER];

const TYPE_OF_YOUR_HOUSE_OPTIONS = ['' => SELECT_VALUE, '1' => 'Kachcha', '2' => 'Pukka', '3' => 'Semi Pukka', '4' => 'Hut'];
const TYPE_OF_SIGNAGE_PRESENT_OPTIONS = ['' => SELECT_VALUE, '1' => 'Old Usha logo', '2' => 'New Usha logo'];
const CONDITION_OF_SIGNBOARD_OPTIONS = ['' => SELECT_VALUE, '1' => 'Good', '2' => 'Average', '3' => 'Need to be replaced'];

const WHERE_IS_THE_SILAI_SCHOOL_OPTIONS = ['' => SELECT_VALUE, '1' => 'Own House', '2' => 'Rented House', '3' => 'Own Shop', '4' => 'Rented Shop', '5' => 'Community Hall', '6' => 'Panchayat Bhawan', '7' => TEXT_ANY_OTHER];


const WEEKLY_MIS_FIELDS = ['week_start_date' => 'Week',
							'num_classical_schools_till_date' => 'Consolidated number of classical schools till date',
							'num_satellite_schools_till_date' => 'Consolidated number of satellite schools till date',
							'num_classical_schools_visited' => 'Number of classical schools visited',
							'num_satellite_school_visited' => 'Number of satellite school visited',
							'num_visits_schools_centers' => 'Visits made to other schools/production centers',
							'total_visits_ngo_office' => 'Total visits made to NGO/Sub NGO/Corporate partner offices',
							'num_new_partnership_explored' => 'Number of new partnership explored',
							'black_machines_sold_silai_schools' => 'Black Machines sold through Silai schools',
							'one_model_name' => '1-Model Name(s)',
							'white_machines_sold_silai_schools' => 'White Machines sold through Silai schools',
							'two_model_name' => '2-Model Name(s)',
							'num_appliances_sold_silai_schools' => 'Number of appliances sold through Silai schools',
							'details_sold_silai_schools' => 'Details of appliances sold',
							'trainings_ongoing_classical_schools' => 'Trainings ongoing for how much classical schools (WE)',
							'trainings_ongoing_satellite_schools' => 'Trainings ongoing for how much satellite schools (WE)',
							'case_studies_shared_head_office' => 'Case studies shared with Head Office',
							'details_of_case_studies_shared' => 'Details of case studies shared',
							'certificates_issued_to_teachers' => 'Certificates issued to teachers',
							'certificate_issued_to_learners' => 'Certificate issued to learners',
							'num_of_photo' => 'Number of good quality photographs shared',
							'num_of_ss_board_replacement' => 'Number of silai school boards for replacement',
							'board_received_head_office' => 'Board received at location from Head Office',
							'board_installed_ss' => 'Board installed at silai schools',
							'silai_book_received_head_office' => 'Silai book received at location from Head Office',
							'silai_book_provided_ss' => 'Silai book provided to silai schools',
							'num_new_cs_training_completed' => 'Number of new classical schools training completed',
							'num_new_ss_training_completed' => 'Number of new satellite school training completed',
							'num_women_training_completed' => 'Number of other women entrepreneurs training completed',
							'monthly_mis_of_last_fy_css' => 'Whether Monthly MIS of Last FY classical Silai schools submitted on SSA?',
							'monthly_mis_of_current_fy_css' => 'Whether Monthly MIS of Current FY classical schools submitted on SSA?',
							'total_teacher_certificate_stock' => 'Total Teachers certificate stock available at location as on date',
							'total_learners_certificate_stock' => 'Total Learners certificate stock available at location as on date',
							'new_partnership_explored' => 'Details of the new partnership explored',
							'feedback_ngo' => 'Feedback from NGO partner(s)',
							'comment' => 'Your comments/remark'
							];
const WEEKLY_MIS_UPLOAD_FIELDS = ['week_start_date',                   
									'location',                     
									'num_classical_schools_till_date',    
									'num_satellite_schools_till_date',    
									'num_classical_schools_visited',      
									'num_satellite_school_visited',       
									'num_visits_schools_centers',         
									'total_visits_ngo_office',            
									'num_new_partnership_explored',       
									'new_partnership_explored',           
									'black_machines_sold_silai_schools',  
									'one_model_name',                     
									'white_machines_sold_silai_schools',  
									'two_model_name',                     
									'num_appliances_sold_silai_schools',  
									'details_sold_silai_schools',         
									'trainings_ongoing_classical_schools',
									'trainings_ongoing_satellite_schools',
									'case_studies_shared_head_office',    
									'details_of_case_studies_shared',     
									'certificates_issued_to_teachers',    
									'certificate_issued_to_learners',     
									'num_of_photo',                       
									'num_of_ss_board_replacement',        
									'board_received_head_office',         
									'board_installed_ss',                 
									'silai_book_received_head_office',    
									'silai_book_provided_ss',             
									'num_new_cs_training_completed',      
									'num_new_ss_training_completed',      
									'num_women_training_completed',       
									'feedback_ngo',                       
									'comment',                            
									'monthly_mis_of_last_fy_css',         
									'monthly_mis_of_current_fy_css',      
									'total_teacher_certificate_stock',    
									'total_learners_certificate_stock'
									];							

const MONTHLY_MIS_FIELDS = [
							// 'school_type' => 'School Type',
							'state' => 'STATE',
							'district' => 'DISTRICT',
							'block' => 'BLOCK',
							'village' => 'VILLAGE',
							'school_code' => 'SCHOOL Code',
							'monthly_quarterly_type' => 'Monthly/Quarterly Type',
							'fiscal_year' => 'Fiscal Year',
							'monthly_quarterly_value' => 'Monthly/Quarterly',
							// 'quarterly_value' => 'Monthly/Quarterly',
							'date_of_training' => 'Teacher Name',
							'ss_sign_board_received' => 'SILAI SCHOOL SIGN BOARD RECEIVED',
							'sb_prominent_place' => 'SIGN BOARD DISPLAYED AT PROMINENT PLACE',
							'condition_of_sb' => 'CONDITION OF SIGN BOARD',
							'machine_condition' => 'SEWING MACHINE WORKING PROPERLY',
							'machine_remark' => 'IF NO THEN WHAT IS THE PROBLEM',
							'usefulness_of_course' => 'Usefulness of course',
							'additional_information' => 'ACTIVITIES DONE FOR INCREASING LEARNERS',
							'activity_code' => 'SCHOOL WORKING STATUS',
							'no_of_student' => 'REASON FOR NON WORKING OF SCHOOL',
							'activities_status' => 'TOTAL LEARNERS ATTENDANCE IN SCHOOL',
							'no_of_learners' => 'Number of learners enrolled',
							'no_of_learners_course_completed' => 'Number of learners completed course',
							'fee_charged_learners_month' => 'AVERAGE FEE CHARGED PER LEARNER PER MONTH',
							'income_from_learners_fee' => 'Income from learners fee',
							'income_from_tailoring' => 'Income from Job-work/tailoring',
							'income_from_sewing_machine_repairing' => 'Income from sewing machine repairing',
							'total_income' => 'Total income from all sources',
							'name_of_classical_school' => 'WHERE YOU MOSTLY SPEND YOUR INCOME',
							'whether_entrepreneur_machine' => 'Total Usha Sewing machines',
							'brand_of_machine' => 'Total Non-Usha Sewing machines',
							'students_practice' => 'WHERE DO STUDENTS PRACTICE',
							'remark' => 'REMARK',
							'enquiry' =>'School Type',
							'feedback' => 'District'
						];

const MIS_SCHOOL_WORKING_STATUS_OPTIONS	 = ['RUNNING' => 'RUNNING', 'TEMPORARILY CLOSED' => 'TEMPORARILY CLOSED', 'PERMANENTLY CLOSED' => 'PERMANENTLY CLOSED'];				
const MIS_REASON_FOR_NON_WORKING_OF_SCHOOL_OPTIONS = [
		'' => '- Select a Value -',
		'NOT APPLICABLE' => 'NOT APPLICABLE',
		'NO LEARNERS' => 'NO LEARNERS',
		'NO INCOME' => 'NO INCOME',
		'ENGAGE IN SOME OTHER WORK' => 'ENGAGE IN SOME OTHER WORK',
		'HEALTH ISSUES' => 'HEALTH ISSUES',
		'LEARNERS EXHAUSTED' => 'LEARNERS EXHAUSTED',
		'MARRIAGE' => 'MARRIAGE',
		'PREGNANCY' => 'PREGNANCY',
		'FAMILY ISSUES' => 'FAMILY ISSUES',
		'UNREACHABLE' => 'UNREACHABLE',
		'LEFT VILLAGE' => 'LEFT VILLAGE',
		'TEACHER IS NO MORE' => 'TEACHER IS NO MORE',
		'ANY OTHER REASON' => 'ANY OTHER REASON',
	];
const MONTHLY_QUARTERLY_TYPE_OPTIONS = [''=>'-Select-','Monthly MIS', 'Quarterly MIS'];
const CONDITION_OF_SIGN_BOARD = [''=>'- Select a Value -', 'GOOD' => 'GOOD', 'AVERAGE' => 'AVERAGE', 'BAD' => 'BAD'];
const MONTHLY_TYPE_DATA = [1 => 'January','February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
const QUARTERLY_TYPE_DATA = [1=> 'APR-JUN', 'JUL-SEP', 'OCT-DEC', 'JAN-MAR'];
//const WEEKLY_MIS_DROP_DOWN_DATE = '29 May 2017'; 
const WEEKLY_MIS_DROP_DOWN_DATE = '03 June 2019';
const AGREEMENT_EDIT_FORM_ID = 'node_manage_agreements_edit_form';

const NO_YES_OPTIONS = [0 => 'No', 1 => 'Yes'];

const LEARNER_WHAT_WILL_YOU_DO_AFTER_LEARNING_FROM = [	'' => SELECT_VALUE,
														1 => 'Private Job',
														2 => 'Earning', 
														3 => 'Tailoring', 
														4 => 'Self dependent', 
														5 => 'Look for a advance training', 
														6 => 'Become a trainer', 
														7 => 'Hobby', 
														8 => 'Support our family',
														9 => 'Business',
														10 => 'Open a Boutique',
														11 => 'Open a Silai School',
														12 => 'Study',
														13 => 'Fashion Designing',
														14 => 'Social work'
													];
const LEARNER_EDUCATIONAL_QUALIFICATION = [1 => 'Non literate', 2 => 'Primary', 3 => 'Upper Primary', 4 => 'Diploma', 5 => 'High School', 6 => 'Intermediate', 7 => 'Graduate', 8 => 'Post Graduate', 9 => 'Any other'];
const LEARNER_MARITAL_STATUS = [1 => 'Unmarried', 2 => 'Married', 3 => 'Widow', 4 => 'Divorce', 5 => 'Separated', 6 => 'Any other'];
const LEARNER_COURSE_CODE = [9 => 'BAS', 10 => 'REF', 11 => 'ADV'];
const DASHBOARD_MONTH_FILTER = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'];

const OCCUPATION_GUARDIAN_OPTIONS = [ '' => SELECT_VALUE,
									'1' => 'Cultivator',
									'2' => 'Skilled labourer',
									'3' => 'Daily wage labourer',
									'4' => 'Clerical Job',
									'5' => 'Office Job',
									'6' => 'Petty Business',
									'7' => 'Final code',
									'8' => 'Traditional Services',
									'9' => 'Higher Job',
									'10' => 'Unskilled Labourer',
									'11' => 'Un employed',
									'12' => 'Student',
									'13' => 'Home Maker'
									];

const OCCUPATION_MOTHER_OPTIONS = [ '' => SELECT_VALUE,
									'1' => 'Cultivator',
									'2' => 'Skilled labourer',
									'3' => 'Daily wage labourer',
									'4' => 'Clerical Job',
									'5' => 'Office Job',
									'6' => 'Petty Business',
									'7' => 'Final code',
									'8' => 'Traditional Services',
									'9' => 'Higher Job',
									'10' => 'Unskilled Labourer',
									'11' => 'Un employed',
									'12' => 'Student',
									'13' => 'Home Maker',
									'14' => 'Any other'
									];


const ADD_SCHOOL_MESSAGE =  'School added by {creator name} is pending for approval.';
const UPDATE_SCHOOL_MESSAGE =  'School updated by {updator name} is pending for approval.';
const TABLE_CUSTOM_NOTIFICATION = 'custom_notifications';
const TABLE_SEWING_CUSTOM_NOTIFICATION = 'custom_sewing_notifications';
const SCHOOL_APPROVAL_MESSAGE = 'School {School code} has been approved.';
const SCHOOL_REJECTION_MESSAGE = 'School {School code} has been rejected. Please see the reason of rejection.';
const INVENTORY_FORWARD_MESSAGE = 'Inventory has been forwarded by {Role}';
const INVENTORY_ACCEPT_MESSAGE = 'Inventory has been accepted by {Receipient name} successfully';
const SENT_INVENTORY_MESSAGE = 'Inventory has been sent by {Sender Name}. Please accept.';
const UPDATE_INVENTORY_MESSAGE = 'Inventory has been updated by {Sender Name}. Please accept.';
const ADD_LEARNER_MESSAGE = 'A new Learner has been added by {Creator name in School Name}';
const ADD_STUDENT_MESSAGE = 'A new Student has been added by {Creator name in School Name}';
const EDIT_LEARNER_MESSAGE = 'Learner information was updated by {updator name}';
const EDIT_STUDENT_MESSAGE = 'Student information was updated by {updator name}';
const ADD_NGO_MESSAGE = 'A new NGO {NGO Name} has been added in the system';
const EDIT_NGO_MESSAGE = 'Details of NGO {NGO Name} has been updated by ';
const MONTHLY_MIS_UPDATE_MESSAGE = 'MIS for Month/Quarter has been updated';
const MONTHLY_MIS_ADD_MESSAGE = 'MIS for Month/Quarter has been submitted by {PC/NGO name}';
const WEEKLY_MIS_UPDATE_MESSAGE = 'MIS for week has been updated';
const WEEKLY_MIS_ADD_MESSAGE = 'MIS for week has been submitted by {PC name}';
const FEE_SUBMISSION_ADD_MESSAGE = 'SSI User submitted Fee on behalf of {School code}';
const FEE_SUBMISSION_UPDATE_MESSAGE = 'SSI User updated Fee on behalf of {School code}';

const COURSE_ADDED_MESSAGE = '{courseName} course added by HO';
const COURSE_UPDATE_MESSAGE = '{courseName} course Updated by HO';

const DEALER_ADDED_MESSAGE = '{dealerName} dealer added by HO';
const DEALER_UPDATE_MESSAGE = '{dealerName} dealer Updated by HO';

const TRAINER_ADDED_MESSAGE = '{trainerName} trainer added by HO';
const TRAINER_UPDATE_MESSAGE = '{trainerName} trainer Updated by HO';


const WEEKLY_MIS_FILE_NAME = 'weekly_mis_sample_file.xls';
const MONTHLY_MIS_FILE_NAME = 'monthly_mis_sample_file.xls';
const QUARTERLY_MIS_FILE_NAME = 'quarterly_mis_sample_file.xls';
const CHART_MIS_WEEKLY_FILTER_OPTION = [1 => 'Last Week', 2 => '2nd Last Week', 3 => '3rd Last Week'];
const CHART_MIS_MONTHLY_FILTER_OPTION = [1 => 'Last Month', 2 => '2nd Last Month', 3 => '3rd Last Month'];
const CHART_MIS_QUARTERLY_FILTER_OPTION = [1 => 'Last Quarter', 2 => '2nd Last Quarter', 3 => '3nd Last Quarter'];

const BULK_PARTNER_TYPE_DATA = [19 => 'NGO', 20 => 'CORPORATE', 21 => 'PSU', 22  => 'ACADEMIC', 23 => 'GOVERNMENT', 24 => 'UIL STAFF MONITORED'];
const BULK_FISCAL_YEAR_DATA = [1 => 'FY 2011-12', 2 => 'FY 2012-13', 3 => 'FY 2013-14', 4 => 'FY 2014-15', 5 => 'FY 2015-16', 6 => 'FY 2016-17', 7 => 'FY 2017-18', 8 => 'FY 2018-19', 9 => 'FY 2019-20'];
const BULK_BUDGET_HEAD_DATA = [3 => 'New school', 31 => 'Renewal', 32 => 'Addendum', 33 => 'Innovation & Research', 34 => 'Refrsher Training', 35 => 'Marketing', 36 => 'Any other'];
const BULK_SCHOOL_TYPE_DATA = [14337 => 'CLASSICAL SCHOOL', 14338=> 'SATELLITE SCHOOL', 14339=> 'PARTNERSHIP SCHOOL', 14340 => 'TRAINING OF TRAINERS',  14341 => 'PRODUCTION CENTER', 14342 => 'ADOPT A SILAI SCHOOL', 17050 => 'NON FORMAL EDUCATION CENTER'];
const BULK_FISCAL_YEAR_DATA_FOR_SCHOOL = [16 => '2011-12 FY', 17 => '2012-13 FY', 18 => '2013-14 FY', 25 => '2014-15 FY', 26 => '2015-16 FY', 27 => '2016-17 FY', 28 => '2017-18 FY', 29 => '2018-19 FY', 30 => '2019-20 FY'];
const FEE_GENERATION_ARRAY = ['state_id' => 'swr_state',
								'town_id' => 'swr_town',
								'school_id' => 'swr_school_code', 
								//'affiliation_fee_check' => 'swr_affiliation',
								//'affiliation_fee_amount' => 'swr_affiliation_value',
								//'renewal_fee_check' => 'swr_renewal',
								//'renewal_fee_amount' => 'swr_renewal_value',
							    //'prospectous_fee_check' => 'swr_prospectous',
								//'prospectous_fee_amount' => 'swr_prospectous_value', 
								'revenue_head_type' => 'swr_revenue_head_type',
								'revenue_head_value' => 'swr_revenue_head_value', 
								'want_to_add_student_fee' => 'swr_student_fee',
								'payment_type' => 'swr_payment_type',
								'total_fee_entry' => 'swr_total_fee_entry',
								'total_pay_to_uil' => 'swr_total_pay_to_uil',
								'tax' => 'swr_tax',
								'neft_beneficiary_name' => 'swr_beneficiary',
								'neft_beneficiary_account_no' => 'swr_beneficiary_ac_no',
								'neft_remitter_name' => 'swr_remitter',
								'neft_remitter_account_no' => 'swr_remitter_ac_no',
								'neft_ifsc_code' => 'swr_ifsc', 
								'neft_transaction_no' => 'swr_transaction',
								'neft_transaction_date' => 'swr_date',
								//'neft_transaction_time' => 'swr_time',
							    'cheque_dd_no' => 'swr_cheque_no',
							    'cheque_amount' => 'swr_cheque_amount',
								'cheque_bank_drawn' => 'swr_bank_drawn', 
								'cheque_transaction_no' => 'swr_cheque_transaction',
								//'cheque_transaction_time' => 'swr_cheque_time',
								'cheque_transaction_date' => 'swr_cheque_date',
								'cash_amount' => 'swr_cash_amount'
							   ];

const LOGIN_SUCCESS_MSG = [
						'en' => 'User login successfully', 
						'hi' => "उपयोगकर्ता लॉगिन सफलतापूर्वक", 
						'mr' => "सदस्याचे नाव यशस्वीरित्या",  
						'bn' => "ইউসার লগইন সুকসেসফুল্লি", 
						'as' => "ব্যবহারকারী লগ-ইন সফলভাবে", 
						'ta' => "பயனர் உள்நுழைவு வெற்றிகரமாக", 
						'gu' => "વપરાશકર્તા પ્રવેશ સફળતાપૂર્વક", 
						'ml' => "ഉപയോക്താവ് ലോഗിൻ വിജയകരമായി", 
						'kn' => "ಬಳಕೆದಾರರ ಲಾಗಿನ್ ಯಶಸ್ವಿಯಾಗಿ" 
						];

const LOGIN_UNSUCESS_MSG = [
						'en' => "Username/password does not match", 
						'hi' => "उपयोगकर्ता नाम / पासवर्ड से मेल नहीं खाता",  
						'mr' => "वापरकर्तानाव / संकेतशब्द जुळत नाही",  
						'bn' => "উসেরনামে / পাসওয়ার্ড  ম্যাচ দেশ  নোট",  
						'as' => "ব্যবহারকারীর নাম / পাসওয়ার্ড মিলছে না", 
						'ta' => "பயனர்பெயர் / கடவுச்சொல் பொருந்தவில்லை", 
						'gu' => "વપરાશકર્તાનામ / પાસવર્ડ મેળ ખાતો નથી", 
						'ml' => "ഉപയോക്തൃനാമം / പാസ്വേഡ് പൊരുത്തപ്പെടുന്നില്ല", 
						'kn' => "ಬಳಕೆದಾರ ಹೆಸರು / ಪಾಸ್ವರ್ಡ್ ಹೊಂದಿಕೆಯಾಗುತ್ತಿಲ್ಲ"
						];

const PROFILE_UPDATE_MSG = [
						'en' => "School updated successfully",
						'hi' => "स्कूल सफलतापूर्वक अपडेट किया गया",  
						'mr' => "शाळा यशस्वीपणे अद्यतनित",  
						'bn' => "স্কুল  সুকসেসফুল্লি আপডেটেড",  
						'as' => "স্কুল সফলভাবে আপডেট", 
						'ta' => "பள்ளி வெற்றிகரமாக மேம்படுத்தப்பட்டது", 
						'gu' => "શાળા સફળતાપૂર્વક અપડેટ", 
						'ml' => "സ്കൂൾ വിജയകരമായി അപ്ഡേറ്റ്", 
						'kn' => "ಸ್ಕೂಲ್ ಯಶಸ್ವಿಯಾಗಿ ನವೀಕರಿಸಲಾಗಿದೆ"
						];

const UNAUTHORIZE_USER_MSG = [
						'en' => "Not an authorize user", 
						'hi' => "उपयोगकर्ता अधिकृत नहीं",  
						'mr' => "नाही एक अधिकृत वापरकर्ता",  
						'bn' => "ইউসার নোট অথ্রিজিদ",  
						'as' => "না একটি অনুমোদন ব্যবহারকারী", 
						'ta' => "ஒரு அங்கீகரிக்க பயனர்", 
						'gu' => "નથી અધિકૃત વપરાશકર્તા", 
						'ml' => "ഒരു അംഗീകരിക്കുക ഉപയോക്താവ്", 
						'kn' => "ಅಲ್ಲ ದೃಢೀಕರಣಗೊಳಿಸುವ ಬಳಕೆದಾರರ"
						];

const UPDATE_SCHOOL_INVENTORY_MSG = [
						'en' => "Received successfully", 
						'hi' => "सफलतापूर्वक प्राप्त",
						'mr' => "यशस्वीरित्या प्राप्त झाली", 
						'bn' => "রেসিভড   সুকসেসফুল্লি", 
						'as' => "সফলভাবে গৃহীত",
						'ta' => "வெற்றிகரமாகப் பெறப்பட்டது",
						'gu' => "સફળતાપૂર્વક પ્રાપ્ત થઈ",
						'ml' => "വിജയകരമായി സ്വീകരിച്ചു",
						'kn' => "ಯಶಸ್ವಿಯಾಗಿ ಸ್ವೀಕರಿಸಿದೆ"
						];

const MIS_SUCCESS_MSG = [
						'en' => "School MIS data inserted successfully",
						'hi' => "स्कूल एमआईएस डेटा को सफलतापूर्वक डाला", 
						'mr' => "शाळा व्यवस्थापन डेटा यशस्वीपणे समाविष्ट", 
						'bn' => "স্কুল  মিস  ডাটা  ইন্সর্টেড  সুকসেসফুল্লি", 
						'as' => "স্কুল এমআইএস ডেটা সফলভাবে ঢোকানো",
						'ta' => "பள்ளி எம்ஐஎஸ் தரவும் வெற்றிகரமாக செருகிய",
						'gu' => "શાળા એમઆઇએસ ડેટા સફળતાપૂર્વક શામેલ",
						'ml' => "വിജയകരമായി ചേർത്ത സ്കൂൾ MIS ഡാറ്റ",
						'kn' => "ಸ್ಕೂಲ್ ಎಂ.ಐ.ಎಸ್ ಡೇಟಾವನ್ನು ಯಶಸ್ವಿಯಾಗಿ ಸೇರಿಸಲಾಗಿದೆ"
						];

const SESSION_ID = 'session_id';
const RESPONSE_CODE = 'responseCode';
const CSRF_TOKEN = 'csrf_token';
const LANGUAGE_CODE = 'language_code';	
const SEWING_SCHOOL_STATUS = [0 => 'Pending For Approval', 1=> 'Approved', 2 => 'Rejected', 3 => 'Terminated', 4 => 'Modified Need Approval', 6 => 'On Hold' ];
const SILAI_SCHOOL_STATUS = [0=> 'Pending for Approval', 1=> 'Approved', 4 => 'Fully Closed', 3 => 'Partially Closed', 2 => 'Rejected'];
const REVENUE_HEAD_AFFILIATION_FEE_NID = 97318;
const REVENUE_HEAD_RENEWAL_FEE_NID = 97319;
const REVENUE_HEAD_PROSPECTUS_FEE_NID = 97320;
const REVENUE_HEAD_STUDENT_FEE_NID = 97322;
const PAYMENT_MODE_OPTIONS = [0 => 'Pay via NEFT', 1 => 'Pay via cheque/DD', 2 => 'Pay via Cash'];
const EXAM_PASSING_EXIT_CODE = 58;
//const SCHOOL_TYPE_COMPANY_RUN = 24;
const CERTIFICATE_ISSUED = 1;
const NOT_ON_ROLL_STATUS = 0;

const FINANCIAL_YEAR_SEWING_SCHOOL = [40 => '2017-18', 39 => '2018-19', 37 => '2019-20', 38 => '2020-21'];
const FINANCIAL_YEAR_SEWING_SCHOOL_NEW = [40 => '2017-2018', 39 => '2018-2019', 37 => '2019-2020', 38 => '2020-2021', 69 => '2010-2011', 70 => '2011-2012', 71 => '2012-2013', 72 => '2013-2014', 73 => '2014-2015', 74 => '2015-2016', 75 => '2016-2017'];
const AREA_OF_OPERATION_SEWING_SCHOOL = [62 => 'Residential', 63 => 'Commercial'];
const AREA_RANGE_SEWING_SCHOOL = [64 => '<200 ft', 65 => '200-400 ft', 66 => '400-550 ft', 67 => '400-700 ft', 68 => '>700 ft'];
const GENDER_SEWING_STUDENT = [1 => 'M', 2 => 'F'];
const MARITAL_STATUS_SEWING_STUDENT = [1 => 'U', 2 => 'M'];
const COURSE_TYPE_SEWING_STUDENT = [42 => 'B', 41 => 'R'];
const EXAM_APPEAR_SEWING_STUDENT = [1 => 'F', 2 => 'R'];
const EXAM_RESULT_SEWING_STUDENT = [1 => 'P', 2 => 'F'];
const GRADES_SEWING_STUDENT = [43 => 'F', 44 => 'S', 45 => 'T'];
const CERTIFICATION_ISSUED_SEWING_STUDENT = [1 => 'Y', 0 => 'N'];
const EXISTING_SEWING_MACHINE_BRANDS_SEWING_SCHOOL = [46 => 'usha', 47 => 'Usha Janome', 48 => 'singer', 49 => 'brother', 50 => 'Other'];
const WANT_TO_BUYNEW_SEWING_STUDENT = [1 => 'Yes', 0 => 'No'];
const MODELMAKE_SEWING_STUDENT = [1 => 'Usha', 2 => 'Usha Janome' , 3 => 'Other'];
const STATUS_ONROLL_SEWING_STUDENT = [1 => 'ON ROLL', 0 => 'NOT ON ROLL'];
const EXIT_CODE_SEWING_SCHOOL = [61 => '4', 59 => '2', 58 => '1', 60 => '3'];
const FINANCIAL_YEAR_SEWING_STUDENT = [26 => '2017', 27 => '2018', 28 => '2019'];
const FUTURE_PLANCOURSES_SEWING_STUDENT = [51 => 'Design and Sew for Self Use', 52 => 'Do further Studies in Sewing', 53 => 'Entrepreneur - Start Own Business', 54 => 'Hobby', 55 => 'Not Decided', 56 =>'Pursue a job in Sewing', 57 => 'Teach Sewing'];
const INVENTORYSTATUSARRAY_SEWING = ['1' => 'Pending with SSI', '2' => 'Received By SSI', '3' => 'Forwarded By SSI', '4' => 'Forwarded & Partially Received', '5' => 'Received by School'];
const INVENTORYSTATUSARRAY_SEWING2 = ['1' => 'Pending', '2' => 'Received By SSI', '3' => 'Forwarded By SSI', '4' => 'Forwarded & Partially Received', '5' => 'Received by School'];
const PARTIALLY_FORWARDED_RECEIVED_STATUS = '4';
const USER = 'user';

const SEWING_WEEKLY_MIS_FIELDS = ['week_start_date' =>'Select Week Start Date',
								 
								  'num_school_visited_week' =>'Number of sewing schools visited this week',
								  'school_code_visited_week' =>'School code(s) where you visited this week ( Use Comma if there are more than one school)',
								  
								  'black_machines_sold_through_sewing_schools' =>'Straight Stitch Machines sold through Sewing schools',
								  'black_machine_model_name' =>'Straight Stitch machine model Name(s)',
								  'white_machines_sold_sewing_schools' =>'Usha Janome Machines sold through Sewing schools',
								  'white_machine_model_name' =>'Usha Janome machine model name(s)',
								  
								  'details_of_uj_accessories' =>'Details of the UJ accessories',
								  
								  'num_students_enrolled_week' =>'No of students enrolled this week',
								  'num_training_enrolled_week' =>'No. of Training done this week',
								 
								];
const SEWING_UPLOAD_WEEKLY_MIS_FIELDS = ['week_start_date',   
										'num_onroll_schools_till_date',
										'num_school_open_during_week',
										'num_school_terminated_during_week',
										'num_school_visited_week',
										'school_code_visited_week',
										'num_new_partnership_explored',
										'details_new_partnership_explored',
										'black_machines_sold_through_sewing_schools',
										'black_machine_model_name' ,
										'white_machines_sold_sewing_schools' ,
										'white_machine_model_name',
										'total_cerf_received_from_head_office_week',
										'total_cerf_issued_students',
										'feedback_from_school',
										'comment',
										'total_revenue_from_school_account_department_a',
										'total_revenue_from_school_account_department_b',
										'total_expense_incurred_during_week',
										'total_sale_uj_accessories_week',
										'details_of_uj_accessories',
										'total_books_received_office_week',
										'total_board_received_office_week',
										'difference_amount',
										'revenue_reconciliation',
										'num_students_enrolled_week',
										'num_training_enrolled_week',
										];								

const SEWING_WEEKLY_MIS_FIELDS_VILIDATION = ['Select Week Start Date',
											
											
											
											'Number of sewing schools visited this week',
											'School code(s) where you visited this week',
											
											
											'Straight Stitch Machines sold through Sewing schools',
											'Straight Stitch machine model Name(s)',
											'Usha Janome Machines sold through Sewing schools',
											'Usha Janome machine model name(s)',
											
											'Details of the UJ accessories',
											
											
											'No. of students enrolled this week',
											'No. of Training done this week'
											];
const BULK_UPLOAD_NO_YES_OPTIONS = [0 => 'No', 1 => 'Yes'];	
const TBL_USHA_STUDENT_FEE_RECEIPT = 'usha_student_fee_receipt';									
const TBL_USHA_GENERATE_FEE_RECEIPT = 'usha_generate_fee_receipt';	
const REVENUE_BULK_PAYMENT_TYPE_REVENUE_HEAD = ['ST' => 97322, 'PS' => 97320, 'AF' => 97318, 'RN' => 97319, 'EX' => 176356, 'SC' => 176359, 'MD' => 176358, 'FA' => 176357]; 									
const REVENUE_BULK_PAYMENT_MODE = ['CHEQ' => 1, 'DD' => 1, 'NEFT' => 3, 'CASH' => 2];
const TABLE_SILAI_SCHOOL_ADITIONAL_DATA = 'silai_add_school_data';	
const SCHOOL_TYPE_COMPANY_RUN = 71535;	
const GENDER_SEWING_TEACHER = [1 => 'Male', 2 => 'Female'];
const QUALIFICATION_SEWING_TEACHER = [82 => 'UG', 83 => 'PG', 81 => 'GR'];
const SKILL_LEVEL_SEWING_TEACHER = [77 => 1, 78 => 2, 79 => 3, 80 => 4];
const SEWING_BULK_SCHOOL_TYPE = [180400 => 'CORP', 180401 => 'NGO', 71536 => 'AR', 71534 => 'AF', 71535 => 'CO'];
const MONTHLY_QUARTERLY_TYPE_OPTIONS_VIEW = [0 => 'Monthly', 1 => 'Quarterly'];
const MONTHLY_QUARTERLY_SCHOOL_WORKING_STATUS = ['RUNNING' => 'RUNNING', 'TEMPORARILY CLOSED' => 'TEMPORARILY CLOSED', 'PERMANENTLY CLOSED' => 'PERMANENTLY CLOSED'];
const MONTHLY_QUARTERLY_REASON_NON_WORKING_OF_SCHOOL = [
							'NO LEARNERS' => 'NO LEARNERS', 
							'NO INCOME' => 'NO INCOME', 
							'ENGAGE IN SOME OTHER WORK' => 'ENGAGE IN SOME OTHER WORK', 
							'HEALTH ISSUES' => 'HEALTH ISSUES', 
							'LEARNERS EXHAUSTED' => 'LEARNERS EXHAUSTED', 
							'MARRIAGE' => 'MARRIAGE', 
							'PREGNANCY' => 'PREGNANCY', 
							'FAMILY ISSUES' => 'FAMILY ISSUES', 
							'UNREACHABLE' => 'UNREACHABLE', 
							'LEFT VILLAGE' => 'LEFT VILLAGE', 
							'TEACHER IS NO MORE' => 'TEACHER IS NO MORE', 
							'ANY OTHER REASON' => 'ANY OTHER REASON', 
							'NOT APPLICABLE' => 'NOT APPLICABLE',
						];
const MONTHLY_QUARTERLY_IF_NO_THEN_WHAT_IS_THE_PROBLEM = [
							'' => '- Select a Value -',
							'NO ISSUES' => 'NO ISSUES', 
							'SKIPPED STITCHES' => 'SKIPPED STITCHES', 
							'STITCHES OF UNEVEN LENGTH' => 'STITCHES OF UNEVEN LENGTH', 
							'FABRIC PUCKERING' => 'FABRIC PUCKERING', 
							'FABRIC NOT FEEDING CORRECTLY' => 'FABRIC NOT FEEDING CORRECTLY', 
							'TANGLED THREAD' => 'TANGLED THREAD', 
							'DOES NOT FEED ON STRAIGHT LINE' => 'DOES NOT FEED ON STRAIGHT LINE', 
							'ANY OTHER ISSUE' => 'ANY OTHER ISSUE' 
						];

const MONTHLY_QUARTERLY_ACTIVITIES_DONE_FOR_INCREASING_LEARNERS = [
							'' => '- Select a Value -',
							'DOOR TO DOOR CONTACT' => 'DOOR TO DOOR CONTACT', 
							'CAMPAIGN IN GIRLS SCHOOLS' => 'CAMPAIGN IN GIRLS SCHOOLS', 
							'COMMUNITY MEETING' => 'COMMUNITY MEETING', 
							'PANCHAYAT MEETING' => 'PANCHAYAT MEETING', 
							'SHG MEETING' => 'SHG MEETING', 
							'ANY OTHER' => 'ANY OTHER', 
						];
const MONTHLY_QUARTERLY_WHERE_DO_STUDENTS_PRACTICE = [
							'' => '- Select a Value -',
							'OWN HOUSE' => 'OWN HOUSE',
							'EXISTING SILAI SCHOOL' => 'EXISTING SILAI SCHOOL', 
							'NEIGHBOURS HOUSE' => 'NEIGHBOURS HOUSE', 
							'TAILORING SHOP' => 'TAILORING SHOP', 
							'COMMUNITY CENTRE' => 'COMMUNITY CENTRE', 
							'ANY OTHER' => 'ANY OTHER', 
						];
/** ADD New Student Bulk Import Option **/
const ADD_STUDENT_BULK_IMPORT_SALUTATION = [
							'Mr.' => 'Mr.',
							'Ms.' => 'Ms.',
							'Mrs.' => 'Mrs.', 
							'Kr.' => 'Ku.', 
						];
const ADD_STUDENT_BULK_IMPORT_GENDER = [1 => 'Male.', 2 => 'Female'];
const ADD_STUDENT_BULK_IMPORT_QUALIFICATION = [
							'UG' => 'UNDER GRADUATE',
							'GR' => 'GRADUATE',
							'PG' => 'POST GRADUATE',
							'GFD' => 'GRADUATE FASHION DESIGN',
						];
const ADD_STUDENT_BULK_IMPORT_YES_NO_OPTION = [0 => 'No', 1 => 'Yes'];
const ADD_STUDENT_BULK_IMPORT_EXISTING_SEWING_MACHINE_BRANDS = [49 => 'Brother', 76 => 'None', 50 => 'Other', 48 => 'Singer', 46 => 'Usha', 47 => 'Usha Janome'];
const ADD_STUDENT_BULK_IMPORT_MODEL_MAKE = [3 => 'Other', 1 => 'Usha', 2 => 'Usha Janome'];
const ADD_STUDENT_BULK_IMPORT_TIME_TO_BUY = [
										1 => 'Immediate',
										2 => 'Within two months',
										3 => 'After Completing the course',
										4 => 'After three months',
										5 => 'After four months',
										6 => 'After six months',
										7 => 'After nine months'
									];
const ADD_STUDENT_BULK_IMPORT_FUTURE_PLAN = [
									51 => 'Design garments for self', 
									52 => 'Further studies in Sewing Design', 
									53 => 'Start Own Business', 
									54 => 'Hobby', 
									55 => 'Not Decided', 
									56 => 'Pursue a job in Sewing', 
									57 => 'Teach Sewing'
								];




							