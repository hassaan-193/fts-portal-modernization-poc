import vSelect from 'vue-select'

const template = `
    <div>
        <v-select
                    :label="label"
                    @input="updateSelection"
                    :reduce="option => option.id"
                    :options="options"
                    v-model="selectedOption" />
        <input type="hidden" :value="this.selectedOption" :name="field" >
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
    },

}
const components = {'v-select': vSelect}

const props = ['options' , 'selected' ,'field' ,'label' ,  'taggable' ,'index']

let methods = {
    updateSelection() {
        let mySelected = this.selectedOption;
        this.$emit("update-" + this.field, mySelected , this.index);
    },
}

export default {
    data ,props , template , components , watch , methods
};
