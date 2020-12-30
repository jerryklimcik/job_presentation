export default class SelectedRows {
    constructor() {
        this.rows = new Set(); 
    }
    
    addNewRows(newRows) {
        this.rows = newRows;
    }
    
    clear() {
        this.rows.clear();
    }
}