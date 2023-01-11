from slack_sdk import WebClient
import time

client = WebClient(token="")

cursor = None
while True:
    conversations = client.conversations_list(cursor=cursor)
    channels = conversations['channels']
    for channel in channels:
        if channel['name'][0] == 'ρ' and not channel['is_archived']:
            print("found {}".format(channel['name']))
            print(channel["id"])
            client.conversations_archive(channel=channel["id"])
            time.sleep(10)
    cursor = conversations['response_metadata']['next_cursor']
