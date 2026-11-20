<?php
// Committed loader only. Actual credentials belong in a private file outside this site.
$config=[
 'enabled'=>getenv('TCF_ENABLED')==='1','environment'=>getenv('TCF_ENV')?:'production',
 'base_url'=>getenv('TCF_BASE_URL')?:'https://thechorefacilitator.co.za',
 'private_dir'=>getenv('TCF_PRIVATE_DIR')?:'','app_key'=>getenv('TCF_APP_KEY')?:'',
 'db_host'=>getenv('TCF_DB_HOST')?:'localhost','db_port'=>(int)(getenv('TCF_DB_PORT')?:3306),
 'db_name'=>getenv('TCF_DB_NAME')?:'','db_user'=>getenv('TCF_DB_USER')?:'','db_password'=>getenv('TCF_DB_PASSWORD')?:'',
 'mail_enabled'=>getenv('TCF_MAIL_ENABLED')==='1','recipient'=>'admin@thechorefacilitator.co.za','sender'=>'admin@thechorefacilitator.co.za',
];
$file=getenv('TCF_CONFIG_FILE');
if($file){
 $actual=realpath($file);$root=realpath($_SERVER['DOCUMENT_ROOT']??__DIR__);
 $normal=fn($p)=>strtolower(rtrim(str_replace('\\','/',(string)$p),'/'));
 if(!$actual||!$root||str_starts_with($normal($actual),$normal($root).'/')||str_starts_with($normal($actual),$normal(__DIR__).'/'))throw new RuntimeException('Private configuration unavailable');
 $private=require $actual;if(!is_array($private))throw new RuntimeException('Invalid private configuration');$config=array_replace($config,$private);
}
return $config;
