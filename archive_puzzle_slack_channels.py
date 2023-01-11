from slack_sdk import WebClient
import time

client = WebClient(token="xoxp-862551652951-849224379555-1497255024083-52a5c1395e300bcefb0e44bfb1fb190b")

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
