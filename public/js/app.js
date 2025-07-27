import Fetch from './Fetch.js';
import Util from './Util.js';
import XHR from './XHR.js';
import IgnoredInputs from './domain/IgnoredInputs.js';

const query_element_inputs='.checkbox-field,.input-field,[is="data-select"]';
const collapse_containers=document.querySelectorAll(".collapse-container");
const loader_containers=document.querySelectorAll(".loader-container");
const modal_overlay=document.getElementsByClassName("modal-overlay");
const tabs_containers=document.querySelectorAll(".tabs-container");
window.global_loader=document.querySelector("#global-loader");

document.addEventListener('DOMContentLoaded',(evt)=>{
	// Agregar evento de cerrar modal al bóton de cerrar (svg)
	Array.from(modal_overlay).forEach((overlay)=>{
		const event_close=new CustomEvent('close',{
			element:overlay,
		});
		overlay.getElementsByClassName('close-btn')[0].getElementsByTagName('svg')[0].addEventListener('click',(evt)=>{
			Util.modal(overlay,false);
			overlay.dispatchEvent(new Event('close'));
		});
	});

	// Contenedores de colapso
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
	// Tabs
	tabs_containers.forEach((tabs_container)=>{
		const tab_titles=tabs_container.getElementsByClassName('tab-title');
		const tab_items=tabs_container.getElementsByClassName('tab-item');
		const items=[];
		let tab_title_selected=null;
		let index=0;
		for(const tab_title of tab_titles){
			if(tab_title_selected==null && tab_title.hasAttribute("selected")){
				tab_title_selected=tab_title;
			}
			const tab_item=tab_items[index];
			const inputs=tab_item.querySelectorAll(query_element_inputs);
			const item={
				inputs:inputs,
				tab_title:tab_title,
				tab_item:tab_item,
				validityInputs:(input=null)=>{
					let has_count_invalid_inputs=tab_title.hasAttribute("invalid-inputs") && input!=null;
					let count_invalid_inputs=0;
					if(has_count_invalid_inputs){
						count_invalid_inputs=tab_title.getAttribute("invalid-inputs");
						if(input.checkValidity()){
							count_invalid_inputs--;
						}else{
							count_invalid_inputs++;
						}
					}else{
						for(const input of inputs){
							if(!input.checkValidity()){
								count_invalid_inputs++;
							}
						}
					}
					tab_title.setAttribute("invalid-inputs",count_invalid_inputs);
					return count_invalid_inputs;
				}
			};
			items.push(item);
			tab_title.addEventListener('click',(evt)=>{
				items.forEach((item)=>{
					item.tab_title.removeAttribute("selected");
					Util.modal(item.tab_item,false);
					item.validityInputs();
				});
				tab_title.setAttribute("selected",true);
				Util.modal(tab_item,true);
			});
			index++;
			for(const input of inputs){
				input.addEventListener('change',(evt)=>{
					//item.validityInputs(input);
					item.validityInputs();
				});
			}
			item.validityInputs();
		}
		if(tab_title_selected!=null){
			tab_title_selected.click();
		}
	});
});

