// Produces a public, static review package. Never includes PHP or hosting settings.
const fs=require('node:fs');
const path=require('node:path');
const root=__dirname,output=path.join(root,'_preview');
fs.mkdirSync(output,{recursive:true});
for(const file of fs.readdirSync(root)){
 if(!/\.(html|css|js)$/.test(file))continue;
 let content=fs.readFileSync(path.join(root,file),'utf8');
 if(file.endsWith('.html')){
  content=content.replace('<html lang="en-ZA">','<html lang="en-ZA" data-site-mode="preview">');
  content=content.replace(/<meta\s+name="robots"\s+content="[^"]*"\s*>/gi,'');
  content=content.replace('</head>','<meta name="robots" content="noindex, nofollow">\n</head>');
  content=content.replace('<base href="/">','<base href="/the-chore-facilitator/">');
  content=content.replace(/action="forms.php"/g,'action="#"');
  content=content.replace(/Online submission is being prepared\.[\s\S]*?if the form is unavailable\./g,'Client preview — form submissions and email delivery are disabled until the website launches.');
  // The preview is not the production business website and should not advertise it as such.
  content=content.replace(/<script type="application\/ld\+json">[\s\S]*?<\/script>/g,'');
 }
 fs.writeFileSync(path.join(output,file),content);
}
fs.cpSync(path.join(root,'images'),path.join(output,'images'),{recursive:true});
fs.writeFileSync(path.join(output,'robots.txt'),'User-agent: *\nDisallow: /\n');
fs.writeFileSync(path.join(output,'.nojekyll'),'');
console.log('Static preview built in _preview; no PHP or server configuration included.');
