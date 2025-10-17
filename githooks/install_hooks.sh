#!/bin/sh
set -e
HOOKS_DIR="$(pwd)/githooks"
GIT_HOOKS_DIR="$(pwd)/.git/hooks"
if [ ! -d "$GIT_HOOKS_DIR" ]; then
  echo ".git/hooks not found, are you in a git repo?" >&2
  exit 1
fi
for f in post-merge post-checkout; do
  cp "$HOOKS_DIR/$f" "$GIT_HOOKS_DIR/$f"
  chmod +x "$GIT_HOOKS_DIR/$f"
  echo "Installed $f";
done
echo "Hooks installed."
