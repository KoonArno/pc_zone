@echo off
setlocal enabledelayedexpansion
cd /d %~dp0\pc_zone_monorepo
git init
git add .
git commit -m "Initial commit: PC Zone monorepo (frontend + backend + SQL)"
echo Created local git repo. Now run:
echo   git remote add origin https://github.com/<USER>/pc_zone.git
echo   git branch -M main
echo   git push -u origin main