// Formulario dinámico
customElements.define('form-dynamic',class FormDynamic extends HTMLFormElement{

	onReset=()=>{};

	constructor(){
		super();
		this.disable_submit=this.hasAttribute('disable-submit');
		this.ignored_inputs=[];
		this.element_inputs=[];
		this.message=this.querySelector('.message')??document.getElementById(this.getAttribute('message-target')??null)??null;
		for(const element_checkbox of this.querySelectorAll('[type="checkbox"]')){
			element_checkbox.check=(op=null)=>{
				if(op==null){
					element_checkbox.click();
				}else
				if(op!=element_checkbox.checked){
					element_checkbox.click();
				}
				element_checkbox.dispatchEvent(new Event('change'));
			};
			element_checkbox.checkEvent=()=>{
				element_checkbox.dispatchEvent(new Event('change'));
			}
			element_checkbox.reset=()=>{
				
			};
		}
		for(const element of this.querySelectorAll('[has-ignore]')){
			this.ignored_inputs.push(new IgnoredInputs(element,query_element_inputs));
		}
		this.addEventListener('submit',(evt)=>{
			evt.preventDefault();
			if(this.disable_submit){
				evt.preventDefault();
			}
		});
	}

	loadInputs(tag_name="select-tab"){
		this.element_inputs=[];
		for(const element of this.querySelectorAll(tag_name)){
			this.element_inputs.push(element);
			this[element.getAttribute("name")]=element;
		}
	}

	checkValidity(){
		let done=true;
		for(const element of this.element_inputs){
			if(!element.checkValidity()){
				done=false;
				break;
			}
		}
		if(done){
			return super.checkValidity();
		}
	}

	input(name){
		const element=this.querySelector('[name="'+name+'"]');
		if(element!=null && !(name in this)){
			this[name]=element;
		}
		return element;
	}

	reset(){
		super.reset();
		this.element_inputs.forEach((element)=>{
			element.reset();
		});
		for(const element_checkbox of this.querySelectorAll('[type="checkbox"]')){
			element_checkbox.checkEvent();
		}
		this.resetMessage();
		this.onReset();
	}

	resetMessage(){
		if(this.message!=null){
			Util.message(this.message,null);
		}
	}

	getValue(name){
		const element=this.querySelector('[name="'+name+'"]');
		if(element!=null && (name in this)){
			return element.value;
		}
		return null;
	}

},{
	extends: 'form'
});

// Fomulario con carga
customElements.define('form-loader',class FormLoader extends HTMLFormElement{

	constructor(){
		super();
		this.addEventListener('submit',(evt)=>{
			Util.loader(window.global_loader,true);
		});

		this.addEventListener('keydown',(event)=>{
            if (event.key==='Enter') {
                // Si se presiona Enter, preventDefault impide el envío del formulario
                event.preventDefault();
            }
        });
	}

},{
	extends:'form'
});

// Fomulario para evitar que se ejecute button al pulsar enter sobre input
customElements.define('form-not-submit-enter',class FormLoader extends HTMLFormElement{

	constructor(){
		super();
		this.addEventListener('keydown', (event) => {
            if (event.key==='Enter') {
                // Si se presiona Enter, preventDefault impide el envío del formulario
                event.preventDefault();
            }
        });
	}

},{
	extends:'form'
});

// Fomulario para input con data-select
customElements.define('form-data-select',class FormLoader extends HTMLFormElement{

	constructor(){
		super();
	}

	reset(){
		super.reset();
		const elements=this.data_select=this.querySelectorAll('[is="data-select"]');
		elements.forEach((element)=>{
			element.reset();
		})
	}

	input(name){
		return this.data_select=this.querySelector('[is="data-select"][name="'+name+'"]');
	}

	selection(name){
		const element=this.data_select=this.querySelector('[is="data-select"][name="'+name+'"]');
		return element==null?null:element.selection();
	}

},{
	extends:'form'
});

