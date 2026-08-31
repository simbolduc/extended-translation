#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
NAME="$(basename "$ROOT")"
OUTPUT="${ROOT}/${NAME}.zip"

cd "$ROOT"
rm -f "$OUTPUT"

zip -r "$OUTPUT" . \
  -x ".git/*" \
  -x ".git/**/*" \
  -x "./.git/*" \
  -x "./.git/**/*" \
  -x ".cursor/*" \
  -x ".cursor/**/*" \
  -x "./.cursor/*" \
  -x "./.cursor/**/*" \
  -x "${NAME}.zip"

echo "Created ${OUTPUT}"
