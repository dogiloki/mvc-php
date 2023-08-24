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
            if(element.tagName.startsWith("EVENT-")){
                let id_element=element.tagName.substring(6).toLowerCase();
                let element_event=document.getElementById(id_element);
                let collection_events=Array.from(element.getElementsByTagName('*'));
                collection_events.forEach((element_on)=>{
                    let event_name=(element_on.tagName).replace("ON-","").toLowerCase();
                    let id_on=element_on.getAttribute('on_id');
                    let emit=element_on.getAttribute('emit');
                    let on_attribute=element_on.getAttribute('on_attrib')??'value';
                    element_event.addEventListener(event_name,(evt)=>{
                        let element_on=document.getElementById(id_on);
                        this.params[emit]=element_on[on_attribute];
                        this.render();
                    });
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