// Elemento personalizado para selección en vertical
customElements.define('data-select',class DataInputMultiple extends HTMLElement{

	onSelect=(key,value)=>{};
	onRemove=(key,value)=>{};

	constructor(){
		super();
		this.multiple=JSON.parse(this.hasAttribute("multiple") && this.getAttribute("multiple")!="false");
		this.required=this.hasAttribute("required");
		this.paginate=JSON.parse(this.getAttribute("paginate")??"true");
		this.selected_content=Util.createElement("div",(element)=>{
			element.classList.add("selected-content");
		});
		this.selector=this.getElementsByClassName("selector")[0];
		this.insertBefore(this.selected_content,this.selector);
		this.action=this.getAttribute("action");
		this.method=this.getAttribute("method");
		this.search=this.getElementsByTagName("input")[0];
		if(this.search!=null){
			this.search.addEventListener('keyup',(evt)=>{
				// Verificar si el campo es válido
				if(this.search.validity.valid){
					this.render();
				}
			});
		}
		this.selected={};
		this.render();
	}

	// Verificar si es valido
	checkValidity(){
		if(this.required){
			return this.selection()!=null;
		}
		return true;
	}

	// Retornar selección en forma de id o array de ids
	selection(){
		const array=this.getSelectedArray();
		return this.multiple?array:array[0];
	}

	// Resetear selección
	reset(){
		this.selected_content.innerHTML="";
		this.selected={};
		this.selector.querySelectorAll("selector-option").forEach((option)=>{
			option.setAttribute("selected",false);
		});
		this.dispatchEvent(new Event("change"));
	}

	// Retornar selección en forma de array de ids
	getSelectedArray(){
		return Object.keys(this.selected);
	}

	addselected(key,value){
		if(this.multiple){
			this.selected[key]=value;
		}else{
			for(const index in this.selected){
				this.selected[index].getOption().setAttribute("selected",false);
			}
			this.selected={};
			this.selected[key]=value;
		}
	}

	loadItems(items){
		this.selector.innerHTML="";
		items.forEach((item)=>{
			this.selector.appendChild(Util.createElement("selector-option",(option)=>{
				option.value=item.id;
				option.innerHTML=item.show;
				option.setAttribute("selected",this.getSelectedArray().includes(item.id.toString()));
				option.addEventListener('click',(evt)=>{
					item.getOption=()=>{
						return option;
					};
					if(this.selected[item.id]){
						delete this.selected[item.id];
						option.setAttribute("selected",false);
					}else{
						this.addselected(item.id,item);
						option.setAttribute("selected",true);
						this.onSelect(item.id,item);
						this.dispatchEvent(new Event("change"));
					}
					if(this.multiple){
						this.updateSelected();
					}
				});
			}));
		});
		this.updateSelected();
	}

	updateSelected(){
		this.selected_content.innerHTML="";
		for(const index in this.selected){
			const item=this.selected[index];
			item.getOption().setAttribute("selected",true);
			this.selected_content.appendChild(Util.createElement("div",(div)=>{
				div.innerHTML=item.show;
				div.classList.add("selected-item");
				div.appendChild(Util.createElement("div",(button)=>{
					button.innerHTML="X";
					button.classList.add("btn-remove");
					button.addEventListener('click',(evt)=>{
						delete this.selected[index];
						item.getOption().setAttribute("selected",false);
						this.selected_content.removeChild(div);
						this.onRemove(item.id,item);
						this.dispatchEvent(new Event("change"));
					});
				}));
			}));
		}
	}

	render(){
		XHR.request({
			method:this.method,
			url:this.action,
			query:{
				"search":this.search.value
			},
			action:(xhr)=>{
				xhr.responseType="json";
			},
			load:(xhr)=>{
				const json=xhr.response;
				if(json.data==null){
					this.loadItems([]);
					return;
				}
				this.loadItems(this.paginate?json.data.data:json.data);
			}
		});
	}

});

// Etiqueta personalizado para opción en tabs
customElements.define('option-tab',class extends HTMLElement{

	constructor(){
		super();
	}

	set value(op){
		return this.setAttribute("value",op);
	}

	get value(){
		return this.getAttribute("value");
	}

});

