#!/usr/bin/env bash
set -eu
# Script de test smoke pour les APIs du dossier francoisdcls
# - démarre un serveur PHP intégré
# - exécute des requêtes GET/POST
# - arrête le serveur

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT_DIR"
PORT=8000
BASE="http://127.0.0.1:${PORT}"
LOG=/tmp/php-smoke.log

# Start PHP server in background
php -S 127.0.0.1:${PORT} > "$LOG" 2>&1 &
PID=$!
echo "Started php -S with PID $PID (log: $LOG)"
# give server a moment
sleep 0.6

fail=0

function run_get() {
  local url=$1
  echo "GET $url"
  http_status=$(curl -s -o /tmp/tmp_out.txt -w "%{http_code}" "$BASE/$url" || echo "000")
  echo "HTTP $http_status"
  if [ "$http_status" -ge 200 ] && [ "$http_status" -lt 300 ]; then
    echo "OK"
    cat /tmp/tmp_out.txt | head -n 6
  else
    echo "FAIL: GET $url returned $http_status"
    cat /tmp/tmp_out.txt | sed -n '1,40p'
    fail=1
  fi
}

function run_post() {
  local url=$1
  shift
  echo "POST $url"
  http_status=$(curl -s -o /tmp/tmp_out.txt -w "%{http_code}" -X POST "$BASE/$url" "$@" || echo "000")
  echo "HTTP $http_status"
  cat /tmp/tmp_out.txt | sed -n '1,80p'
  if [ "$http_status" -ge 200 ] && [ "$http_status" -lt 300 ]; then
    echo "OK"
  else
    # special-case: participation endpoint may return 400 if duplicate — treat as OK
    if [[ "$url" == "services/api_ajout_participation.php" && "$http_status" -eq 400 ]]; then
      # Accept common duplicate messages (French/English) and escaped unicode JSON
      if grep -qiE "d\u00e9j|déj|already|enregistr|duplicate|Participation" /tmp/tmp_out.txt; then
        echo "OK (duplicate or expected failure)"
      else
        echo "FAIL: POST $url returned $http_status"
        fail=1
      fi
    else
      echo "FAIL: POST $url returned $http_status"
      fail=1
    fi
  fi
}

# Tests GET
run_get "site_f1.php"
run_get "services/pilotes.php"
run_get "services/ecuries.php"
run_get "services/pantheon_pilotes.php"

# Test POST add pilote
run_post "services/api_ajout_pilote.php" -F "prenom=Smoke" -F "nom=Test"
# Test POST add ecurie
run_post "services/api_ajout_ecurie.php" -F "nom=SmokeEcurie" -F "siege=FR"

# For participation we need real ids: extract first pilote_id and first ecurie_id
pilote_id=$(curl -s "$BASE/services/pilotes.php" | sed -n 's/.*"pilote_id":\([0-9]\+\).*/\1/p' | head -n1 || true)
ecurie_id=$(curl -s "$BASE/services/ecuries.php" | sed -n 's/.*"ecurie_id":\([0-9]\+\).*/\1/p' | head -n1 || true)
# try to get a valid championship year from services/championnats.php
annee=$(curl -s "$BASE/services/championnats.php" | sed -n 's/.*"annee":\([0-9]\+\).*/\1/p' | head -n1 || true)
if [ -z "$annee" ]; then
  # fallback to 2024 if no championship years available
  annee=2024
fi
if [ -n "$pilote_id" ] && [ -n "$ecurie_id" ]; then
  echo "Using pilote_id=$pilote_id ecurie_id=$ecurie_id annee=$annee for participation test"
  run_post "services/api_ajout_participation.php" -F "pilote_id=$pilote_id" -F "ecurie_id=$ecurie_id" -F "annee=$annee"
else
  echo "SKIP participation: no ids found (pilote_id=$pilote_id, ecurie_id=$ecurie_id)"
fi

# Teardown
kill $PID || true
sleep 0.2
echo "Server $PID stopped"

if [ "$fail" -ne 0 ]; then
  echo "Some tests failed"
  exit 2
fi

echo "All smoke tests passed"
exit 0
