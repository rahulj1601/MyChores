//Ensures the shadow is activated under the navbar when the user scrolls
window.onscroll = function(){
    activateShadow()
}
function activateShadow(){
    var element = document.getElementsByClassName("navbarContainer")[0];
    if ($(window).scrollTop()>10){
        element.classList.add("shadow-on");
    }
    else{
        element.classList.remove("shadow-on");
    }
}

//Counts inactivity time and will redirect the user to the login page if they are inactive for 15 minutes
var inactiveTime = 0;
$(document).ready(function () {
    setInterval(increaseInactiveTime,60000);
    $(this).on("mousemove keypress click", function(){
        inactiveTime = 0;
    });
});
function increaseInactiveTime(){
    inactiveTime++;
    if (inactiveTime >= 15){
        window.location.href = "https://cs139.dcs.warwick.ac.uk/~u2030590/cs139/coursework/login.php";
    }
};

//Creating the loader
function activateLoader() {
    setTimeout(showPage, 1000);
}
function showPage() {
    $('#loader').fadeOut();
    $('#page').fadeIn(700);
}