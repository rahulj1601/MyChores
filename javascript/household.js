var x=0;

$(document).ready(function() {
    //Displays the necessary sections for the household.php file when it is loaded
    //This ensures the page is presentable to the user
    //If we don't want to display the Done option on the menu because there are no completed chores
    //this if statement will execute
    if ( $('#Done').children().length == 1 ) {
        document.getElementById("Done").style.display = "none";
        document.getElementById("Donebutton").style.display = "none";
        document.getElementById("clearAllButton").style.display = "none";
        x=1;
    }
    var element = document.getElementsByClassName("choreGroup")[x];
    element.style.display = "block"; 
    var button = document.getElementById('vertical-menu').getElementsByTagName('button')[x]; 
    button.classList.add("active");
    $("#carouselControls .carousel-item:first").addClass("active");

    //Determines whether the arrow should be displayed when 
    //scrolling in overlow of chores in allChores section
    $(".choreGroup").scroll(function(){
        var element = $(".choreGroup:visible").attr('id');
        if ($('#'+element).scrollTop() + $('#'+element).innerHeight() >= $('#'+element)[0].scrollHeight){
            $('#'+element+' .scrollDownArrow').css("display","none");
        }
        else{
            $('#'+element+' .scrollDownArrow').css("display","block");
        }
    }); 

    //Prevents the modal from being displayed when the red skip icon or the green clear icon is clicked
    $("#skipChore").click(function(e) {
        e.stopPropagation();
    });

    $("#cornerMenu").click(function(){
        if ($("#menuItems").is(":visible")){
            $("#cornerMenu").css({"transform": "rotate(90deg)", "animation":"backRotate 0.5s forwards"});
            $("#cornerMenu svg:nth-child(2)").fadeOut(250);
            $("#cornerMenu svg:first").delay(250).fadeIn(250);
            $("#menuItems").animate(
                {opacity:0,height:'toggle'},{queue:false,duration:500}
            );
        }
        else{
            $("#cornerMenu").css({"transform": "rotate(0deg)", "animation":"rotate 0.5s forwards"});
            $("#cornerMenu svg:first").fadeOut(250);
            $("#cornerMenu svg:nth-child(2)").delay(250).fadeIn(250);
            $("#menuItems").animate(
                {opacity:1,height:'toggle'},{queue:false,duration:500}
            );
        }
    });
    
    setTimeout(adjustDimensions, 1200);

});

//Controls the dimensions of the components when resizing and loading the household.php script
$(window).on('resize load',adjustDimensions);

function adjustDimensions(){
    //Controls dimensions of the allChores section so the bottom is aligned with the bottom
    //of the listGroup section showing the chores in order of when they are due
    //Only resizes the page elements to align the bottom of the columns if there are more than 4 
    //chores added to the list group div on the left of the page
    if ($(".list-group:first > .list-group-item").length >= 4){
        var height = $(".list-group:first").height() - $("#carouselControls .carousel-control-prev").height() - 30;
        var numButtons = ($("#vertical-menu > button").length);
        var desiredHeight = (height/numButtons)-20;
        var choreGroupHeight = height - 40;

        if (x==1){
            numButtons = ($("#vertical-menu > button").length)-1;
            desiredHeight = (height/numButtons)-20;
        }
        if ( $('#carouselControls  .carousel-inner').children().length == 0 ) {
            console.log("dont display");
            document.getElementById("carouselControls").style.display = "none";
            height = $("#nextChores").height();
            desiredHeight = (height/numButtons)-20;
            choreGroupHeight = $("#nextChores").height() - 40;
        }

        document.getElementById("allChores").style.maxHeight = height+"px";
        $(".choreGroup").css("max-height",choreGroupHeight);
        $("#vertical-menu > button").height(desiredHeight);
    }

    //Determines whether the scroll down arrow should be displayed for the initial chore group
    //This depends on the height of the elements and so changes when the window resizes
    var element = document.getElementsByClassName("choreGroup")[x];
    var elementByID = $(element).attr('id');
    var groupHeight = $('#'+elementByID).height();
    var totalHeight=0;
    var singleChores = element.getElementsByClassName("singleChore");
    $(singleChores).each(function() {
        totalHeight += $(this).outerHeight();
    });
    if (totalHeight > groupHeight){
        $('#'+elementByID+' .scrollDownArrow').css("display","block");
    }

};

