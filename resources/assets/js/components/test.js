class Test {
  constructor(elem){

    this.container = elem;
    // const options = this.container.querySelectorAll('input[type=checkbox]');
    // options.forEach(element => {
    //   console.log(elem)
    // });
    // console.log(list.items)
    // this.items = options
    // var questions = []
    // list.items.forEach((element , index ) => {
    //   questions [index] = element.elm.querySelectorAll('input[type=checkbox]')[0].name.split('[')[0]
    // });
    // console.log(questions)
    elem.addEventListener('click',function(e){
        event.target.classList.contains('textbox')
        if(e.target && e.target.matches('[prd-id]')){
              //do something
            //   console.log(e.target.prd-id)
            console.log('hello')
            console.log(e.target)
         }
     });
    // Handle toggle all event
    // const handler = elem.querySelector('[pro-id]');
    // handler.addEventListener('click', this.submitQuiz.bind(this));
  }
  submitQuiz(event) {
      console.log('sdsd')
}

};

export default Test;
