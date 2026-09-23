// import selectinput from "./components/selectinput"

const template = `
<div>
<div class="row">
    <div class="col-md-3 form-group mb-3">
        <a @click="addProduct" class="btn btn-secondary">Add</a>
    </div>
</div>
<div class="row" v-for="(item, index) in rentout_products">
<div class="col-md-2 form-group mb-2">
    <label>Product Name:</label>

</div>


<div class="form-group col-sm-1">
    <br>
    <button type="button" class="btn btn-sm btn-danger mt-2" @click="deleteProduct(index)">Remove</button>
</div>

</div>
</div>
`;

// function mounted() {
//     this.elem = this.$el;
//     this.elem.addEventListener('click',this.clickList.bind(this));
// }

// const components = {selectinput};

// const data = {
//     product: {},
//     productId : null,
//     elem : null,
//     file : null,
// };

// let methods = {
//     setUpdateUrl(){
//     }

// }
//   export default {
//     mounted , data , methods , components
// };
