### How to run

1. Create a directory [WORKING_DIR] e.g `mkdir berzel-inno-challenge`
2. cd to [WORKING_DIR]
3. git clone the frontend repository inside [WORKING_DIR] `git clone git@github.com:berzel/inno-fe.git frontend`
4. git clone the backend repository inside [WORKING_DIR] `git clone git@github.com:berzel/inno-be.git backend`
5. cd to backend directory
6. run `cp .env.example .env`
7. run `composer install` 
8. run `./vendor/bin/sail up`
9. run `./vendor/bin/sail artisan migrate`
10. run `./vendor/bin/sail artisan queue:work`
11. create a new york times developer account [here](https://developer.nytimes.com/apis) 
12. create a new york times app, making sure to give it access to the article search api and copy the api key to the .env file, key `NEW_YORK_TIMES_KEY`
13. create a developer account on the guardian website [here](https://open-platform.theguardian.com/access/) and copy the key to the .env file, key `THE_GUARDIAN_KEY`
14. open the backend directory in a new terminal
15. run `./vendor/bin/sail artisan app:poll-articles`
16. open [http://localhost:3000](http://localhost:3000) in your browser of choice
