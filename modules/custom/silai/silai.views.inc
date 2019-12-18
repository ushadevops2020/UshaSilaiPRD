<?php
function silai_views_data() { 
  $data[TABLE_SILAI_TRAINEE_FEEDBACK][TABLE][GROUP] = t('Silai Trainee Feedback Table - Custom');
  $data[TABLE_SILAI_TRAINEE_FEEDBACK][TABLE][JOIN] = array(
    TABLE_NODE_FIELD_DATA => [
      LEFT_FIELD => NID,
      FIELD => NID,
    ],
  );

   $data[TABLE_SILAI_TRAINEE_FEEDBACK][NID] = array(
    TITLE => t('Feedback nid'),
    HELP => t('Trainee content that references a node.'),
    RELATIONSHIP => array(
      BASE => TABLE_NODE_FIELD_DATA,
      TEXT_BASE_FIELD => NID,
      FIELD => NID, 
      ID => STANDARD,
      HANDLER => VIEW_HANDLER_RELATIONSHIP,
      LABEL => t('Feedback node'),
    ),
   );

  $data[TABLE_SILAI_TRAINEE_FEEDBACK]['silai_r_communication'] = array(
    TITLE => t('Communication'),
    HELP => t('silai_r_communication.'),
    FIELD => array(
      ID => NUMERIC,
    ),
    FILTER => array(
      ID => NUMERIC,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );


  $data[TABLE_SILAI_TRAINEE_FEEDBACK]['silai_r_entrepreneurship'] = array(
    TITLE => t('Entrepreneurship'),
    HELP => t('silai_r_entrepreneurship'),
    FIELD => array(
      ID => NUMERIC,
    ),
    FILTER => array(
      ID => NUMERIC,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

  $data[TABLE_SILAI_TRAINEE_FEEDBACK]['silai_r_creativity'] = array(
    TITLE => t('Creativity'),
    HELP => t('silai_r_creativity'),
    FIELD => array(
      ID => NUMERIC,
    ),
    FILTER => array(
      ID => NUMERIC,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  ); 

  $data[TABLE_SILAI_TRAINEE_FEEDBACK]['silai_r_average_rating'] = array(
    TITLE => t('Average rating'),
    HELP => t('silai_r_average_rating'),
    FIELD => array(
      ID => NUMERIC,
    ),
    FILTER => array(
      ID => NUMERIC,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  ); 


  $data[TABLE_CUSTOM_MANAGE_INVENTORY][TABLE][GROUP] = t('Manage Inventory Table - Custom');
  $data[TABLE_CUSTOM_MANAGE_INVENTORY][TABLE][JOIN] = array(
    TABLE_NODE_FIELD_DATA => [
      LEFT_FIELD => NID,
      FIELD => NID,
    ],
  );

   $data[TABLE_CUSTOM_MANAGE_INVENTORY][NID] = array(
    TITLE => t('Inventory nid'),
    HELP => t('Inventory content that references a node.'),
    RELATIONSHIP => array(
      BASE => TABLE_NODE_FIELD_DATA, 
      TEXT_BASE_FIELD => NID,
      FIELD => NID, 
      ID => STANDARD,
      HANDLER => VIEW_HANDLER_RELATIONSHIP,
      LABEL => t('Feedback node'),
    ),
   );


  $data[TABLE_CUSTOM_MANAGE_INVENTORY]['id'] = array(
    TITLE => t('Inventory custom table id'),
    HELP => t('Inventory custom table id.'),
   FIELD => array(
      ID => NUMERIC,
    ),
    FILTER => array(
      ID => NUMERIC,
    ),
    ARGUMENT => array(
      ID => NUMERIC,
    ),
   );

 
  $data[TABLE_CUSTOM_MANAGE_INVENTORY]['sender_id'] = array(
    TITLE => t('Sender id'),
    HELP => t('sender_id'),
    FIELD => array(
      ID => NUMERIC,
    ),
    FILTER => array(
      ID => NUMERIC,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );  

  $data[TABLE_CUSTOM_MANAGE_INVENTORY]['receiver_id'] = array(
    TITLE => t('Receiver Id'),
    HELP => t('receiver_id'),
    FIELD => array(
      ID => NUMERIC,
    ),
    FILTER => array(
      ID => NUMERIC,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

  $data[TABLE_CUSTOM_MANAGE_INVENTORY]['qty_send'] = array(
    TITLE => t('Quantity Send'),
    HELP => t('qty_send'),
    FIELD => array(
      ID => NUMERIC,
    ),
    FILTER => array(
      ID => NUMERIC,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  ); 

  $data[TABLE_CUSTOM_MANAGE_INVENTORY]['qty_received'] = array(
    TITLE => t('Quantity Received'),
    HELP => t('qty_received'),
    FIELD => array(
      ID => NUMERIC,
    ),
    FILTER => array(
      ID => NUMERIC,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

  $data[TABLE_CUSTOM_MANAGE_INVENTORY]['location_id'] = array(
    TITLE => t('Location Id'),
    HELP => t('location_id'),
    FIELD => array(
      ID => NUMERIC,
    ),
    FILTER => array(
      ID => NUMERIC,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  ); 

  $data[TABLE_CUSTOM_MANAGE_INVENTORY]['status'] = array(
    TITLE => t('Inventory Status'),
    HELP => t('status'),
    FIELD => array(
      ID => NUMERIC,
    ),
    FILTER => array(
      ID => NUMERIC,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  ); 

  $data[TABLE_CUSTOM_MANAGE_INVENTORY]['sender_role'] = array(
    TITLE => t('Inventory Sender Role'),
    HELP => t('sender role'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  ); 

  $data[TABLE_CUSTOM_MANAGE_INVENTORY]['receiver_role'] = array(
    TITLE => t('Inventory Receiver Role'),
    HELP => t('receiver role'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    'filter' => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );


  $data[TABLE_CUSTOM_MANAGE_INVENTORY]['sent_date'] = array(
    TITLE => t('Sent Date'),
    HELP => t('Sent Date'),
    FIELD => array(
      ID => DATEFIELD,
    ),
    SORT => array(
      ID => DATEFIELD,
    ),
    FILTER => array(
      ID => DATEFIELD,
    ),
  );

  $data[TABLE_CUSTOM_MANAGE_INVENTORY]['received_date'] = array(
    TITLE => t('Received Date'),
    HELP => t('Received Date'),
    FIELD => array(
      ID => DATEFIELD,
    ),
    SORT => array(
      ID => DATEFIELD,
    ),
    FILTER => array(
      ID => DATEFIELD,
    ),
  );

$data[TABLE_CUSTOM_MANAGE_INVENTORY]['ref_id'] = array(
    TITLE => t('Receiver Reference'),
    HELP => t('Receiver Reference'),
    FIELD => array(
      ID => NUMERIC,
    ),
    FILTER => array(
      ID => NUMERIC,
    ),
    ARGUMENT => array(
      ID => NUMERIC,
    ),
  ); 

  $data[TABLE_CUSTOM_MANAGE_INVENTORY]['total_forwarded'] = array(
    TITLE => t('Total Forwarded'),
    HELP => t('total forwarded'),
    FIELD => array(
      ID => NUMERIC,
    ),
    FILTER => array(
      ID => NUMERIC,
    ),
    ARGUMENT => array(
      ID => NUMERIC,
    ),
  ); 

  $data[TABLE_SILAI_ASSOCIATED_USER][TABLE][GROUP] = t('Silai NGO Associated User Table - Custom');
  $data[TABLE_SILAI_ASSOCIATED_USER][TABLE][JOIN] = array(
    TABLE_NODE_FIELD_DATA => [
      LEFT_FIELD => NID,
      FIELD => NID,
    ],
  );

   $data[TABLE_SILAI_ASSOCIATED_USER][NID] = array(
    TITLE => t('NGO nid'),
    HELP => t('NGO content that references a node.'),
    RELATIONSHIP => array(
      BASE => TABLE_NODE_FIELD_DATA, 
      TEXT_BASE_FIELD => NID,
      FIELD => NID, 
      ID => STANDARD,
      HANDLER => VIEW_HANDLER_RELATIONSHIP,
      LABEL => t('NGO node'),
    ),
   );

  
  $data[TABLE_SILAI_ASSOCIATED_USER]['user_id'] = array(
    TITLE => t('NGO Associated User id'),
    HELP => t('user_id.'),
    FIELD => array(
      ID => NUMERIC,
    ),
    FILTER => array(
      ID => NUMERIC,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

  $data[TABLE_SILAI_NGO_PAYMENT_DETAIL]['table']['group'] = t('Silai Agreement Payment detail - Custom');

  $data[TABLE_SILAI_NGO_PAYMENT_DETAIL]['table']['base'] = array(
    FIELD => ID, 
    TITLE => t('Silai Agreement Payment detail'),
    'weight' => -10,
  );

  $data[TABLE_SILAI_NGO_PAYMENT_DETAIL][TABLE][JOIN] = array(
    TABLE_NODE_FIELD_DATA => [
      LEFT_FIELD => NID,
      FIELD => NID,
    ],
  );

   $data[TABLE_SILAI_NGO_PAYMENT_DETAIL][NID] = array(
    TITLE => t('Agreemnt Node nid'),
    HELP => t('Agreemnt Node that references a node.'),
    RELATIONSHIP => array(
      BASE => TABLE_NODE_FIELD_DATA, 
      TEXT_BASE_FIELD => NID,
      FIELD => NID, 
      ID => STANDARD,
      HANDLER => 'views_handler_relationship',
      LABEL => t('Package node'),
    ),
   );

  $data[TABLE_SILAI_NGO_PAYMENT_DETAIL][ID] = array(
    TITLE => t('Payment Id'),
    HELP => t('id.'),
    FIELD => array(
      ID=> NUMERIC,
    ),
    FILTER => array(
      ID => NUMERIC,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    ARGUMENT => array(
      ID => NUMERIC,
    ),
  );

   $data[
   ]['cararr_id'] = array(
    TITLE => t('Cararr Id'),
    HELP => t('Cararr Id.'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

  $data[TABLE_SILAI_NGO_PAYMENT_DETAIL]['amount'] = array(
    TITLE => t('NGO Received Amount'),
    HELP => t('NGO Received Amount.'),
    FIELD => array(
      ID => NUMERIC,
    ),
    FILTER => array(
      ID => NUMERIC,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    ARGUMENT => array(
      ID => NUMERIC,
    ),
  );

  $data['send_message']['table']['group'] = t('sent message details - Custom');
  $data['send_message']['table']['base'] = array(
    'field' => 'id',
    TITLE => t('send message table'),
    'weight' => -10,
  );

  $data['send_message'][TABLE][JOIN] = array(
    TABLE_NODE_FIELD_DATA => [
      LEFT_FIELD => 'location',
      FIELD => NID,
    ],
  );
  $data['send_message'][NID] = array(
    TITLE => t('location relationship title'),
    RELATIONSHIP => array(
      BASE => TABLE_NODE_FIELD_DATA, 
      TEXT_BASE_FIELD => NID,
      FIELD => 'location', 
      ID => STANDARD,
      HANDLER => 'views_handler_relationship',
      LABEL => t('location node'),
    ),
  );

  $data['send_message'][TABLE][JOIN] = array(
    'users_field_data' => [
      LEFT_FIELD => 'receiver_id',
      FIELD => 'uid',
    ],
  );
  $data['send_message']['uid'] = array(
    TITLE => t('users field data'),
    RELATIONSHIP => array(
      BASE => 'users_field_data', 
      TEXT_BASE_FIELD => 'uid',
      FIELD => 'receiver_id', 
      ID => STANDARD,
      HANDLER => 'views_handler_relationship',
      LABEL => t('user field node'),
    ),
  );

# For School survey Questionire ---- Start-----
  $data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA][TABLE][GROUP] = t('Silai School Additional Data Table - Custom');
  $data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA][TABLE][JOIN] = array(
    TABLE_NODE_FIELD_DATA => [
      LEFT_FIELD => NID,
      FIELD => NID,
    ],
  );

  $data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA][NID] = array(
    TITLE => t('Silai School nid'),
    HELP => t('Silai School content that references a node.'),
    RELATIONSHIP => array(
      BASE => TABLE_NODE_FIELD_DATA, 
      TEXT_BASE_FIELD => NID,
      FIELD => NID, 
      ID => STANDARD,
      HANDLER => VIEW_HANDLER_RELATIONSHIP,
      LABEL => t('Silai School Additional Data'),
    ),
   );

  $data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['cast'] = array(
    TITLE => t('Silai School Teacher Cast'),
    HELP => t('Silai School Teacher Cast'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

  $data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['status_date'] = array(
    TITLE => t('Silai School Status Date'),
    HELP => t('Silai School Status Date'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  ); 

  $data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['pin_code'] = array(
    TITLE => t('Silai School Pin Code'),
    HELP => t('Silai School Pin Code'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  ); 

  $data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['marital_status'] = array(
    TITLE => t('Silai School Teacher Marital Status'),
    HELP => t('Silai School Teacher Marital Status'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

  $data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['religion'] = array(
    TITLE => t('Silai School Teacher Religion'),
    HELP => t('Silai School Teacher Religion'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  ); 

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['cast_category'] = array(
    TITLE => t('Silai School Teacher Cast Category'),
    HELP => t('Silai School Teacher Cast Category'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['any_special_category'] = array(
    TITLE => t('Silai School Teacher Special Category'),
    HELP => t('Silai School Teacher Special Category'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

  $data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['have_bank_account'] = array(
    TITLE => t('Silai School Teacher have Bank Account'),
    HELP => t('Silai School Teacher have Bank Account'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

  $data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['bank_name'] = array(
    TITLE => t('Silai School Teacher Bank Name'),
    HELP => t('Silai School Teacher Bank Name'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

  $data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['have_pan_card'] = array(
    TITLE => t('Silai School Teacher have PAN Card'),
    HELP => t('Silai School Teacher have PAN Card'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

  $data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['have_aadhar_card'] = array(
    TITLE => t('Silai School Teacher have Aadhar Card'),
    HELP => t('Silai School Teacher have Aadhar Card'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['aadhar_number'] = array(
    TITLE => t('Silai School Teacher Aadhar Number'),
    HELP => t('Silai School Teacher Aadhar Number'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['associated_with_any_mfi'] = array(
    TITLE => t('Silai School Teacher Associated with Any MFI'),
    HELP => t('Silai School Teacher Associated with Any MFI'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['mfi_number'] = array(
    TITLE => t('Silai School Teacher MFI Number'),
    HELP => t('Silai School Teacher MFI Number'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['father_name'] = array(
    TITLE => t('Silai School Teacher Father Name'),
    HELP => t('Silai School Teacher Father Name'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['father_occupation'] = array(
    TITLE => t('Silai School Teacher Father Occupation'),
    HELP => t('Silai School Teacher Father Occupation'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['family_income'] = array(
    TITLE => t('Silai School Teacher Family Income'),
    HELP => t('Silai School Teacher Family Income'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['ration_card'] = array(
    TITLE => t('Silai School Teacher Ration card'),
    HELP => t('Silai School Teacher Ration card'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['type_of_ration_card'] = array(
    TITLE => t('Silai School Teacher Type of Ration card'),
    HELP => t('Silai School Teacher Type of Ration card'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['have_gas_connection'] = array(
    TITLE => t('Silai School Teacher have Gas connection'),
    HELP => t('Silai School Teacher have Gas connection'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['other_mifs_working_your_area'] = array(
    TITLE => t('Silai School Other Mifs working'),
    HELP => t('Silai School Other Mifs working'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['getting_benefitted_government_schemes'] = array(
    TITLE => t('Silai School Teacher Getting Govt Scheme benefit'),
    HELP => t('Silai School Teacher Getting Govt Scheme benefit'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['kindly_give_government_benefitted_details'] = array(
    TITLE => t('Silai School Teacher Govt benefit detail'),
    HELP => t('Silai School Teacher Govt benefit detail'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['electricity_status_in_home'] = array(
    TITLE => t('Silai School Teacher Home Electricity status'),
    HELP => t('Silai School Teacher Home Electricity status'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['average_electricity_hours'] = array(
    TITLE => t('Silai School Avg Electricity hours'),
    HELP => t('Silai School Avg Electricity hours'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['have_solar_panel'] = array(
    TITLE => t('Silai School have Solar panel'),
    HELP => t('Silai School have Solar panel'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['usage_of_drinking_water'] = array(
    TITLE => t('Silai School drinking water usage'),
    HELP => t('Silai School drinking water usage'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['appliances_in_the_home'] = array(
    TITLE => t('Silai School Teacher home appliances'),
    HELP => t('Silai School Teacher home appliances'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['type_of_your_house'] = array(
    TITLE => t('Silai School Teacher House Type'),
    HELP => t('Silai School Teacher House Type'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['before_silai_school_household'] = array(
    TITLE => t('Silai School Teacher household before'),
    HELP => t('Silai School Teacher household before'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['after_silai_school_household'] = array(
    TITLE => t('Silai School Teacher household after'),
    HELP => t('Silai School Teacher household after'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['total_boys'] = array(
    TITLE => t('Silai School Total boys'),
    HELP => t('Silai School Total boys'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['total_girls'] = array(
    TITLE => t('Silai School Total girls'),
    HELP => t('Silai School Total girls'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['use_mobile_phone'] = array(
    TITLE => t('Silai School Teacher use Mobile?'),
    HELP => t('Silai School Teacher use Mobile?'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['mobile_number'] = array(
    TITLE => t('Silai School Teacher mobile number'),
    HELP => t('Silai School Teacher mobile number'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['alternative_mobile_number'] = array(
    TITLE => t('Silai School Teacher alt mobile number'),
    HELP => t('Silai School Teacher alt mobile number'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['type_of_mobile_phone'] = array(
    TITLE => t('Silai School Teacher type of Mobile phone'),
    HELP => t('Silai School Teacher type of Mobile phone'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['use_internet'] = array(
    TITLE => t('Silai School Teacher use internet?'),
    HELP => t('Silai School Teacher use internet?'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['use_whatsapp'] = array(
    TITLE => t('Silai School Teacher use whatsapp?'),
    HELP => t('Silai School Teacher use whatsapp?'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['have_email_id'] = array(
    TITLE => t('Silai School Teacher have email id?'),
    HELP => t('Silai School Teacher have email id?'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['mention_email'] = array(
    TITLE => t('Silai School Teacher email'),
    HELP => t('Silai School Teacher email'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['computer_in_family'] = array(
    TITLE => t('Silai School Teacher computer in family'),
    HELP => t('Silai School Teacher computer in family'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['completed_training_from_usha'] = array(
    TITLE => t('Silai School Teacher completed usha training'),
    HELP => t('Silai School Teacher completed usha training'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['if_yes_got_trainined'] = array(
    TITLE => t('Silai School Teacher training days'),
    HELP => t('Silai School Teacher training days'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['have_received_certificate'] = array(
    TITLE => t('Silai School Teacher received certificate?'),
    HELP => t('Silai School Teacher received certificate?'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['usha_black_machine_you_have'] = array(
    TITLE => t('Silai School Usha black machines'),
    HELP => t('Silai School Usha black machines'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['model_of_usha_black_machines'] = array(
    TITLE => t('Silai School Usha black machines model'),
    HELP => t('Silai School Usha black machines model'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['usha_white_machine_you_have'] = array(
    TITLE => t('Silai School Usha white machines'),
    HELP => t('Silai School Usha white machines'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['model_of_usha_white_machines'] = array(
    TITLE => t('Silai School Usha white machines model'),
    HELP => t('Silai School Usha white machines model'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['aadhar_attachment'] = array(
    TITLE => t('Silai School Teacher Aadhar attachment'),
    HELP => t('Silai School Teacher Aadhar attachment'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['pan_attachment'] = array(
    TITLE => t('Silai School Teacher PAN attachment'),
    HELP => t('Silai School Teacher PAN attachment'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['teacher_photograph_attachment'] = array(
    TITLE => t('Silai School Teacher photograph attachment'),
    HELP => t('Silai School Teacher photograph attachment'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['signage_in_the_school'] = array(
    TITLE => t('Silai School Usha signage?'),
    HELP => t('Silai School Usha signage?'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['type_of_signage_present'] = array(
    TITLE => t('Silai School Usha signage present'),
    HELP => t('Silai School Usha signage present'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['condition_of_signboard'] = array(
    TITLE => t('Silai School Signboard condition'),
    HELP => t('Silai School Signboard condition'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['how_many_learners_you_have_trained'] = array(
    TITLE => t('Silai School number of learners trained'),
    HELP => t('Silai School number of learners trained'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['how_many_non_usha_black_machines'] = array(
    TITLE => t('Silai School non usha black machines'),
    HELP => t('Silai School non usha black machine'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['where_is_the_silai_school'] = array(
    TITLE => t('Silai School situated?'),
    HELP => t('Silai School situated'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['average_learners_attending'] = array(
    TITLE => t('Silai School avg learners attending'),
    HELP => t('Silai School avg learners attending'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['average_fee_charged'] = array(
    TITLE => t('Silai School avg fee charged'),
    HELP => t('Silai School avg fee charged'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['monthly_income_learners_fee'] = array(
    TITLE => t('Silai School monthly income learners fee'),
    HELP => t('Silai School monthly income learners fee'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['monthly_income_stitching'] = array(
    TITLE => t('Silai School monthly income stitching'),
    HELP => t('Silai School monthly income stitching'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['income_from_sewing_machine_repairing'] = array(
    TITLE => t('Silai School income from sewing machine repairing'),
    HELP => t('Silai School income from sewing machine repairing'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['monthly_income_from_sale_of_dresses'] = array(
    TITLE => t('Silai School monthly income from sale of dresses'),
    HELP => t('Silai School monthly income from sale of dresses'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['monthly_income_from_silai_schools'] = array(
    TITLE => t('Silai School monthly income from silai schools'),
    HELP => t('Silai School monthly income from silai schools'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['stitching_related_order_work_completed'] = array(
    TITLE => t('Silai School stitching related order work completed?'),
    HELP => t('Silai School stitching related order work completed?'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['if_yes_order_completed'] = array(
    TITLE => t('Silai School stitching related order completed'),
    HELP => t('Silai School stitching related order completed'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );

$data[TABLE_SILAI_SCHOOL_ADITIONAL_DATA]['additional_notes'] = array(
    TITLE => t('Silai School additional_notes'),
    HELP => t('Silai School additional_notes'),
    FIELD => array(
      ID => STANDARD,
    ),
    SORT => array(
      ID => STANDARD,
    ),
    FILTER => array(
      ID => STRING_TEXT,
    ),
    ARGUMENT => array(
      ID => STRING_TEXT,
    ),
  );
# For School survey Questionire ---- End-----
  return $data;
}