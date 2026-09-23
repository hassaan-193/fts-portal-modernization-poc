import flatPickr from 'vue-flatpickr-component';

const template = `
    <div>
        <flat-pickr @input="updateSelection" class="form-control" v-model="date" name="date_time" :config="config" ></flat-pickr>
    </div>
`;

const props = ['selected' ]

function data () {
    return {
        date: null,
        config: {
            // altInput: true,
            enableTime: true,
            dateFormat: 'Y-m-d H:i:S',
            defaultDate: new Date(),
            defaultHour :12,
            time_24hr : true,
          },
    }
};

const components = {'flat-pickr': flatPickr}

let watch = {
    selected(newValue){
        this.date = newValue
    }
}

let methods = {
    updateSelection() {

            let mySelected = this.date;
            this.$emit("update-date_time", mySelected);
    }

}

export default {
    data  , template , components , methods, props, watch
};
