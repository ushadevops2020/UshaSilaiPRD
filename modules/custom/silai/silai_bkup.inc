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
      ID => STRING_TEXT,
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
      ID => STRING_TEXT,
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
      FIELD => UID,
    ],
  );
  $data['send_message'][UID] = array(
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
  return $data;
}
