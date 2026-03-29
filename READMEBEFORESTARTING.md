Please do not mess with the script. (.sql file) should be able to just run it on *mysql* and then be set up with XAMMP. Once thats all done you should be able to run it all fine.
http://localhost/barecarsales_app/index.php

Uhhh, I dunno. At this point just go in order since all of the logic has been undermined basically.

Pick and choose which entity to work on, make sure you aren't working on the same on, again, take the following steps before starting.

I don't know if he's going to use XAMMP, assuming not maybe it's important to test it on a different reciever (not its actual name) but alpache may be different from whatever Zhengs going to be using.

If someone wants to test WAAMP (I think its called) please do but if using XAMPP complete the following steps.

Firstly, create the actual diagram on MySQL, to do this compile/execute the SQL script and then in the header click Database -> Reverse engineer -> next -> next and you should have the full ER diagram as opposed to the actual table code.

Given this ER diagram I'd reccomend following similar logic to employee, don't worry about the nuances (i.e. data type conversion, drop boxes, box selection, etc) those can be compelted later. You should be able to create all 4 files (create, delete, edit, base) using employee. CTRL F (employee in this case) and replace everything with the name of the entity you'll be working on and just go from there.

Before starting make sure that someone isn't already working on said entity that you are working on so just shoot a message to the GC. 

If we want more complicated logic we can add once complete, I suggest making the website look a little nicer than what it is, I added the bare minimum amount of CSS just to get started but the header and index (.php) can really use some work.

Good luck!