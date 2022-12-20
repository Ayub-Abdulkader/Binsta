let buttons = document.querySelectorAll('.clipIcon');
let cpCode = document.querySelectorAll('.code');


// clip board
function copyClip(textId) {

    textId.select();
    textId.setSelectionRange(0, 99999);
    
    navigator.clipboard.writeText(textId.value);
    alert("copied 😉")
    
}

for (let i = 0; i < buttons.length; i++) {
    buttons[i].addEventListener("click", function(e) {
        e.preventDefault();
        copyClip(cpCode[i])
    });
}

// clip board end //

// filter on search

function filterOnSearch() {
    
    let input, filter, filterDiv, card, a, i;

    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    filterDiv = document.getElementById("filterDiv");
    card = filterDiv.getElementById("content");

  // Loop through all filterDivst items, and hide those who don't match the search query
  for (i = 0; i < filterDiv.length; i++) {
    a = filterDiv[i].getElementById("parag")[0];
    console.log(a);
    txtValue = a.textContent || a.innerText;
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
      filterDiv[i].style.display = "";
    } else {
      filterDiv[i].style.display = "none";
    }
  }
}
let input = document.getElementById("searchInput");

input.addEventListener("keyup", function(e) {
    //e.preventDefault();
    filterOnSearch()
});