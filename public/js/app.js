import Util from './Util.js';
import XHR from './XHR.js';

let collapse_containers=document.querySelectorAll(".collapse-container");
let loader_containers=document.querySelectorAll(".loader-container");
window.global_loader=document.querySelector("#global-loader");

document.addEventListener('DOMContentLoaded',(evt)=>{
	collapse_containers.forEach((collapse_container)=>{
		let checkbox=collapse_container.querySelector('input[type=checkbox]');
		let collapse=collapse_container.querySelector('.collapse');
		collapse.style.maxHeight=checkbox.checked?(collapse.getAttribute('height')??"100vh"):"0px";
		checkbox.addEventListener('change',()=>{
			collapse.style.maxHeight=checkbox.checked?(collapse.getAttribute('height')??"100vh"):"0px";
		});
	});
	loader_containers.forEach((loader_container)=>{
		Util.addClassPresent(loader_container.parentNode,'parent-loader');
	});
});

customElements.define('form-loader',class FormLoader extends HTMLFormElement{

	constructor(){
		super();
		this.addEventListener('submit',(evt)=>{
			Util.loader(window.global_loader,true);
		});
	}

},{
	extends:'form'
});

customElements.define('data-table',class DataTable extends HTMLElement{

	static text_asc="▲";
	static text_desc="▼";

	constructor(){
		super();
		this.addEventListener('click',(evt)=>{
			if(evt.target.tagName==="TH"){
				alert("dsa");
			}
		});
		this.container_table=this.getElementsByTagName("table")[0];
		this.model=this.container_table.getAttribute("model-table");
		this.headers=this.container_table.querySelectorAll("th");
		this.columns=[];
		this.columns_method=[];
		this.initColumns();
		this.render();
	}

	initColumns(){
		for(let header of this.headers){
			let column=header.getAttribute("data-column");
			if(column==null){
				continue;
			}
			if(column.includes("(")){
				this.columns_method.push(column.replaceAll("()",""));
			}else{
				this.columns.push(column);
			}
		}
	}

	createRow(items){

	}

	render(){
		XHR.request({
            method:"POST",
            uri:"component-data-table",
			data:{
				name:this.model,
				select_columns:JSON.stringify(this.columns),
				with_methods:JSON.stringify(this.columns_method)
			},
            action:(xhr)=>{
                xhr.responseType="json";
            },
            load:(xhr)=>{
                let paginate=xhr.response;
				let body=this.container_table.getElementsByTagName("tbody")[0]??Util.createElement("tbody");
				body.innerHTML="";
                for(let data of paginate.data){
					let tr=Util.createElement("tr");
					for(let column of this.columns){
						let td=Util.createElement("td",(td)=>{
							td.innerHTML=data[column];
						});
						tr.appendChild(td);
					}
					for(let method of this.columns_method){
						let td=Util.createElement("td",(td)=>{
							td.innerHTML=data[method];
						});
						tr.appendChild(td);
					}
					body.appendChild(tr);
				}
				this.container_table.appendChild(body);
            }
        });
	}

});