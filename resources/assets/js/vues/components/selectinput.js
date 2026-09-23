import vSelect from 'vue-select'

const template = `
    <div>
        <v-select taggable @input="updateSelection" :options="options" v-model="selectedOption" />
        <input type="hidden" :value="selectedOption" :name="field" >
        </div>
`;

function data () {
    return {
        selectedOption : this.selected,
    }
};

let watch = {
    selected(newValue){
        this.selectedOption = newValue
    }
}
const components = {'v-select': vSelect}

const props = ['options' , 'selected' ,'field' ,'label' ]

let methods = {
    updateSelection() {
            let mySelected = this.selectedOption;
            console.log(mySelected)

            this.$emit("update-" + this.field, mySelected);
    }
}

export default {
    data ,props , template , components , methods , watch
};
