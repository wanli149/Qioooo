@echo off
chcp 65001 > nul

:: Show current branch
echo Current branch:
git branch | findstr "*"

:: Add all changes
echo Adding changes...
git add .

:: Get commit message
set /p commit_message=Enter commit message:

:: Commit changes
echo Committing changes...
git commit -m "%commit_message%"

:: Push to GitHub
echo Pushing to GitHub...
git push github main
if errorlevel 1 (
    echo GitHub push failed!
    exit /b 1
) else (
    echo GitHub push successful!
)

:: Push to Gitee
echo Pushing to Gitee...
git push gitee main
if errorlevel 1 (
    echo Gitee push failed!
    exit /b 1
) else (
    echo Gitee push successful!
)

echo All operations completed!
pause 