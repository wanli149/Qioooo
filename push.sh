#!/bin/bash

# 设置颜色
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# 显示当前分支
echo -e "${YELLOW}当前分支:${NC}"
git branch | grep \* | cut -d ' ' -f2

# 添加所有更改
echo -e "${YELLOW}添加更改...${NC}"
git add .

# 获取提交信息
echo -e "${YELLOW}请输入提交信息:${NC}"
read commit_message

# 提交更改
echo -e "${YELLOW}提交更改...${NC}"
git commit -m "$commit_message"

# 推送到GitHub
echo -e "${YELLOW}推送到GitHub...${NC}"
if git push github main; then
    echo -e "${GREEN}GitHub推送成功!${NC}"
else
    echo -e "${RED}GitHub推送失败!${NC}"
    exit 1
fi

# 推送到Gitee
echo -e "${YELLOW}推送到Gitee...${NC}"
if git push gitee main; then
    echo -e "${GREEN}Gitee推送成功!${NC}"
else
    echo -e "${RED}Gitee推送失败!${NC}"
    exit 1
fi

echo -e "${GREEN}所有操作完成!${NC}" 