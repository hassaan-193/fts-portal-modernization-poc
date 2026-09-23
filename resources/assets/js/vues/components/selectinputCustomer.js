import vSelect from 'vue-select'

const template = `
    <div>
        <v-select
                    :taggable="taggable"
                    @option:created="optionCreated"
                    :createOption="createOption"
                    :label="label"
                    @input="updateSelection"
                    :options="options"
                    v-model="selectedOption" />
        <input type="hidden"

        :value="(selectedOption && selectedOption.id !=0 ? selectedOption.id : '')
        ||
        (selectedOption && selectedOption.id ==0 ? selectedOption.name : '')
        ||
        (selectedOption === null ? '' : '' )"
        :name="field" >
        </div>
`;

function data () {
    return {
        selectedOption : null,
        postValue : null
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
    optionCreated(newValue) {
        this.$emit("create-" + this.field, newValue.name);
    },
    createOption(newOption){
        return  newOption = {
            id: 0,
            [this.label]: newOption
        }
    }
}

export default {
    data ,props , template , components , watch , methods
};
