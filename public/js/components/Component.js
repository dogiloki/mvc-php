import Util from '../Util.js';
import XHR from '../XHR.js';
import Wire from './Wire.js';

export default class Component{

    constructor({
        model=null,
        elements=[]
    }){
        this.model=model;
        this.elements=elements;
    }

}