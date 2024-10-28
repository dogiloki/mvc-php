import XHR from '../XHR.js';

let form=document.getElementById('form-login');

form.addEventListener('submit',(evt)=>{
    evt.preventDefault();
    XHR.request({
        method:form.getAttribute('method'),
        url:form.getAttribute('action'),
        data:{
            _token:form['_token'].value,
            user:form['user'].value,
            password:form['password'].value
        },
        load:(data)=>{
            if(data.status==200){
                location.reload();
            }
        }
    });
});