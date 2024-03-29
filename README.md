# Big-Board

## Local requirements

- PHP 7.1
- MySQL
- [Composer](https://getcomposer.org/)

## Set up Slack

### Channels

Create a `#big-board` channel.

### Slack Bot

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
    - `channels:manage`
    - `channels:join`
    - `chat:write.public`

7. Click "Slash Commands". Create these seven commands. For each one, define the Request URL as `http://[YOURAPP].herokuapp.com/tobybot`. Include Descriptions and Usage Hints as desired.
    - `/board`
    - `/solve`
    - `/info`
    - `/connect`

## Set up Google API project

1. [Go here](https://console.developers.google.com/apis/credentials)
2. Click "Create credentials". Choose "OAuth client ID".
3. Add Authorized JavaScript origins
    - `https://[YOURAPP].herokuapp.com`
4. Add Authorized redirect URIs:
    - `https://[YOURAPP].herokuapp.com`
    - `https://[YOURAPP].herokuapp.com/oauth`
5. Note your Client ID and Client secret.
6. Add the privacy policy URL (`http://your-domain/privacy`) to your [OAuth consent screen](https://console.cloud.google.com/apis/credentials).  Once your app is live, you'll need to go through the verification process [here](https://support.google.com/cloud/answer/7454865)) to get rid of the "unverified app" screen.
7. Create a separate credential for testing.  Add `http://localhost:8888` to Authorized JavaScript origins and `http://localhost:8888` and `http://localhost:8888/oauth` to Authorized Redirect URI's for that one
8. Click "Create credentials" again and choose "Service Account".  Download the JSON for login info here for later.
9. At https://console.developers.google.com/apis/dashboard?project=[your-project-name], enable the google drive api and the picker api
10. Enable auth/drive.file scope at https://console.developers.google.com/apis/credentials/consent/edit?project=[your-project-id]
11. Click "Create credentials" again and choose "API Key"

## Set up Heroku instance

Provision a MySQL add-on. I used ClearDB. Create a DB. Note your:

- URL
- DB name
- username
- password

Provision Heroku Redis.  `heroku addons:create heroku-redis:hobby-dev -a your-app-name`
Allow workers on Heroku. `heroku config:add LD_LIBRARY_PATH=/app/php/ext:/app/apache/lib`

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

- `GOOGLE_APP_ID` - the project number in the google cloud console
- `GOOGLE_CLIENT_ID` - you got this while setting up google credentials earlier
- `GOOGLE_CLIENT_SECRET` - same
- `GOOGLE_DEVELOPER_KEY` - the API key you got while setting up google credentials
- `GOOGLE_DRIVE_ID` - go to google drive for your team folder, look at the url - it's the long id string there.  Similarly for `GOOGLE_DOCS_TEMPLATE_ID`
- `GOOGLE_SERVICE_ACCOUNT_APPLICATION_CREDENTIALS` - this is the json for when you made the service account credential

**Slack configuration**

- `SLACK_DOMAIN` - just the id, without any dots or the slack.com domain -- e.g. palindrome2018
- `TOBYBOT_SLACK_KEY` - OAuth Access token. Starts with `xoxa`.
- `TOBYBOT_VERIFICATION_TOKEN` - Verification Token, listed under Basic Information. 24 characters long.
- `BIGBOARDBOT_SLACK_KEY` - Bot User OAuth Access Token for a standard bot. Starts with `xoxb`.

**Other**

- `SIDEBAR_TEAM_INFO` - data that goes in Big Board sidebar.  Semicolon separated;  each semicolon starts a new line.

## Set up local environment

Run:

```
composer install --ignore-platform-reqs
```

At this point, make sure all the installed libraries (which are at `vendor/bin/`) are on your PATH. Then run:

```
composer dump-autoload
propel config:convert
propel sql:insert
propel migrate --fake
```

You can run the app locally with e.g.

```
php -S localhost:8888
```

## Push to Heroku

To set up the DB on Heroku, first push, then run:

```
heroku run bash
propel sql:insert
```

Set up automatic deployments by connecting your Heroku instance to your GitHub repo.
