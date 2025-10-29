#!/usr/bin/env bash
set -euo pipefail

# One-repo (monorepo) push
cd pc_zone_monorepo
git init
git add .
git commit -m "Initial commit: PC Zone monorepo (frontend + backend + SQL)"
# Create repo on GitHub first, then:
# git remote add origin https://github.com/<USER>/pc_zone.git
# git branch -M main
# git push -u origin main
