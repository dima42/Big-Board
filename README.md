# Big-Board

## Local requirements

- PHP 7+
- MySQL
- Composer

## Set up Slack

- Make a `#big-board` channel.

### Bots

You'll need to create two bots:

### 1. *Big Board Bot*.

1. Go to https://api.slack.com/apps
2. Click *Create New App*.
3. _Don't_ click the "Interested in the next generation of apps?" link.
4.  Name it whatever you'd like and choose your team's workspace.
5.  Under Bot User, name it whatever you'd like.
6.  Under OAuth & Permissions > Scopes, add `bot` permissions.

### 2. *TobyBot*.

1. Go to https://api.slack.com/apps
2. Click *Create New App*.
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

