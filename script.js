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
let searchInput = document.getElementById("searchInput");
function filterOnSearch() {

    let input = document.getElementById("searchInput").value;
    let cards = document.querySelectorAll('div.cards');
    // Loop through all filterDivst items, and hide those who don't match the search query
    for (i = 0; i < cards.length; i++) {
      
      if (cards[i].textContent.toLowerCase().includes(input.toLowerCase())) {
      cards[i].classList.remove('hidden');
    } else {
      cards[i].classList.add("hidden");
    }
  }
}
let typingTimer;               
let typeInterval = 200;
let input = document.getElementById("searchInput");

searchInput.addEventListener("keyup", function(e) {
    e.preventDefault();
    clearTimeout(typingTimer);
    typingTimer = setTimeout(filterOnSearch, typeInterval);
});

// Removing white space from code within pre, code tag. answerd by: voltrevo. #stack overflow
[].forEach.call(document.querySelectorAll('code'), function($code) {
  let lines = $code.textContent.split('\n');

  if (lines[0] === '')
  {
      lines.shift()
  }

  let matches;
  let indentation = (matches = /^[\s\t]+/.exec(lines[0])) !== null ? matches[0] : null;
  if (!!indentation) {
      lines = lines.map(function(line) {
          line = line.replace(indentation, '')
          return line.replace(/\t/g, '    ')
      });

      $code.textContent = lines.join('\n').trim();
  }
});
