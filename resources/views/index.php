<!DOCTYPE html>
<html>
<head>
    <title>Inicio</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{urlPublic('css/normalize.css')}}">
</head>
<body>
    <h1>Hola mundo</h1>
    <p>Bienvenido</p>
    @php
        $user=\app\Models\User::all();
    @endphp
    <component:vista :variable="{{base64_encode(serialize($user))}}">
    </component:vista>
</body>
</html>

<script src="{{urlPublic('js/fetch.js')}}"></script>

<script>

document.addEventListener('DOMContentLoaded',()=>{

    // Obtener componentes
    let components=[];
    let colletion=Array.from(document.getElementsByTagName('*'));
    colletion.forEach((element)=>{
        if(element.tagName.startsWith("COMPONENT:")){
            components.push({
                "name":element.tagName.substring(10),
                "node":element,
                "vars":getVars(element),
                "wires":[],
            });
        }
    });
    function getVars(node){
        let element=node;
        let params={};
        let attributes=Array.from(element.attributes);
        attributes.forEach((attribute)=>{
            let name=attribute.name;
            if(name.startsWith(":")){
                params[name]=attribute.value;
            }
        });
        return params;
    }

    for(let component of components){
        render(component);
    }

    function render(component){
        component.wires.map((wire)=>{
            wire.value=wire.getValue();
            return wire;
        })
        let component_send={...component};
        component_send.node=null;
        component_send.wires=component.wires.filter((wire)=>{
            return wire.listener!="none" && wire.listener!=null;
        });
        Fetch.post("component/"+component.name,(data)=>{
            let json=JSON.parse(data);
            let html=json.html;
            let vars=json.vars;
            let doc=new DOMParser().parseFromString(html,"text/html");
            let collection_old=Array.from(component.node.getElementsByTagName('*'));
            let collection_new=Array.from(doc.body.getElementsByTagName('*'));
            if(collection_old.length==0){
                component.node.innerHTML=html;
                getWires(component,true);
            }else{
                // collection_old.forEach((element_old,index)=>{
                //     let element_new=collection_new[index];
                //     console.log(element_new,element_old);
                //     if(element_new==null){
                //         element_old.remove();
                //     }
                // });
                // getWires(component,false);
                component.node.innerHTML=html;
                getWires(component,true);
            }
            component.wires.map((wire)=>{
                let value=vars[wire.attrib];
                if(wire.getValue()!=value){
                    wire.setValue(value);
                }
                return wire;
            });
        },{
            "body":new URLSearchParams({json:JSON.stringify(component_send)})
        });
    }

    function getWires(component,addEvent=true){
        let collection=component.node.getElementsByTagName('*');
        for(let a=0; a<collection.length; a++){
            let element=collection[a];
            element.setAttribute("id",component.name+"-"+a);
            let attributes=Array.from(element.attributes);
            attributes.forEach((attribute)=>{
                if(attribute.name.startsWith("wire:")){
                    let wire=attribute.name.substring(5);
                    let listener=wire;
                    let attrib=attribute.value;
                    //let listener=wire.match(/^([^()]+)/)[1];
                    //let attrib=wire.match(/"([^"]+)"/)[1];
                    if(addEvent){
                        addEventWire(component,element,listener,attrib);
                    }
                }
            });
        }
    }

    function addEventWire(component,element,listener,attrib){
        let wire={
            "element":element,
            "listener":listener,
            "attrib":attrib,
            getValue:()=>{
                let value;
                switch(element.tagName){
                    case "INPUT":
                    case "TEXTAREA":
                    case "SELECT":
                        value=element.value;
                        break;
                    case "IMG":
                        value=element.src;
                        break;
                    case "A":
                        value=element.href;
                        break;
                    case "OBJECT":
                        value=element.data;
                        break;
                    default:
                        value=element.innerHTML;
                }
                return value;
            },
            setValue:(value)=>{
                switch(element.tagName){
                    case "INPUT":
                    case "TEXTAREA":
                    case "SELECT":
                        element.value=value;
                        break;
                    case "IMG":
                        element.src=value;
                        break;
                    case "A":
                        element.href=value;
                        break;
                    case "OBJECT":
                        element.data=value;
                        break;
                    default:
                        element.innerHTML=value;
                }
            }
        };
        //console.log(this.content.querySelector((element.tagName)+"[wire\\:"+listener+"=\""+attrib+"\"]"));
        if(listener!="none" && listener!=null){
            element.addEventListener(listener,()=>{
                component.vars[attrib]=wire.getValue();
                render(component);
            });
        }
        component.wires.push(wire);
    }

});

</script>