bash get_puzzles_curl.sh | jq '.[] | .Id' | xargs -L 1 bash del_curl.sh
