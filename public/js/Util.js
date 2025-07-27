export default class Util{

    static ERROR={
		code:-1,
		class:'message-error'
	};
	static INFO={
		code:0,
		class:'message-info'
	};
	static SUCCESS={
		code:1,
		class:'message-success'
	};
	static WARNING={
		code:2,
		class:'message-warning'
	};

    static getMeta(key){
        let element=document.querySelector('meta[name="'+key+'"]');
        if(element==null){
            return null;
        }
        return element.getAttribute('content')??null;
    }

	static modal(content,visible=-1){
		content.style.display=(visible==-1)?
							((content.style.display=="none")?"":"none"):
							(visible)?"":"none";
		if(visible==-1){
			content.classList.toggle("hidden");
		}else{
			if(visible){
				content.classList.remove("hidden");
			}else{
				content.classList.add("hidden");
			}
		}
	}

	static modalById(text,visible){
		Util.modal(document.getElementById(text),visible);
	}

	static formatMoney(text,add_decimal=false){
		text=text.toString();
		if(text.endsWith(".") && !add_decimal){
			add_decimal=true;
		}
		let amount=Number(text.replace(/[^0-9.]/g,""));
		if(isNaN(amount)){
			amount=0;
		}
		return "$ "+(amount??0).toLocaleString('es-MX')+(add_decimal?".":"");
	}

	static formatTime(text,quantity=3,str=true){
		let array=text.split(":");
		for(let index=0; index<quantity; index++){
			array[index]??="00";
		}
		return array.slice(0,quantity).join(":")+(str?"hrs":"");
	}

	static formatSize(value){
		let units=['B','KB','MB','GB','TB','PT','EB','ZB','YB'];
		let index=0;
		while(value>1024){
			value/=1024;
			index++;
		}
		return Number(value).toFixed(2)+" "+units[index];
	}

	static changeNumberSign(num){
		return (num>0)?-num:num;
	}

	static convertText(texto){
		return texto.codePointAt(0)-64;
	}

	static convertNum(num){
		return String.fromCodePoint(num+64);
	}

	static changeNum(actual,min,max,direccion=Util.IZQ){
		if(direccion==Util.IZQ){
			if(actual<max){
				actual++;
			}else{
				actual=min;
			}
		}else
		if(direccion==Util.DER){
			if(actual>min){
				actual--;
			}else{
				actual=max;
			}
		}
		return actual;
	}

	static numRandom(max,min=0){
		return Math.round(Math.random()*(max-min)+min);
	}

	static withinRange(value,min,max){
		return value>=min && value<=max;
	}

	static where(conditional,action){
        if(conditional){
            action();
        }
    }

	static messageById(id,message,type){
		Util.message(document.getElementById(id),message,type);
	}

	static message(content,message=null,type=null){
		if(content===null){
			return;
		}
		content.innerHTML=message;
		content.setAttribute('class','');
		if(message===null){
			Util.modal(content,false);
		}else{
			type=(typeof type==='string')?type:type.class;
			content.setAttribute('class',"message message-"+type);
			Util.modal(content,true);
		}
	}

	static loader(element=null,active=true){
        if(element===null){
            return;
        }
        Util.modal(element,active);
		let loader_element=element.querySelector(".loader");
        if(active){
            Util.addClassPresent(loader_element,"active");
        }else{
            loader_element.classList.remove("active");
        }
    }

	static createElement(type,value=null){
        let content=document.createElement(type);
        if(value==null){
            return content;
        }
        if(Array.isArray(value)){
            value.forEach((item)=>{
                content.appendChild(item);
            });
        }else
        if(typeof value === 'function'){
            value(content);
            return content;
        }else{
            content.innerHTML=value;
        }
        return content;
    }

    static iterate(count,action,params=[]){
    	let elements=[];
    	for(let index=0; index<count; index++){
    		elements.push(action(index,...params));
    	}
    	return elements;
    }

    static arrayRemove(array,item_remove){
    	if(Array.isArray(array)){
    		return array.filter(item=>item!==item_remove);
    	}else
    	if(typeof array === 'object'){
    		let array_filter={};
    		for(let key in array){
    			if(array.hasOwnProperty(key) && array[key]!==item_remove){
    				array_filter[key]=array[key];
    			}
    		}
    		array=array_filter;
    		return array;
    	}
    	return array;
    }

	static addClassPresent(item,value){
		if(!item.classList.contains(value)){
			item.classList.add(value);
		}
	}

	static formDataToArray(form_data){
		var data={};
		form_data.forEach((value,key)=>{
			data[key]=value;
		});
		return data;
	}

	static sync(element1,element2,{
		event='keyup',
		mutual=false,
		content1={
			event:null
		},
		content2={
			event:null
		}
	}={}){
		element1.addEventListener(content1.event??event,(evt)=>{
			element2.value=element1.value;
		});
		if(mutual){
			element2.addEventListener(content2.event??event,(evt)=>{
				element1.value=element2.value;
			});
		}
	}

}