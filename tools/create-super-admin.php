<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require dirname(__DIR__).'/admin/includes/bootstrap.php';
if($argc!==3){fwrite(STDERR,"Usage: php tools/create-super-admin.php EMAIL FULL_NAME < private-password-input\n");exit(1);}
if(function_exists('stream_isatty')&&stream_isatty(STDIN)){fwrite(STDERR,"Pipe the password on standard input; do not place passwords in command arguments. See ADMIN-SETUP.md.\n");exit(1);}
$email=email_value($argv[1]);$name=text_value(['name'=>$argv[2]],'name',160,true);$password=rtrim(fgets(STDIN)?:'',"\r\n");$hash=hash_password($password);unset($password);
// Database named lock prevents simultaneous first-account creation.
if((int)query("SELECT GET_LOCK('tcf_first_admin',10)")->fetchColumn()!==1)throw new RuntimeException('Setup lock unavailable');
try{transaction(function()use($email,$name,$hash){if(query('SELECT COUNT(*) FROM admin_users')->fetchColumn()>0)throw new ValidationError('First-account setup is already complete. Use the Users screen.');query("INSERT INTO admin_users(full_name,email,password_hash,role) VALUES(?,?,?,'super_admin')",[$name,$email,$hash]);audit('first_admin_created','admin_users',(int)db()->lastInsertId());});echo "First Super Admin created. Remove this setup script from the deployed site.\n";}finally{query("SELECT RELEASE_LOCK('tcf_first_admin')");}
