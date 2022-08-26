# Instruction

run these command to get started

`composer install`
`cp .env.example .env`
`php artisan key:generate`

edit .env to suit your database information

`php artisan migrate`
`php artisan db:seed`



## Models

this app use 3 models to represent the flow needed to achieve business logic 

### Subscriber

`subscriber` represent the subscriber that follow a website
`subscriber` implement notifiable
`subscriber` use notification to inform the subscriber of a new post in `website`

`subscriber` belongsToMany `website`

validate `website_ids`, `website_id` , `email`, `name`

### Website 

`website` represent website that available in app
`website` belongsToMany `subscriber`
`website` has `subscriber` cached in  `getSubscribersCachedAttribute` method in the model
`website` has `dropCachedSubscribers` method to drop the cached user
`website` hasMany `post`

validate `name`, `endpoint`

### Post

`post` belongsTo `website`
`post` has notification object called `NewPost` that represent job and email that need to be send to `subscriber`

validate `title`, `description`, `url`, `website_id`