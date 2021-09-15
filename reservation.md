
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

[ ] Filter by hoists

[ ] Filter by users

[ ] Include tags, Images and user

[ ] Show count of previous reservations

[x ] Paginate

[ ]Sort by distance if lng/lat provided. Otherwise, oldest first

## Show Office endpoint

[ ] Show count of previous reservations

[ ] Include tags, Images and user

##Create office endpoint
[ ] Host must be authenticated & email verified

[ ] Cannot fill `approval_status`

[ ] Attach photos to offices endpoint



​Can someone tell me how can I learn a topic like system design or database design? or point me to some free sources