//Controls the section where all of the chores are displayed and tabs can be selected to view them
//Controls when to display a specific list group
//Displays certain elements depending on which item has been chosen from the menu
function showChores(name){
    //Hides scroll down arrow
    $('#'+name+' .scrollDownArrow').css("display","none");

    //Display clear all button when done is selected
    if (name=="Done"){document.getElementById("clearAllButton").style.display = "block";}
    else{document.getElementById("clearAllButton").style.display = "none";}

    //Hides all choreGroups for the groups which aren't the selected one
    var chores = document.getElementsByClassName("choreGroup");
    for (var i = 0; i < chores.length; i++){
        chores[i].style.display = "none";
    }

    //Displays the chores for the selected choreGroup
    $("#"+name).fadeIn("Slow");

    //Sets the selected menu item to active by adding the class 'active'
    var menuOptions = document.getElementById('vertical-menu').getElementsByClassName("active");
    for (var i = 0; i < menuOptions.length; i++){
        menuOptions[i].classList.remove("active");
    }
    var button = document.getElementById(name+"button");
    button.classList.add("active");

    //Determines whether there is an overflow of chores
    //If there is the scroll down arrow will be displayed
    var groupHeight = $('#'+name).height();
    var totalHeight=0;
    $('#'+name+' .singleChore').each(function() {
        totalHeight += $(this).outerHeight();
    });
    if (totalHeight > groupHeight){
        $('#'+name+' .scrollDownArrow').css("display","block");
    }

};

//Controls when the buttons are displayed or not
//These are the buttons to update the status of chore
function statusButtons(){
    var statusButtons = document.getElementsByClassName("statusButtons");
    for (var i = 0; i < statusButtons.length; i++){
        var element = statusButtons[i];
        if (element.style.display == "grid"){
            element.style.display = "none";
        }
        else{
            element.style.display = "grid";
        }
    }
};

//Submits a form for the user when they need to update the status of a list
function updateStatus(choreStatus,choreID){
    $.ajax({
        url: 'choreStatus.php',
        type: 'post',
        data: {choreID: choreID, choreStatus:choreStatus},
        success: function(response){
            location.reload();
        }
    });
}; 

//Timer function which is started using the load function further down the page
function startTimer(time, element){
    var hours, minutes, seconds;
    var totalSeconds = parseInt(time.split(":")[0] * 3600) + parseInt(time.split(":")[1] * 60) + parseInt(time.split(":")[2]);
    $('.timer').css("color","red");
    setInterval(function () {

        hours = parseInt(totalSeconds / 3600);
        minutes = parseInt((totalSeconds - hours*3600) / 60);
        seconds = parseInt(totalSeconds % 60);

        if (hours<10){hours="0"+hours;}
        if (minutes<10){minutes="0"+minutes;}
        if (seconds<10){seconds="0"+seconds;}

        if (--totalSeconds > 0){
            element.innerHTML = (hours == 0 ? "" : hours + ":") + minutes + ":" + seconds;
        }
        else{
            element.innerHTML = "00:00";
        }

    }, 1000);
}

$(window).on('load', function(){
    timers = document.getElementsByClassName('timer');
    if (timers.length > 0){
        for (var i=0; i<timers.length; i++){
            startTimer(timers[i].innerHTML, timers[i]);
        }
    }
});

//Opens a modal when this function is called
function openModal(name, choreid){
    $.ajax({
        url: 'getModalContents.php',
        type: 'post',
        data: {name:name, choreid:choreid},
        success: function(response){
            var responseData = JSON.parse(response);
            $('#emptyModal .modalHeader h5').html(responseData.header);
            $('#emptyModal .modalBody').html(responseData.body);
            $('#emptyModal .modalFooter').html(responseData.footer);
            $('#emptyModal').fadeIn("fast");
        }
    });
}

//Closes any open modals when this function is called
function closeModal(){
    $('#emptyModal').fadeOut("fast");
}

//close modal on background click
window.onclick = function(event) {
    if (event.target == document.getElementById('emptyModal')) {
        closeModal();
    }
}

//Activate the walkthrough
function viewWalkthrough(){
    document.getElementById("walkthrough").style.display = "block";
    if ($('#listGroup').hasClass('active')){
        $('.list-group').css("outline","solid #0404e0");
        $('#carouselControls').css("outline","none");
        $('#allChores').css("outline","none");
    }
    else if ($('#carousel').hasClass('active')){
        $('.list-group').css("outline","none");
        $('#carouselControls').css("outline","solid #0404e0");
        $('#allChores').css("outline","none");
    }
    else if ($('#chores').hasClass('active')){
        $('.list-group').css("outline","none");
        $('#carouselControls').css("outline","none");
        $('#allChores').css("outline","solid #0404e0");
    }
}

//Need to wait till Bootstrap has made the correct carousel item active
function walkthroughDelay(){
    setTimeout(viewWalkthrough,1000);
}

//Allows the walkthrough to be closed
function closeWalkthrough(){
    $("#walkthrough").fadeOut('fast');
    $('.list-group').css("outline","none");
    $('#carouselControls').css("outline","none");
    $('#allChores').css("outline","none");
}

//Uses jQuery UI to make the walkthrough element draggable
$(function() {
    $("#walkthrough").draggable({containment: ".container-fluid"});
});