# Big-Board

## Local requirements

- PHP 7+
- MySQL
- [Composer](https://getcomposer.org/)

## Set up Slack

### Channels

- Make a `#big-board` channel.

### Bots

You'll need to create two bots

#### Big Board Bot

1. Go to https://api.slack.com/apps
2. Click **Create New App**.
3. _Don't_ click the "Interested in the next generation of apps?" link.
4.  Name it whatever you'd like and choose your team's workspace.
5.  Under Bot User, name it whatever you'd like.
6.  Under OAuth & Permissions > Scopes, add `bot` permissions.

#### TobyBot

1. Go to https://api.slack.com/apps
2. Click **Create New App**.
3. _Do_ click the "Interested in the next generation of apps?" link. This will take you to the Developer Previvew page.
4. Click "Create a developer preview app".
5. Name it TobyBot and choose your team's workspace.
6. Allow the following permission scopes:
    - `commands`
    - `channels:history`
    - `channels:read`
    - `channels:write`
    - `chat:write:user`
    - `emoji:read`
    - `pins:write`
    - `users.profile:read`
    - `users.profile:write`
    - `users:read`
    - `users:write`

## Set up Google API project

1. [Go here](https://console.developers.google.com/apis/credentials)
2. Click "Create credentials". Choose "OAuth client ID".
3. Add Authorized JavaScript origins
    - `http://localhost:8888`
    - `http://[YOURAPP].herokuapp.com`
4. Add Authorized redirect URIs:
    - `http://localhost:8888`
    - `http://localhost:8888/oauth`
    - `http://[YOURAPP].herokuapp.com`
    - `http://[YOURAPP].herokuapp.com/oauth`
5. Note your Client ID and Client secret.

## Set up Heroku instance

Provision a MySQL add-on. I used ClearDB. Create a DB. Note your:

- URL
- DB name
- username
- password

## Config variables

Define these locally and Heroku.

*Database configuration*

- `BIG_BOARD_DB_HOST`
- `BIG_BOARD_DB_NAME`
- `BIG_BOARD_DB_USERNAME`
- `BIG_BOARD_DB_PASSWORD`

*Google Drive configuration*

- `GOOGLE_CLIENT_SECRET`

*Slack configuration*

- `TOBYBOT_SLACK_KEY` - OAuth Acces token. Starts with `xoxa`.
- `TOBYBOT_VERIFICATION_TOKEN` - Verification Token, listed under Basic Information. 24 characters long.
- `BIGBOARDBOT_SLACK_KEY` - Bot User OAuth Access Token for a standard bot. Starts with `xoxb`.

## Set up local app

Run:

`composer update --ignore-platform-reqs`

At this point, make sure all the installed libraries (which are probably at `vendor/bin/`) are on your PATH. Then run:

```
composer dump-autoload
propel config:convert
propel sql:insert
```

## Push to Heroku

To set up the DB on Heroku, first push, then run:

```
heroku run bash
propel sql:insert
```
