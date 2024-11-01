import Util from './Util.js';

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