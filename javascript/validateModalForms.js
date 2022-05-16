//Validates the add and edit forms
function validateForm(form) {
    var isValid = 0;
    var formID = form+"Chore";
	var name = document.forms[formID]["choreName"].value;
    var description = document.forms[formID]["choreDescription"].value;
    var frequency = document.forms[formID]["choreFrequency"].value;
    var deadline = document.forms[formID]["deadlineDate"].value;
    var notification = document.forms[formID]["notificationDate"].value;
    var user = document.forms[formID]["choreUser"].value;
    var error = $(".error");

	if(name === '') {
        toggleError(form, "Name");
	} 
    else {
		toggleCorrect(form, "Name");
        isValid++;
	}

    if(description === '') {
        toggleError(form, "Description");
	} 
    else {
		toggleCorrect(form, "Description");
        isValid++;
	}
    
    if(frequency === '') {
        toggleError(form, "Frequency");
	} 
    else {
		toggleCorrect(form, "Frequency");
        isValid++;
	}

    if(notification === '') {
        toggleError(form, "Notification");
	} 
    else {
		toggleCorrect(form, "Notification");
        isValid++;
	}

    if(deadline === '') {
        toggleError(form, "Deadline");
	}
    else {
		toggleCorrect(form, "Deadline");
        isValid++;
	}

    if(isOlder(new Date(deadline),new Date())) {
        toggleError(form, "Deadline");
        errorMessage("Deadline date must be in the future",error);
        isValid--;
    }
    else if(isOlder(new Date(deadline),new Date(notification))) {
        toggleError(form, "Notification");
        errorMessage("Notification date must be before deadline date",error);
        isValid--;
    }
    else if(isOlder(new Date(notification),new Date())) {
        toggleError(form, "Notification");
        errorMessage("Notification date must be in the future",error);
        isValid--;
    }
    else {
        hideErrorMessage(error);
	}

    if(user === '') {
        toggleError(form, "User");
	} 
    else {
		toggleCorrect(form, "User");
        isValid++;
	}

    if (isValid == 6){
        return true;
    }
    else{
        return false;
    }
};

//Toggles an error when this function is called for the specified field
function toggleError(form, id){
    $("#"+form+id).css("border","1px solid red");
};

//Toggles the field as correct when this function is called for a specific field
function toggleCorrect(form, id){
    $("#"+form+id).css("border","1px solid green");
};

//Compares two dates to see if dateOne is older than dateTwo
function isOlder(dateOne, dateTwo){
    if (dateOne < dateTwo) {
       return true;
    }
    else {
      return false;
    }
}

//Updates the error on the page
function errorMessage(message,error){
    $(error).html(message);
    error.css("display","block");
}

function hideErrorMessage(error){
    error.css("display","none");
}


//Validation for the changing username and password or household name and password
function validateSettings(form){
    var isValid = 0;
    var formID = form+"Form";
	var oldPassword = document.forms[formID]["OldPassword"].value;

	if(correctPassword(form+"Password", oldPassword)){
        toggleCorrect(form,"OldPassword");
        toggleShowDetails(form);
        toggleDisabled(form,"OldPassword");
        isValid++;
        
        var newName = document.forms[formID]["NewName"].value;
        var newPassword = document.forms[formID]["NewPassword"].value;
        var newPasswordConfirm = document.forms[formID]["NewPasswordConfirm"].value;
        
        if(newName === '') {
            toggleError(form,"NewName");
        }
        else if (takenName(form+"Name",newName)){
            toggleError(form,"NewName");
        }
        else{
            toggleCorrect(form,"NewName");
            isValid++;
        }

        if(newPassword !== newPasswordConfirm || newPassword.length<8) {
            toggleError(form,"NewPassword");
            toggleError(form,"NewPasswordConfirm");
        }
        else if (newPassword !== '' && newPasswordConfirm !== ''){
            toggleCorrect(form,"NewPassword");
            toggleCorrect(form,"NewPasswordConfirm");
            isValid++;
        }
        else{
            toggleError(form,"NewPassword");
            toggleError(form,"NewPasswordConfirm");
        }
    }
    else{
        toggleError(form,"OldPassword")
    }

    if (isValid == 3){
        return true;
    }
    else{
        return false;
    }
}

//This will show the rest of the form once the user has entered the correct password
function toggleShowDetails(form){
    $("#"+form+"Details").slideDown();
}

//The password they enter will become a disabled input meaning it cannot be changed
function toggleDisabled(form, input){
    $("#"+form+input).prop('disabled', true);
}

//Checks to see if the password they have entered is correct for the function
function correctPassword(form, oldPassword){
    var correct = $.ajax({
        url: 'formValidation.php',
        type: 'post',
        async:false,
        data: {password:oldPassword, function:form},
    }).responseText;
    if(correct == 1){return true}
    else{return false}
};

//Checks if the new username or new household name they have chosen is taken by another user
//If they enter the current username/household name they are able to keep it
function takenName(form, newName){
    var taken = $.ajax({
        url: 'formValidation.php',
        type: 'post',
        async:false,
        data: {name:newName, function:form},
    }).responseText;
    if(taken == 1){return true}
    else{return false}
};