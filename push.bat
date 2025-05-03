@echo off
chcp 65001 > nul

:: 显示当前分支
echo 当前分支:
git branch | findstr "*"

:: 添加所有更改
echo 添加更改...
git add .

:: 获取提交信息
set /p commit_message=请输入提交信息:

:: 提交更改
echo 提交更改...
git commit -m "%commit_message%"

:: 推送到GitHub
echo 推送到GitHub...
git push github main
if errorlevel 1 (
    echo GitHub推送失败!
    exit /b 1
) else (
    echo GitHub推送成功!
)

:: 推送到Gitee
echo 推送到Gitee...
git push gitee main
if errorlevel 1 (
    echo Gitee推送失败!
    exit /b 1
) else (
    echo Gitee推送成功!
)

echo 所有操作完成!
pause 