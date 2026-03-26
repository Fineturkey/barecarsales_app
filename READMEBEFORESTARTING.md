Please do not mess with the script. (.sql file) should be able to just run it on *mysql* and then be set up with XAMMP. Once thats all done you should be able to run it all fine.
http://localhost/carsales_app/index.php. 

Buyers should be able to insert and delete values. 
When coding reference buyers create, delete, edit, every entity should follow a similar pattern to buyer (table) we won't flesh out all of the logic just yet, we just need to be able to insert all of the logic for now and then worry about doing the carry over later. Buyer is already done, if someone can finish up on customer/vehicle that would be amazing.

If you don't know whats going where, reference the ER table in MySQL, this is very easy to do. 

Go to your database, select database -> reverse engineering -> next -> and it should show up.

From there using the ER diagram as provided we should go in the following order since we'll connect every together eventually. Buyer, Customer, Vehicle, Employment history, sale, basically do something that isn't in the middle of everything, as an example we don't want to do purchase and repair just yet since some of the values carry over (i.e. purchase_id) so realistically theres no need to create those values we should just reference it through php.

If you want to do a small one (seller or salesperson) just to get the feel for it, go for it, just follow the logic under the buyer folder and make sure to reference it properly (../ and 'folder'/) make sure the hrefs are correct as well.

All I'll say for now, wouldn't reccomend trying to link it all together just yet. Make the ones that we need and then figure out if we even need all of the entities. 

Cheers. 