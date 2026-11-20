<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require dirname(__DIR__).'/admin/includes/bootstrap.php';
query('DELETE FROM password_resets WHERE expires_at<DATE_SUB(UTC_TIMESTAMP(),INTERVAL 1 DAY)');
query('DELETE FROM submission_receipts WHERE created_at<DATE_SUB(UTC_TIMESTAMP(),INTERVAL 7 DAY)');
foreach(glob(private_path('rate-limits/*.json'))?:[] as $file)if(!is_link($file)&&filemtime($file)<time()-86400)unlink($file);
// Session garbage collection also runs under the same configured session save path.
start_session();session_gc();session_write_close();echo "Expired security records cleaned. Operational records and documents were not deleted.\n";
