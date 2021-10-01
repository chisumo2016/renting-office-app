
### Booking Reservation
# Office Booking Reservation Application/LETTING FOR REMOTE WORKER
Git Hub
themsaid/ergodnc

# Setup Migration ,Models , Factories

[x] Prepare Migrations
    - Users
    - Tags
    - Offices
    - images
    - reservation
[x]Seed the Initial tags

   - In migration table

   Mass Assigment 

      -ServiceProvider

[x] Prepare Models

      -Relationship

      -Casts

[x] Prepare Factories

     -Adding the const value in the model and use in factories

[x] Prepare Resources

      - php artisan make:resource UserResource

      -  php artisan make:resource OfficeResource 

      -  php artisan make:resource ReservationResource

      -  php artisan make:resource TagResource

      -   php artisan make:resource ImageResource

[x] Tags

    -Routes

    -Controller

    -Tests


[x] Offices

    -List office 

    -Read Office

    -Create Office

# TODO NEXT
[ ] Filter the offices returned

[ ] Paginate the list offices endpoint

[ ] Show office endpoint

[ ] Create office endpoint

## List Offices Endpoint

[x] Show only approved and visible record

[x] Filter by hoists

[x] Filter by users

[x] Switch to using Custom Polymorphic Types

[x]"Ordered by distance but don't include the distance attribute

[x] Include tags, Images and user //problem

    ErrorException: Undefined array key "tags"

[ x] Show count of previous reservations

        ErrorException: Undefined array key "reservations_count"

[x ] Paginate

[x] Sort by distance if lng/lat provided. Otherwise, oldest first

        Expected response status code [200] but received 500.

        Failed asserting that 200 is identical to 500.

        https://stackoverflow.com/questions/2234204/find-nearest-latitude-longitude-with-an-sql-query

## Show Office endpoint

[x] Show count of previous reservations

[x] Include tags, Images and user

[ x ] Configure the resources

## Create office endpoint

[x] Host must be authenticated & email verified

[x] Token (if exists) must allow `office.create`

[x] Validation


#TODO
 [x] Office approval status should be pending pr approved only .. no rejected  (remove rejected in office model, officeFactory)
 [x] Store Office inside a database transaction(OfficeController)

## Update Office Endpoint 
[ x ] Create an endpoint in the api file 

[ x] Must be authenticated  & email verified

   NB:abstract the validation (create and update)

[ x] Token (if exists) must allow `office.update`

[ x ] Can only update their own offices  - use Policy 

[ x ] Validation

[X] Mark as pending when critical attributed are updated and notify admin(create notification classphp artisan make:notification OfficePendingApproval
)
NB:Send a notification inside the controller or Que (implements ShouldQueue)

## Create Office Endpoint
 [x] Notify admin on new Office

## Delete Office Endpoint

[ x ] Must be authenticated  & email verified

[ x ] Token (if exists) must allow `office.delete`

[ x ] Can only delete their own offices

[x] Cannot delete an office that has a reservation

#TODO
[x] Identify who an admin is by adding an `is_admin` attribute to the users table

[x]Show hidden and unapproved office when filtering by `user_id` and the auth matches the user so hosts can see all their listings

## Office Photos

[x] Attach photos to offices endpoint

[x ] Allow choosing a photo to become the featured photo
      we want the user to be able to select  an image/photo belongs to an office,Office has many photos and we want
      the user to select the specific photo , and this photo will be  allowed to be promoted as featured photo
      will be displayed in the UI , list the office resources in the front end : example flags or score of current stamps

   -featured_image_id field in offices
    -Add the relationship in office model
[ x] Deleting a photo - Must have at least one photo if it's approved



#TODO Housing keeping
[x] Delete all images when deleting an office
[ ] Use the default disk to store public images so it's easier to switch tp different drivers in production
[x] Switch to using Sanctum guard by default
[x] use the new [assertNotSofDeleted]  https://github.com/laravel/framework/pull/38886
[x] use the new LazilyRefreshDatabase testing trait on the base test case
[x] use keyed implicit binding in the office image routes so laravel scopes to the office that image belongs to [tweet]
  
## List Reservations Endpoint 

[x ] Must be authenticated  & email verified (User Must )

[x ] Token (if exists) must allow `reservations.show`

[ x] Can only list their own reservations or reservations on their offices

[x ] Allow filtering by office_id  only for authenticated host

[ ] Allow filtering by user_id  nly for authenticated user

[x] Allow filtering by date range

[ ] Allow filtering by status

[] Paginate

## Make  Reservations Endpoint

[ ] Must be authenticated  & email verified

[ ] Token (if exists) must allow `office.make`

[ ] Cannot make reservations on their own property

[ ] Validate no other reservation conflicts with the same time

[]Use locks to make the process atomic

[] Email user and Host when a reservation is make
[] Email user and Host on reservation start day
[] Generate WIFI password for new reservations (store encrypted)


## Cancel  Reservations Endpoint

[ ] Must be authenticated  & email verified

[ ] Token (if exists) must allow `office.cancel`

[ ] Can only cancel their own reservation

[ ] Can only cancel  an active reservation that has a start_date in the future

## Handling Billing with Cashier









​Can someone tell me how can I learn a topic like system design or database design? or point me to some free sources
