//Checks if the user has entered valid details when registering
function validateForm() {
    var isValid = 0;
	var name = document.forms["register"]["name"].value;
    var email = document.forms["register"]["email"].value;
    var username = document.forms["register"]["username"].value;
    var password = document.forms["register"]["password"].value;

	if(name === '') {
        toggleError("Name");
	} 
    else {
		toggleCorrect("Name");
        isValid++;
	}
	
	if(email === '') {
		toggleError("Email");
	} 
    else if (!isEmail(email)) {
		toggleError("Email");
	} 
    else {
		toggleCorrect("Email");
        isValid++;
	}

    if(username === '') {
		toggleError("Username");
	}
    else if(isExist(username)){
        toggleError("Username");
    }
    else {
		toggleCorrect("Username");
        isValid++;
	}
	
	if(password === '') {
		toggleError("Password");
	} 
    else if (password.length < 8){
        toggleError("Password");
    }
    else {
		toggleCorrect("Password");
        isValid++;
	}

    if (isValid == 4){
        return true;
    }
    else{
        return false;
    }
};

//jQuery regex to check if the email is valid
function isEmail(email) {
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
};

//Marks a field when there is an error
function toggleError(id){
    $("#floating"+id).css("border-bottom","1px solid red");
    $("#"+id.toLowerCase()+" .tick svg").css("display","none");
    $("#"+id.toLowerCase()+" .warning svg").css("display","block");
};

//Marks the field as correct when the details have been entered correctly
function toggleCorrect(id){
    $("#floating"+id).css("border-bottom","1px solid green");
    $("#"+id.toLowerCase()+" .warning svg").css("display","none");
    $("#"+id.toLowerCase()+" .tick svg").css("display","block");
};

//Checks to see if the username hasn't entered an existing username
function isExist(username){
    var exists = $.ajax({
        url: 'formValidation.php',
        type: 'post',
        async:false,
        data: {name: username, function:"checkUsername"},
    }).responseText;
    if(exists == 1){return true}
    else{return false}
};