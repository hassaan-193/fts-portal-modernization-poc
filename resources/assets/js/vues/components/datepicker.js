import flatPickr from 'vue-flatpickr-component';

const template = `
    <div>
        <flat-pickr @input="updateSelection" class="form-control"
        v-model="date" :name="fieldname" :config="config" ></flat-pickr>
    </div>
`;

const props = ['selected' ,'fieldname' , 'enabledate' ]

function data () {
    return {
        date: null,
        config: {
            dateFormat: 'Y-m-d',
            defaultDate: this.enableDate != "false" ? null : new Date()
        },
    }
};

const components = {'flat-pickr': flatPickr}

let watch = {
    selected(newValue){
        console.log(newValue)
        this.date = newValue
    }
}

let methods = {
    updateSelection() {
            let mySelected = this.date;
            this.$emit("update-" + this.fieldname, mySelected);
    }

}

export default {
    data  , template , components , methods, props, watch
};
