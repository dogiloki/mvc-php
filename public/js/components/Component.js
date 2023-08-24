class Component{

    constructor(name,element){
        this.name=name;
        this.element=element;
        this.params=this.getParams();
        this.loadEvents();
        this.render();
    }

    getParams(){
        let params={};
        let collection=Array.from(this.element.attributes);
        collection.forEach((attribute)=>{
            params[attribute.name]=attribute.value;
        });
        return params;
    }

    loadEvents(){
        let events={};
        let collection=Array.from(this.element.getElementsByTagName('*'));
        collection.forEach((element)=>{
            if(element.tagName.startsWith("ON:")){
                let event=(element.tagName.substring(3)).toLowerCase();
                let collection_events=Array.from(element.getElementsByTagName("*"));
                collection_events.forEach((element_event)=>{
                    if(element_event.tagName.startsWith("ID:")){
                        let id=(element_event.tagName.substring(3)).toLowerCase();
                        let element_apply_event=document.getElementById(id);
                        element_apply_event.addEventListener(event,(evt)=>{
                            let attributes=Array.from(element_event.attributes);
                            attributes.forEach((attribute)=>{
                                let object=attribute.value.split(':');
                                this.params[attribute.name]=document.getElementById(object[0])[object[1]??'value'];
                                this.render();
                            }); 
                        });
                    }
                });
            }
        });
        return events;
    }

    render(){
        Fetch.post('component/'+this.name,(data)=>{
            this.element.innerHTML=data;
        },{
            'Content-Type':'application/json',
            'Accept':'text/html',
            'body':new URLSearchParams(this.params)
        });
    }

}