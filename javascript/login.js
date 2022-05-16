//Check if the login details are correct on submission of the form
function validateForm() {
    var isValid = 0;
    var username = document.forms["login"]["username"].value;
    var password = document.forms["login"]["password"].value;

    if(username === '') {
		toggleError("Username");
	}
    else {
		toggleCorrect("Username");
        isValid++;
	}
	
	if(password === '') {
		toggleError("Password");
	}
    else {
		toggleCorrect("Password");
        isValid++;
	}

    if (!isCorrect(username,password)){
        toggleError("Username");
        toggleError("Password");
        isValid=0;
    }

    if (isValid == 2){
        return true;
    }
    else{
        return false;
    }
};

//Shows an error for the field which has been passed into this function
function toggleError(id){
    $("#floating"+id).css("border-bottom","1px solid red");
    $("#"+id.toLowerCase()+" .tick svg").css("display","none");
    $("#"+id.toLowerCase()+" .warning svg").css("display","block");
};

//Shows the entry data is correct
function toggleCorrect(id){
    $("#floating"+id).css("border-bottom","1px solid green");
    $("#"+id.toLowerCase()+" .warning svg").css("display","none");
    $("#"+id.toLowerCase()+" .tick svg").css("display","block");
};

//Checks if the entry data is correct and returns true or false depending on the response
function isCorrect(username, password){
    var correct = $.ajax({
        url: 'formValidation.php',
        type: 'post',
        async:false,
        data: {username: username, password:password, function:"checkLogin"},
    }).responseText;
    if(correct == 1){return true}
    else{return false}
};