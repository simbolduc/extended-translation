#!/usr/bin/env bash
set -euo pipefail

rsync -av --delete --exclude='.git' ./ /home/simbolduc/extended-translation/