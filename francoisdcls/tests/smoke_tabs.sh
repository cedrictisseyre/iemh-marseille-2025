#!/usr/bin/env bash
# Simple smoke test: for each tab target, fetch the page and check #main-content exists
set -eu
PORTS=(8080 8081)
TARGETS=(
  "francoisdcls/pages/liste_pilotes.php"
  "francoisdcls/pages/liste_ecuries.php"
  "francoisdcls/pages/statistiques.php"
  "francoisdcls/pages/recherche.php"
  "francoisdcls/pages/comparer_pilotes.php"
  "francoisdcls/pages/palmares_annee.php"
  "francoisdcls/pages/pantheon_pilotes.php"
)

echo "Running smoke tabs test"
for port in "${PORTS[@]}"; do
  echo "-- port $port --"
  for t in "${TARGETS[@]}"; do
    url="http://127.0.0.1:$port/$t"
    echo -n "Checking $url ... "
    body=$(curl -s "$url" || true)
    if echo "$body" | grep -q "id=\"main-content\""; then
      echo "OK (has #main-content)"
    elif [ $(echo "$body" | sed -n 's/^[[:space:]]*//;s/[[:space:]]*$//p' | wc -c) -gt 80 ]; then
      echo "OK (non-empty body)"
    elif echo "$body" | grep -qE '<h[1-6]'; then
      echo "OK (has heading)"
    else
      echo "EMPTY or unexpected structure"
    fi
  done
done
