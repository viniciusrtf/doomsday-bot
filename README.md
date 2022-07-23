<h1 align="center">Doomsday Bot</h1>

## About Doomsday Bot

In the past I asked candidates for dev positions to show their skills through this suggested project: 

> Build a Telegram chatbot that fetches hazardous asteroids from NASA NEOWS API and tell the user if there's any danger for today. As a bonus, build a feature for receiving regular notifications on these asteroids. Also, write some documentation on how to get your chatbot up and running.

Well, frankly, the majority of candidates didn't have the time for this (neither would I) and it got retired from the recruitment process.

That said, it is time to release my own solution, because.. why not?

## Running it locally (using BotMan Tinker)

Clone this repository in your local machine.

```console
$ git clone https://github.com/viniciusrtf/doomsday-bot.git
$ cd doomsday-bot
```

OK then, now you should make sure you have PHP 7.4 installed in your system. For Ubuntu:

```console
$ sudo apt update && sudo apt install php7.4 php7.4-curl php7.4-mbstring php7.4-sqlite3
```

Install composer if you don't have it yet

```console
$ php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
$ php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
$ php composer-setup.php
$ php -r "unlink('composer-setup.php');"
```

Optionally, to make `composer` a global command in your system:

```console
$ mv composer.phar /usr/local/bin/composer
```

Now you can install Laravel, BotMan and all their dependencies:

```console
$ composer install
```

Obs: Use `$ php composer.phar install` if you didn't installed it globally.

Now, let's make a .env file and generate a Laravel App Key:

```console
$ cp .env.example .env
$ php artisan key:generate
```

Open the `.env` file with with your favorite text editor and make sure you set database connection to sqlite. 

```
DB_CONNECTION=sqlite
```

And **delete** all other `DB_*` configuration.

Initialize the database and run the migrations:

```console
$ touch database/database.sqlite
$ php artisan migrate
```

For the email settings in `.env` file, I prefered to create a free account on SendGrid and creating an API Key, so my email settings is like this:

```
MAIL_DRIVER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=<my-sendgrid-api-key>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=<your.authenticated@domain.com>
MAIL_FROM_NAME="Doomsday Bot"
```

But you can simplify just using Mailtrap free tier if you're just testing this thing.

Now, you should get a NASA API Key. Go to https://api.nasa.gov/ and fill in the "application form". As I'm writing this (2021/03/29) there was no waiting time to be approved or anything, so the API Key should be provided right away. This could have changed if your reading this too much in the future.

If you got your API Key, make sure there's a line in `.env` file like this:

```
NASA_NEWOWS_API_KEY=<your-api-key>
```

Start the BotMan Tinker:

```console
$ php artisan botman:tinker
```

A text-based chat will appear on your terminal. Go ahead and ask the Bot this:

```
Are there any asteroid endangering Earth today?
```

And if the bot reply accordingly to that, everything should be working fine.

## Check notifications

You can check if the Email notifications are working this way:

```console
$ php artisan schedule:run
```

In order to check Telegram notifications, see "Running on Telegram" and complete all steps before running the comand above.

## Testing

The test coverage is minimum at this point, but you can still check if Doomsday Bot code isn't completely broken by executing:

```console
$ ./vendor/bin/phpunit --testsuite=BotMan
```

## Running on Telegram

If you must see Doomsday Bot actually doing its thing on Telegram, you'll need a Telegram API Key. In order to do so, open your Telegram (preferably in desktop), and search for the user "BotFather". Open a new conversation with this user and type `/newbot`.

Choose a `name` and a `username` for the bot. If BotFather tells you the username is taken, don't try choosing another, it won't work. That's a bug. type `/newbot` and start again.

If everything goes right, you should receive a "congratulations" message containing your API Key.

Place this key in your `.env` file like this:

```
TELEGRAM_TOKEN=<your-api-key>
```

Now you'll need an HTTPS server, otherwise Telegram won't register your chatbot.

**Via ngrok**

With ngrok you can use your local machine as a Web Server opened to the Internet through HTTPS. Doing it is pretty straightforward. Go to https://ngrok.com/download and follow the provided instructions, except that you should use port `8000` while starting a HTTP tunnel. 

Like this:

```console
$ ./ngrok http 8000
```

It will provide you with nice HTTP and HTTPS URLs. Tale note of that.

Than, on another Terminal tab, start the built-in Laravel's web server:

```console
$ php artisan serve
```

In yet another tab, register your chatbot with Telegram:

```console
$ php artisan botman:telegram:register
```

You'll be asked to provide a *target URL*. It's the HTTPS URL generated by ngrok plus "/botman". Like this:

```
What is the target url for the telegram bot?:
 > https://<url-provided-by-ngrok>/botman
```

Now you should be able to find your Bot in Telegram, and it should be working fine.

**Via Heroku**

The original deployment of this bot, which is available at http://t.me/VF2DoomsdayBot, was deployed on Heroku, using PostgreSQL instead of SQLite. It is a free, reliable and extremely convenient way of hosting a small project like this one.

If you got here without too much difficulties, I'm betting you can Google your way into Heroku with no brain injuries.

Obs: if you go that way, note that I left a `Procfile` already done for you.

## Why didn't you provided a Dockerfile on this project?

It's on the roadmap. The main benefit would be a (probable) reduction on setup documentation and less steps to get the bot up and running. Actually the one candidate that provided a Dcokerfile for this coding homework ended up being hired.

## I'm stuck

Open an issue. I'll do my best to help you out.

## License

BotMan is free software distributed under the terms of the MIT license.

