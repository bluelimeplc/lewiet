<script>
var selected_single_string_index = {};

function autocomplete_single_string_by_options(options){
  let input = null;
  let arr = [];
  let selected_index = 'selected';
  let callback_function = function(){};
  let callback_function_arguments = [];
 
  if(options.hasOwnProperty('input'))
    input = options.input;
  
  if(options.hasOwnProperty('services'))
    arr = options.services;

  if(options.hasOwnProperty('selected_index'))
    selected_index = options.selected_index;
  
  if(options.hasOwnProperty('callback_function'))
    callback_function = options.callback_function;
  
  if(options.hasOwnProperty('callback_function_arguments'))
    callback_function_arguments = options.callback_function_arguments;

  autocomplete_single_string(input, arr, selected_index, callback_function, callback_function_arguments);
}

function autocomplete_single_string(inp, arr, selected_index = 'selected', callback_function = function(){}, callback_function_arguments = []) {
  /*the autocomplete function takes two arguments,
  the text field element and an array of possible autocompleted values:*/
  
  var currentFocus;
  /*execute a function when someone writes in the text field:*/
  inp.addEventListener("input", function(e) {
    
      var a, b, i, val = this.value;
      /*close any already open lists of autocompleted values*/
      closeAllLists();
      if (!val) { return false;}
      currentFocus = -1;
      /*create a DIV element that will contain the items (values):*/
      a = document.createElement("DIV");
      a.setAttribute("id", this.id + "bt_autocomplete-list");
      a.setAttribute("class", "bt_autocomplete-items bt_autocomplete");
      /*append the DIV element as a child of the autocomplete container:*/
      this.parentNode.appendChild(a);
      /*for each item in the array...*/
      $.each(arr, function(i,row){
        //console.log(i);
      //for (i = 0; i < arr.length; i++) {
        /*check if the item starts with the same letters as the text field value:*/
        /*+++++++++++++++++++++++++++++++++++++++++++*/
        if(arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {

        //if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
          /*create a DIV element for each matching element:*/
          b = document.createElement("DIV");
          let inner_html = '';
          
          /*make the matching letters bold:*/
          inner_html += "<span class=\"selected\">" + arr[i].substr(0, val.length) + "</span>";
          inner_html += arr[i].substr(val.length);
          inner_html += '<br>';
          /*insert a input field that will hold the current array item's value:*/
          inner_html += '<input type="hidden" value="' + i + '">';
          inner_html += '<input type="hidden" value="' + arr[i] + '">';
          b.innerHTML = inner_html;
          /*execute a function when someone clicks on the item value (DIV element):*/
          b.addEventListener("click", function(e) {
              /*insert the value for the autocomplete text field:*/
              inp.value = this.getElementsByTagName("input")[1].value;
              //selected_item_array_id = this.getElementsByTagName("input")[0].value;
              selected_single_string_index[selected_index] = this.getElementsByTagName("input")[0].value;
              /*close the list of autocompleted values,
              (or any other open lists of autocompleted values:*/
              closeAllLists();
              if(callback_function_arguments.length > 0)
                callback_function(callback_function_arguments);
              else
                callback_function();
          });
          a.appendChild(b);
        }
    });
  });
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keydown", function(e) {
      var x = document.getElementById(this.id + "bt_autocomplete-list");
      if (x) x = x.getElementsByTagName("div");
      if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the "active" item:*/
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActive(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add("bt_autocomplete-active");
  }
  function removeActive(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("bt_autocomplete-active");
    }
  }
  function closeAllLists(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName("bt_autocomplete-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
  /*execute a function when someone clicks in the document:*/
  document.addEventListener("click", function (e) {
      closeAllLists(e.target);
  });
}

</script>