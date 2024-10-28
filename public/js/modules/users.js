import Util from '../Util.js';
import XHR from '../XHR.js';

loadUsers();

function loadUsers(page=1,per_page=10){
    XHR.request({
        method:'GET',
        url:Util.getMeta('api-users-show'),
        action:(xhr)=>{
            xhr.responseType="json";
        },
        query:{
            page:page,
            per_page:per_page
        },
        load:(data)=>{
            let pagination=data.response.data;
            let users=pagination.data;
            let container_pages=document.getElementById('container-pages');
            let table_users=document.getElementById('table-users').getElementsByTagName('tbody')[0];
            try{
                container_pages.innerHTML="";
                table_users.innerHTML="";
            }catch{

            }
            for(let link of pagination.links){
                let li=Util.createElement("li",(element)=>{
                    if(link.active){
                        element.classList.add("selected");
                    }
                    element.setAttribute("value",link.label);
                    element.innerHTML=link.label;
                    element.addEventListener('click',(evt)=>{
                        loadUsers(element.getAttribute("value"));
                    });
                });
                container_pages.appendChild(li);
            }
            document.getElementById('current-page').textContent=pagination.current_page;
            document.getElementById('total-pages').textContent=pagination.to;
            document.getElementById('total-results').textContent=pagination.total;
            for(let user of users){
                table_users.appendChild(Util.createElement("tr",(tr)=>{
                    tr.appendChild(Util.createElement("td",(td)=>{
                        td.textContent="";
                    }));
                    tr.appendChild(Util.createElement("td",(td)=>{
                        td.textContent="";
                    }));
                    tr.appendChild(Util.createElement("td",(td)=>{
                        td.textContent=user.name;
                    }));
                    tr.appendChild(Util.createElement("td",(td)=>{
                        td.textContent=user.surname1+" "+user.surname2;
                    }));
                    tr.appendChild(Util.createElement("td",(td)=>{
                        td.textContent=user.registration;
                    }));
                    tr.appendChild(Util.createElement("td",(td)=>{
                        td.textContent="";
                    }));
                }));
            }
        }
    });
}