<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require dirname(__DIR__).'/admin/includes/bootstrap.php';
if(!config('mail_enabled')||query("SELECT setting_value FROM system_settings WHERE setting_key='notifications_enabled'")->fetchColumn()!=='1'){echo "Notification delivery is paused. Records remain saved.\n";exit;}
$count=0;
for($i=0;$i<25;$i++){
 $processed=transaction(function()use(&$count){$n=query('SELECT * FROM notification_queue WHERE sent_at IS NULL AND attempts<5 AND available_at<=UTC_TIMESTAMP() ORDER BY id LIMIT 1 FOR UPDATE')->fetch();if(!$n)return false;
  $body='A new '.str_replace('_',' ',$n['event_type']).' record #'.$n['entity_id']." is ready for staff review.\nSign in securely: ".url('admin/')."\nNo applicant or household details are included in this email.";
  $sent=send_mail((string)config('recipient'),'New TCF website submission',$body);
  if($sent){query('UPDATE notification_queue SET sent_at=UTC_TIMESTAMP(),attempts=attempts+1 WHERE id=?',[$n['id']]);$count++;}
  else{query('UPDATE notification_queue SET attempts=attempts+1,available_at=DATE_ADD(UTC_TIMESTAMP(),INTERVAL 15 MINUTE) WHERE id=?',[$n['id']]);audit('notification_delivery_failed',$n['event_type'],(int)$n['entity_id']);}
  return true;
 });if(!$processed)break;
}
echo $count." notification(s) accepted by the mail transport. Verify inbox delivery separately.\n";
