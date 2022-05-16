<?php

//Produces the label for a progress bar
//Used in a function to reduce the number of lines in the code as this part of code is used more than once

function progressBar($choreStatus){
    switch ($choreStatus){
    case(0):
        return "</div><small class='justify-content-center d-flex w-100' style='color:black;'>Pending</small>";
        break;
    case(25):
        return "Started</div>";
        break;
    case(50):
        return "In Progress</div>";
        break;
    case(75):
        return "Almost Done</div>";
        break;
    case(100):
        return "Complete</div>";
        break;
    }
}

?>