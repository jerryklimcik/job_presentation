export default class Search {
    constructor() {
        this.filter = {};
    }
    
    find(search) {
        this.filter = JSON.stringify(search);
    }
    
    clear() {
        this.filter = {};
    }
}