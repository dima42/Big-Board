# Big-Board

## Local requirements

- PHP 7.1
- MySQL
- [Composer](https://getcomposer.org/)

## Set up Slack

### Channels

Create a `#big-board` channel.

### Bots

Create two bots:

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
7. Click "Slash Commands". Create these seven commands. For each one, define the Request URL as `http://[YOURAPP].herokuapp.com/tobybot`. Include Descriptions and Usage Hints as desired.
    - `/board`
    - `/solve`
    - `/info`
    - `/workon`
    - `/note`
    - `/connect`

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

Define these locally and Heroku.  Locally: copy `envvars_example.config` to `envvars.config`, fill out the fields, and run `source envars.config`.  On Heroku: config variables are in the settings tab.

Some notes on figuring these out:

**Database configuration**

- `BIG_BOARD_DB_HOST`
- `BIG_BOARD_DB_NAME`
- `BIG_BOARD_DB_USERNAME`
- `BIG_BOARD_DB_PASSWORD`

On Heroku - there's a `CLEARDB_DATABASE_URL` config variable which is in the format `mysql://BIG_BOARD_DB_USERNAME:BIG_BOARD_DB_PASSWORD@BIG_BOARD_DB_HOST/BIG_BOARD_DB_NAME?reconnect=true`.

Locally - Run mysql and create a database and a user.  host is localhost, the rest is what you set while creating this.

**Google Drive configuration**

- `GOOGLE_CLIENT_ID` - you got this while setting up gooogle credentials earlier
- `GOOGLE_CLIENT_SECRET` - same
- `GOOGLE_DRIVE_ID` - go to google drive for your team folder, look at the url - it's the long id string there.  Similarly for `GOOGLE_DOCS_TEMPLATE_ID`

**Slack configuration**

- `SLACK_DOMAIN` - just the id, without any dots or the slack.com domain -- e.g. palindrome2018
- `TOBYBOT_SLACK_KEY` - OAuth Access token. Starts with `xoxa`.
- `TOBYBOT_VERIFICATION_TOKEN` - Verification Token, listed under Basic Information. 24 characters long.
- `BIGBOARDBOT_SLACK_KEY` - Bot User OAuth Access Token for a standard bot. Starts with `xoxb`.

**Other**

- `SIDEBAR_TEAM_INFO` - data that goes in Big Board sidebar.  Semicolon separated;  each semicolon starts a new line.

## Set up local app

Run:

```
composer install --ignore-platform-reqs
```

At this point, make sure all the installed libraries (which are at `vendor/bin/`) are on your PATH. Then run:

```
composer dump-autoload
propel config:convert
propel sql:insert
```

You can run the app locally with e.g. `php -S localhost:8888`

## Push to Heroku

To set up the DB on Heroku, first push, then run:

```
heroku run bash
propel sql:insert
```

Set up automatic deployments by connecting your Heroku instance to your GitHub repo.