// Etiqueta personalizado para selección en tabs
customElements.define('select-tab',class extends HTMLElement{

	constructor(){
		super();
		this.onSelect=(key,value)=>{};
		this.onDeselect=(key,value)=>{};
		this.selected_options={};
		this.multiple=this.hasAttribute("multiple");
		this.required=this.hasAttribute("required");
		this.action=this.getAttribute("action");
		this.method=this.getAttribute("method")??"GET";
		this.options=this.querySelectorAll("option-tab");
		if(this.action==null){
			this.loadEvents();
		}else{
			this.render();
		}
	}

	set name(op){
		return this.setAttribute("name",op);
	}

	get name(){
		return this.getAttribute("name");
	}

	set value(op){
		this.select([...this.options].find(option=>option.value==op));
	}

	get value(){
		const array=this.selection();
		if(array.length<=0){
			return null;
		}
		return this.multiple?array:array[0];
	}

	checkValidity(){
		if(this.required){
			return this.value!=null;
		}
		return true;
	}

	isSelect(option){
		return option.hasAttribute("selected") && this.selected_options.hasOwnProperty(option.value);
	}

	select(option){
		if(!this.multiple){
			this.reset();
			//Object.keys(selected_options).forEach(key=>delete selected_options[key]);
		}
		this.selected_options[option.value]=option;
		option.setAttribute("selected",true);
		this.dispatchEvent(new Event("change"));
		this.onSelect(option.value,option);
	}

	reset(){
		for(const option of this.options){
			if(this.isSelect(option)){
				this.deselect(option);
			}
		}
		this.selected_options={};
		this.dispatchEvent(new Event("change"));
	}

	deselect(option){
		delete this.selected_options[option.value];
		option.removeAttribute("selected");
		this.dispatchEvent(new Event("change"));
		this.onDeselect(option.value,option);
	}

	selection(){
		return Object.keys(this.selected_options);
	}

	loadEvents(){
		for(const option of this.options){
			option.addEventListener("click",(evt)=>{
				if(this.isSelect(option)){
					this.deselect(option);
				}else{
					this.select(option);
				}
			});
			if(option.hasAttribute("selected")){
				option.click();
			}
		}
	}

	loadOptions(options){
		this.innerHTML="";
		this.options=[];
		for(const option of options){
			const element=Util.createElement("option-tab",(element)=>{
				element.value=option.id;
				element.textContent=option.show;
				return element;
			});
			this.appendChild(element);
			this.options.push(element);
		}
		this.loadEvents();
	}

	render(){
		XHR.request({
			method:this.method,
			url:this.action,
			action:(xhr)=>{
				xhr.responseType="json";
			},
			load:(xhr)=>{
				const json=xhr.response;
				if(json.data==null){
					this.loadOptions([]);
					return;
				}
				this.loadOptions(json.data);
			}
		});
	}

});

