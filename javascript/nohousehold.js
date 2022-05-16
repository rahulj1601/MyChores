//Validates the creation of a new household
function validateCreate() {
    var isValid = 0;
	var name = document.forms["createHousehold"]["name"].value;
    var passocde = document.forms["createHousehold"]["passcode"].value;

	if(name === '') {
        toggleError("create","Name");
	} 
    else if(isExist(name)){
        toggleError("create","Name");
    }
    else {
		toggleCorrect("create","Name");
        isValid++;
	}
	
	if(passocde === '') {
		toggleError("create","Passcode");
	} 
    else if (passocde.length < 5){
        toggleError("create","Passcode");
    }
    else {
		toggleCorrect("create","Passcode");
        isValid++;
	}

    if (isValid == 2){
        return true;
    }
    else{
        return false;
    }
};

//Validates entering a new household
function validateEnter() {
    var isValid = 0;
	var name = document.forms["enterHousehold"]["name"].value;
    var passcode = document.forms["enterHousehold"]["passcode"].value;

	if(name === '') {
        toggleError("enter","Name");
	}
    else {
		toggleCorrect("enter","Name");
        isValid++;
	}
	
	if(passcode === '') {
		toggleError("enter","Passcode");
	}
    else if(passcode.length < 8) {
        toggleError("enter","Passcode");
    }
    else {
		toggleCorrect("enter","Passcode");
        isValid++;
	}

    if (!validHousehold(name,passcode)){
        toggleError("enter","Passcode");
        toggleError("enter","Name");
        isValid=0;
    };

    if (isValid == 2){
        return true;
    }
    else{
        console.log("false");
        return false;
    }
};

//Used by validation functions to show any errors in the forms
function toggleError(form, id){
    $("#"+form+"floating"+id).css("border-bottom","1px solid red");
    $("#"+form+id+" .tick svg").css("display","none");
    $("#"+form+id+" .warning svg").css("display","block");
};

//Used by validation functions to show when form fields are correct
function toggleCorrect(form, id){
    $("#"+form+"floating"+id).css("border-bottom","1px solid green");
    $("#"+form+id+" .warning svg").css("display","none");
    $("#"+form+id+" .tick svg").css("display","block");
};

//Checks if the entry is valid and corresponds to data in the database
function validHousehold(name, passcode){
    var correct = $.ajax({
        url: 'formValidation.php',
        type: 'post',
        async:false,
        data: {name: name, passcode: passcode, function:"checkHouseholdLogin"},
    }).responseText;
    if(correct == 1){return true}
    else {return false}
}

//Checks if the household name already exists and returns the correct boolean feedback
function isExist(name){
    var exists = $.ajax({
        url: 'formValidation.php',
        type: 'post',
        async:false,
        data: {name: name, function:"checkHousehold"},
    }).responseText;
    if(exists == 1){return true}
    else{return false}
};