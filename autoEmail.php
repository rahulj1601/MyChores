<?php

// Keep executing even if you close your browser
ignore_user_abort(true);

// Execute for an unlimited time
set_time_limit(0);

include "database.php";
include "security.php";
$data = new Database();

// Loop infinitely, stops when a file called stop.txt is created
while (!file_exists('stopAutoEmail.txt')) {

    //gets the current date and time in the required format
    $currentDateTime = date("Y-m-d\TH:i");

    //gets the different chores with the notificationDate the same as the current date and time
    $stmt = $data->prepare("SELECT * FROM chores WHERE notificationDate=:currentDateTime");
    $stmt->bindValue(':currentDateTime',$currentDateTime,SQLITE3_TEXT);
    $currentEmails = $stmt->execute();

    while ($current = $currentEmails->fetchArray()){
        //Get user to email
        $username = h($current['choreUser']);

        $stmt = $data->prepare("SELECT email FROM users WHERE username=:username");
        $stmt->bindValue(':username',$username,SQLITE3_TEXT);
        $userDetails = $stmt->execute()->fetchArray();

        //Get chore name
        $choreName = h($current['choreName']);

        $to = h($userDetails['email']);
        $subject = "Your Chore Reminder - ".h($choreName);

        $message = "
        <div style='background-color:#00A92A;color:white;padding:50px;'>
            <div style='text-align:center;background-color:white;padding:20px 0px 20px 0px;'><a href='https://cs139.dcs.warwick.ac.uk/~u2030590/cs139/coursework/'><img style='height:60px;width:auto;' src='https://cs139.dcs.warwick.ac.uk/~u2030590/cs139/coursework/images/mychoreslogothin.png'></a></div>
            <h1>".h($choreName)."</h1>
            <p style='font-size:18px;'>Due ".date('l, F jS Y \a\t g:ia', strtotime($current["deadlineDate"]))."</p>
            <p style='font-size:17px;'>".h($current['choreDescription'])."</p>
        </div>";

        //if the headers were already set in the previous loop they will be unset 
        //This allows them to be reallocated for a new email in the loop
        if (isset($headers)){
            unset($headers);
        }
        
        //Setting up the headers and content type of the email
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=iso-8859-1';
        $headers[] = 'From: MyChores <noreply@mychores.com>';

        mail($to,$subject,$message,implode("\r\n", $headers));
    }

    sleep(60);
}

?>