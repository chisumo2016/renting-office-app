
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

#TODO NEXT
[ ] Filter the offices returned

[ ] Paginate the list offices endpoint

[ ] Show office endpoint

[ ] Create office endpoint

## List Offices Endpoint

[x] Show only approved and visible record

[x] Filter by hoists

[x] Filter by users

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

##Create office endpoint
[ ] Host must be authenticated & email verified

[ ] Cannot fill `approval_status`

[ ] Attach photos to offices endpoint



​Can someone tell me how can I learn a topic like system design or database design? or point me to some free sources
