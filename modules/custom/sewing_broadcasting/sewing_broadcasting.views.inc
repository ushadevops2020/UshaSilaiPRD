<?php
function sewing_broadcasting_views_data() {
  $data['sewing_broadcasting']['table']['group'] = t('sent notification - Custom');
  $data['sewing_broadcasting']['table']['base'] = array(
    'field' => 'id',
    TITLE => t('send notification table'),
    'weight' => -10,
  );

  $data['sewing_broadcasting'][TABLE][JOIN] = array(
    TABLE_NODE_FIELD_DATA => [
      LEFT_FIELD => 'location',
      FIELD => NID,
    ],
  );
  $data['sewing_broadcasting'][NID] = array(
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
  $data['sewing_broadcasting'][TABLE][JOIN] = array(
    'users_field_data' => [
      LEFT_FIELD => 'sender_id',
      FIELD => 'uid',
    ],
  );
  $data['sewing_broadcasting']['uid'] = array(
    TITLE => t('users title'),
    RELATIONSHIP => array(
      BASE => 'users_field_data', 
      TEXT_BASE_FIELD => 'uid',
      FIELD => 'sender_id', 
      ID => STANDARD,
      HANDLER => 'views_handler_relationship',
      LABEL => t('user field node'),
    ),
  );
  return $data;
}
