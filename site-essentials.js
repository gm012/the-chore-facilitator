(() => {
 'use strict';
 const menu=document.getElementById('mainNav');
 const toggle=document.getElementById('menuToggle');
 const closeMenu=() => { menu?.classList.remove('open'); toggle?.setAttribute('aria-expanded','false'); toggle?.setAttribute('aria-label','Open navigation'); };
 toggle?.addEventListener('click',()=>toggle.setAttribute('aria-label',toggle.getAttribute('aria-expanded')==='true'?'Close navigation':'Open navigation'));
 document.addEventListener('keydown',event=>{if(event.key==='Escape'&&menu?.classList.contains('open')){closeMenu();toggle.focus();}});
 document.addEventListener('click',event=>{if(menu?.classList.contains('open')&&!menu.contains(event.target)&&!toggle?.contains(event.target))closeMenu();});
 menu?.addEventListener('focusout',()=>setTimeout(()=>{if(!menu.contains(document.activeElement)&&document.activeElement!==toggle)closeMenu();},0));
 const current=location.pathname.split('/').pop()||'index.html';
 menu?.querySelectorAll('a').forEach(a=>{if(a.getAttribute('href')===current)a.setAttribute('aria-current','page');});
 document.querySelectorAll('[data-back-to-top]').forEach(link=>link.addEventListener('click',event=>{
  event.preventDefault();
  const main=document.getElementById('main-content');
  main?.focus({preventScroll:true});
  window.scrollTo({top:0,behavior:matchMedia('(prefers-reduced-motion: reduce)').matches?'instant':'smooth'});
 }));
 // No optional trackers are installed. Do not imply that there are choices to consent to.
 // If that changes, gate their requests before loading them and replace this disclosure
 // with tested Accept/Reject/Manage and withdrawal controls before deployment.
 let cookieDialog;
 document.querySelectorAll('[data-cookie-settings]').forEach(button=>button.addEventListener('click',()=>{
  if(!cookieDialog){
   cookieDialog=document.createElement('dialog');
   cookieDialog.className='cookie-dialog';
   cookieDialog.setAttribute('aria-labelledby','cookie-dialog-title');
   cookieDialog.innerHTML='<h2 id="cookie-dialog-title">Cookie settings</h2><p><strong>No optional tracking is active.</strong> This website does not currently use analytics or advertising cookies.</p><p>If you use an online form, a temporary essential session cookie helps protect your submission. It is not used for advertising.</p><p>Fonts load from Google Fonts, which receives your connection information. Read our <a href="cookies.html">cookie policy</a> and <a href="privacy.html">privacy policy</a>.</p><button type="button" autofocus>Close</button>';
   document.body.append(cookieDialog);
   cookieDialog.querySelector('button').addEventListener('click',()=>cookieDialog.close());
  }
  cookieDialog.showModal();
 }));
 document.querySelectorAll('[data-enquiry-form]').forEach(async form=>{
  const submit=form.querySelector('button[aria-disabled]');
  const status=form.querySelector('.form-status');
  const kind=form.dataset.enquiryForm;
  let ready=false;
  const endpoint=new URL('forms.php',location.href);
  const report=message=>{status.textContent=message;};
  async function refreshToken(){
   const response=await fetch(endpoint+'?form='+encodeURIComponent(kind),{credentials:'same-origin',cache:'no-store',headers:{Accept:'application/json'}});
   if(!response.ok)throw new Error('unavailable');
   const result=await response.json();
   if(!result.ready||!result.token)throw new Error('unavailable');
   form.elements.csrf_token.value=result.token;
   ready=true; submit.disabled=false; submit.removeAttribute('aria-disabled');
  }
  form.addEventListener('submit',async event=>{
   event.preventDefault();
   if(!ready){report('Online submission is currently unavailable. Please email admin@thechorefacilitator.co.za.');return;}
   if(!form.reportValidity())return;
   submit.disabled=true; form.setAttribute('aria-busy','true'); report('Sending…');
   const controller=new AbortController();
   const timer=setTimeout(()=>controller.abort(),30000);
   try {
    const response=await fetch(endpoint,{method:'POST',credentials:'same-origin',body:new FormData(form),headers:{Accept:'application/json'},signal:controller.signal});
    const result=await response.json();
    if(!response.ok||!result.ok){
     if(response.status===403){await refreshToken();throw new Error('Your session expired. Your details are still here; please submit again.');}
     throw new Error(result.message||'We could not send your message. Your details are still here; please try again or email admin@thechorefacilitator.co.za.');
    }
    form.reset(); report('Your submission has been accepted for sending. Thank you. If you do not hear back, please email admin@thechorefacilitator.co.za.');
    ready=false;
    try{await refreshToken();}catch{report('Your submission was accepted for sending. Please refresh the page before sending another message.');}
   }catch(error){report(error.name==='AbortError'?'The request timed out and delivery could not be confirmed. Your details are still here. Please contact admin@thechorefacilitator.co.za before sending again.':error.message||'Sending failed. Your details are still here.');}
   finally{clearTimeout(timer);submit.disabled=!ready;form.removeAttribute('aria-busy');status.focus({preventScroll:true});}
  });
  try{await refreshToken(); const note=form.querySelector('[class$="submit"] p, .client-submit-area p'); if(note)note.textContent='Your information will be used to respond to your enquiry.';}
  catch{report('Online submission is currently unavailable. Please email admin@thechorefacilitator.co.za.');}
 });
})();
