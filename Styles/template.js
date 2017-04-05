/*

 This code creates the functionality present in the base template.
 Dual info tabs and accordian info box are controlled here.
 
 Code inspired by W#Schools.com

*/

<!--
    
// Change visibility/style between selected info tabs on click
//
function openTab(evt, tabChoice) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        // this turns off both content tabs, so you can turn on the one associated to the click event
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        // this kills both active tabs (no active color dislpayed, reset below)
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabChoice).style.display = "block";
    // this resets the color for the clicked, now active, tab
    evt.currentTarget.className += " active";
};


// Auto-opens the result tab 
//
function openResultTab() {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        // this turns off both content tabs, so you can turn on the one associated to the click event
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        // this kills both active tabs (no active color dislpayed, reset below)
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById("Results").style.display = "block";
    // this resets the color for the clicked, now active, tab
    document.getElementById("result-link").className += " active";
};


// This is for the accordian element
//   
var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
    acc[i].onclick = function() {
        this.classList.toggle("active");
        var panel = this.nextElementSibling;
        if (panel.style.maxHeight){
        panel.style.maxHeight = null;
        } else {
        panel.style.maxHeight = panel.scrollHeight + 'px';
        } 
    }
}

-->