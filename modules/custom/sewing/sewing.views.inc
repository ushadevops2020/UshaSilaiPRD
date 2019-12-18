<?php
function sewing_views_data() { 
  $data[TABLE_CUSTOM_MANAGE_INVENTORY_SEWING][TABLE][GROUP] = t('Manage Inventory Table Sewing- Custom');
  $data[TABLE_CUSTOM_MANAGE_INVENTORY_SEWING][TABLE][JOIN] = array(
    TABLE_NODE_FIELD_DATA => [
      LEFT_FIELD => NID,
      FIELD => NID,
    ],
  );

   $data[TABLE_CUSTOM_MANAGE_INVENTORY_SEWING][NID] = array(
    TITLE => t('Inventory nid'),
    HELP => t('Inventory content that references a node.'),
    RELATIONSHIP => array(
      BASE => TABLE_NODE_FIELD_DATA, 
      TEXT_BASE_FIELD => NID,
      FIELD => NID, 
      ID => STANDARD,
      HANDLER => VIEW_HANDLER_RELATIONSHIP,
      LABEL => t('Inventory Node'),
    ),
   );


  $data[TABLE_CUSTOM_MANAGE_INVENTORY_SEWING]['id'] = array(
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

 
  $data[TABLE_CUSTOM_MANAGE_INVENTORY_SEWING]['sender_id'] = array(
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

  $data[TABLE_CUSTOM_MANAGE_INVENTORY_SEWING]['receiver_id'] = array(
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

  $data[TABLE_CUSTOM_MANAGE_INVENTORY_SEWING]['qty_send'] = array(
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

  $data[TABLE_CUSTOM_MANAGE_INVENTORY_SEWING]['qty_received'] = array(
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

  $data[TABLE_CUSTOM_MANAGE_INVENTORY_SEWING]['location_id'] = array(
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

  $data[TABLE_CUSTOM_MANAGE_INVENTORY_SEWING]['status'] = array(
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

  $data[TABLE_CUSTOM_MANAGE_INVENTORY_SEWING]['sender_role'] = array(
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

  $data[TABLE_CUSTOM_MANAGE_INVENTORY_SEWING]['receiver_role'] = array(
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


  $data[TABLE_CUSTOM_MANAGE_INVENTORY_SEWING]['sent_date'] = array(
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

  $data[TABLE_CUSTOM_MANAGE_INVENTORY_SEWING]['received_date'] = array(
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

$data[TABLE_CUSTOM_MANAGE_INVENTORY_SEWING]['ref_id'] = array(
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

  $data[TABLE_CUSTOM_MANAGE_INVENTORY_SEWING]['total_forwarded'] = array(
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

  $data[TABLE_CUSTOM_MANAGE_INVENTORY_SEWING]['qty_received_school'] = array(
    TITLE => t('Quantity Received School'),
    HELP => t('qty_received_school'),
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

  $data[TABLE_CUSTOM_MANAGE_INVENTORY_SEWING]['qty_received_school_date'] = array(
    TITLE => t('School Received Date'),
    HELP => t(' School Received Date'),
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

  return $data;
}