// Select personalizado para llenar option en base a peticion http
customElements.define('select-fillable',class SelectFillable extends HTMLSelectElement{

	constructor(){
		super();
		this.action=this.getAttribute("action");
		this.method=this.getAttribute("method")??"GET";
		this.render();
	}

	loadOptions(options){
		this.innerHTML="";
		for(const option of options){
			const element=Util.createElement("option",(element)=>{
				element.value=option.id;
				element.textContent=option.show;
				return element;
			});
			this.appendChild(element);
		}
	}

	render(){
		XHR.request({
			method:this.method,
			url:this.action,
			action:(xhr)=>{
				xhr.responseType="json";
			},
			load:(xhr)=>{
				const json=xhr.response;
				if(json.data==null){
					this.loadOptions([]);
					return;
				}
				this.loadOptions(json.data);
			}
		});
	}

},{
	extends: 'select'
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
		// Attributos para seleccionar columnas a mostrar en la tabla	
		this.list_column={};
		// Attributos para getionar parámetros de la paginación
		this.current_page=1;
		this.initColumns();
		this.render();
	}

	initColumns(){
		this.list_column.data=[];
		this.list_column.columns=function(){
			return this.data.filter((column)=>{
				return column.key=="column";
			});
		}
		this.list_column.methods=function(){
			return this.data.filter((column)=>{
				return column.key=="method";
			});
		}
		this.list_column.events=function(){
			return this.data.filter((column)=>{
				return column.key=="event";
			});
		}
		for(let header of this.headers){
			let attributes=header.getAttributeNames();
			if(attributes.length<=0){
				this.list_column.data.push({
					obj:header,
					key:null,
					value:header.innerHTML
				});
			}
			for(let attribute of attributes){
				if(!attribute.startsWith("data-")){
					continue;
				}
				let column=attribute.replace("data-","");
				this.list_column.data.push({
					obj:header,
					key:column,
					value:header.getAttribute(attribute)
				});
			}
		}
	}

	loadPagination(paginate){
		let pagination_container=this.getElementsByClassName("pagination-container")[0]??null;
		if(pagination_container==null){
			this.appendChild(Util.createElement("div",(element)=>{
				element.classList.add("pagination-container");
			}));
		}
		pagination_container.innerHTML="";
		let pagination_buttons=Util.createElement("div",(buttons)=>{
			buttons.classList.add("pagination-buttons");
			for(let link of paginate.links){
				buttons.appendChild(Util.createElement("button",(button)=>{
					button.classList.add("button");
					if(link.active){
						button.classList.add("button-active");
					}
					button.value=link.value;
					button.innerHTML=link.label;
					button.addEventListener('click',()=>{
						if(button.value==-1){
							this.current_page--;
						}else
						if(button.value==0){
							this.current_page++;
						}else{
							this.current_page=button.value;
						}
						if(Util.withinRange(this.current_page,1,paginate.total_pages)){
							this.render();
						}else{
							this.current_page=this.current_page<1?1:paginate.total_pages;
						}
					});
				}));
				buttons.appendChild(document.createTextNode(" "));
			}
		});
		let pagination_info=Util.createElement("div",(info)=>{
			info.classList.add("pagination-info");
			info.innerHTML=paginate.info;
		});
		pagination_container.appendChild(pagination_buttons);
		pagination_container.appendChild(pagination_info);
	}

	loadRows(paginate){
		let body=this.container_table.getElementsByTagName("tbody")[0]??Util.createElement("tbody");
		body.innerHTML="";
        for(let paginate_data of paginate.data){
			let tr=Util.createElement("tr");
			for(let data of this.list_column.data){
				switch(data.key){
					case "column":{
						tr.appendChild(Util.createElement("td",(td)=>{
							td.innerHTML=paginate_data[data.value];
						}));
						break;
					}
					case "event":{
						tr.appendChild(Util.createElement("td",(td)=>{
							td.appendChild(Util.createElement("button",(button)=>{
									button.innerHTML=data.value;
									button.addEventListener("click",(evt)=>{
										Fetch.delete(Util.getMeta("route-nagios-host-delete")+"/"+paginate_data.id,(res)=>{
											td.innerHTML=JSON.parse(res).message;
										});
									});
							}));
						}));
						break;
					}
					default:{
						tr.appendChild(Util.createElement("td",(td)=>{
							td.innerHTML="";
						}));
					}
				}
			}
			for(let cell of tr.children){
				let text=cell.getAttribute("data-text");
				const regex=/{(.*?)}/g;
				let match;	
				while((match=regex.exec(text))!==null){
					cell.innerHTML=text.replaceAll(match[0],data[match[1]]);
				}
			}
			body.appendChild(tr);
		}
		this.container_table.appendChild(body);
	}

	render(){
		XHR.request({
            method:"POST",
            uri:"component-data-table",
			data:{
				"name":this.model,
				"columns":JSON.stringify({
					"select":Array.from(this.list_column.columns()).map((column)=>{
						return column.value;
					}),
					"methods":Array.from(this.list_column.methods()).map((method)=>{
						return method.value;
					}),
				}),
				"paginate":JSON.stringify({
					"current_page":this.current_page
				})
			},
            action:(xhr)=>{
                xhr.responseType="json";
            },
            load:(xhr)=>{
                let paginate=xhr.response;
				this.loadPagination(paginate);
				this.loadRows(paginate);
            }
        });
	}

});