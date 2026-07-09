#!/usr/bin/env bash
set -euo pipefail

# Configuration — customize these
API_URL="http://localhost"
API_TOKEN="YOUR_API_TOKEN"
KID_ID="1"

# Fetch game list
GAMES=$(curl -sf "$API_URL/api/games" \
  -H "Authorization: Bearer $API_TOKEN" \
  -H "Accept: application/json") || { echo "Failed to fetch games"; exit 1; }

# Parse each game
echo "$GAMES" | jq -c '.[]' | while read -r GAME; do
  NAME=$(echo "$GAME" | jq -r '.name')
  PROCESSES=$(echo "$GAME" | jq -r '.processes[]')

  FOUND=0
  while IFS= read -r PROC; do
    if pgrep -x "$PROC" >/dev/null 2>&1; then
      echo "[$NAME] Process running: $PROC"
      FOUND=1
    fi
  done <<< "$PROCESSES"

  if [ "$FOUND" -eq 1 ]; then
    curl -sf -X POST "$API_URL/api/log/create" \
      -H "Authorization: Bearer $API_TOKEN" \
      -H "Content-Type: application/json" \
      -H "Accept: application/json" \
      -d "$(jq -n --arg kid "$KID_ID" --arg msg "Game running: $NAME" '{kid_id: ($kid | tonumber), message: $msg}')" \
      >/dev/null && echo "[$NAME] Logged"
  fi
done
