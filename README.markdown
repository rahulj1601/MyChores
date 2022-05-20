#MyChores (fully compatible with Chrome)

##What is MyChores?
MyChores is a web app where users can form their own household and add 
various chores to complete around the house

##How can it be viewed?
Because this is a web app I have developed which requires an external database to store user information, 
it must make use of web hosting which is not currently set up.

##Description of Required Features

User Registration:
I have included the ability for the user to enter their name, email, username and password. 
These details will be checked against the database using AJAX to see if the username exists 
and the corresponding errors will be toggled based on the response and the other field entries.

User Authentication:
I have included the ability for the user to login using their username and password. These 
details can be validated using AJAX which will respond to the user by toggling an error response 
or correct response. If the login details are correct the user will be directed to the index.php 
page where they will be either shown their household if they are already part of a household or 
can choose to join or create a new household if they are not part of a household.

Adding a Chore:
When the user is part of a household they are able to select the menu icon in the bottom right 
and choose to add a new chore. This requires adding details about the chore such as the chore 
name, chore description, chore frequency, deadline date and time, notification date and time, 
and finally the chore user. The chore user can be randomly allocated if chosen, this causes the 
chore to randomly allocate to a user and once allocated will remain with that user every time the 
chore is required to be done. You can choose to randomly allocate the chore again by going to the 
edit options for the chore. But, I feel it is good to remain with a specific user as you tend to 
repeat the same chores once you have been tasked with it for the first time.

Chore Allocation:
This is handled when the chore is created and is mentioned in the above section.

Marking Chores Complete:
On the users own chore section they have the option to update the status of the chores and can 
skip the chore, mark it as done, and also update the completion status if it is pending, just 
started, in progress, almost complete. This will submit a form with the selected status and they 
will then be able to view an interface with the updated chore.

Displaying the Status of Chores:
The status of chores has been displayed in a progress bar with the corresponding label of the chore. 
This progress bar is animated to make the interface a bit more attractive to the user. The status of a 
chore can be viewed by looking at the progress bars in the section on the left of the page. This section 
displays the top 5 or 10 chores belong to the household in order of the date they are due. 5 chores are 
displayed if there are only 3 users in the household, and 10 chores are displayed otherwise. This is 
because when there are more users you generally have more chores so displaying more chores on the left 
of the page would be useful. You can also view the status of each chore by looking at the section which 
shows each user and the chores they have.

Notifications:
The notifications are receivable via the email that is attached to the user account. The user is informed 
of chores they are assigned to and they can choose when to receive the notification. The notification date 
is automatically updated by adding the chore frequency to the notification date to get the next time the 
user will need to be notified of the chore. This same process occurs for the deadline date. When the chore 
is marked as complete or skipped by the user it is updated so the deadline date becomes the next deadline 
and the same for the notification date.


##Additional Features

Email:
I will receive an email for a specific chore on the time and date I have selected for that chore when 
creating it.

Animations:
There are several animations to improve the user experience such as a loader animation which ensures the 
page is loaded nicely and it will then fade out to display the actual page contents. There are also animations
with the menu, the modals and also the scroll down arrow for the section where you can view the chores of 
different users.

Change Account Details:
I am able to go to the main menu and select the settings icon where I can change details for my household 
login as well as details for my own user account login if required

Responsive Design:
This web app works well on any screen size as it uses Bootstrap and additional media queries for responsiveness.

Inactive Time:
The web app will automatically log the user out if the user has not been using the application for 15 
minutes. If it detects any mouse movement, clicking or a keypress the inactive time goes back down to zero.

Skip Chores:
I am able to skip any of my own chores if required, this is useful if I cannot complete that chore on a 
specific occasion but will continue to perform that chore on the next deadline date.

Getting Started Modal:
I can view some information on where to get started as well as a nice walkthrough of various aspects of 
the application and what they are able to do and show me.

Last 24 Hour Timer:
On the last 24 hours of a chore in the list group section on the left of the screen I can see a selection 
of the chores ordered by their deadline dates. The ones which are due within the next 24 hours display a 
timer which is highlighted in red and this helps to clearly identify any top priority chores which must 
be completed as soon as possible.

Multiple Households:
I am able to join or create my own household by giving it a name and a password. This means this web app 
can support several different households each composed of various users.


##Compatibility

This web application is compatible with Google Chrome and Microsoft Edge. However, there are some 
functions which do not work within Safari and also within Firefox. The datetime-local input type of my 
forms where you choose to add a chore doesn’t work within Firefox and it results in being a text input 
which isn’t suitable for the web app. There is also some styling which doesn’t work as it is meant to 
for Firefox. However, all functions work perfectly as expected when using Chrome on any device. Therefore,
use Chrome when running this web app.
 

##References

Green background image used for the login and registration pages
https://wallpaperaccess.com/green-geometric

Regex used for email validation
https://stackoverflow.com/questions/46155/how-to-validate-an-email-address-in-javascript

I used this website to find out how to adjust the background opacity without changing the foreground 
opacity on the login and registration pages
https://css-tricks.com/snippets/css/transparent-background-images/

I also made use of jQuery, jQuery UI, Bootstrap and a jQuery additional plugin called jQuery UI Touch 
Punch. This additional plugin was to enable the ability to drag the walkthrough box on mobile.
