Oneband 网站智能问答服务 - 使用说明

基于阿里云百炼（Model Studio）知识库构建的站内智能问答服务，让访客能针对 Oneband 网站内容提问并获得基于真实网页内容的回答。

服务概览
项目  内容
平台  阿里云百炼（Bailian / Model Studio）
知识库名称 onebandsystem
知识库类型 文档搜索 / 标准版
应用类型  知识问答（RAG）
应用 ID aid-e89b7045ed1a4fe8b5c9c27fc75bdd26
使用模型  qwen3.6-plus
向量模型  text-embedding-v4
向量存储  平台存储（免费）
内容来源  Drupal 网站 article / page 内容类型，仅中文（zh-hans）版本
一、在控制台里直接测试问答

不用接入 Drupal，也可以先在百炼控制台直接测试效果：

登录 https://bailian.console.aliyun.com
左侧菜单点 知识库，进入 onebandsystem 知识库
或者顶部导航点 应用 → 应用管理，找到 onebandsystem_... 这个知识问答应用
在对话框里直接输入关于网站内容的问题，比如：
"LiveU 有哪些产品？"
"LU200 传输器是什么？"
"Oneband 是做什么的？"
观察回答是否准确、是否基于真实文章内容、有没有编造信息（幻觉）


https://bailian.console.aliyun.com/cn-beijing?tab=app#/knowledge-base/list?activeKey=qa


二、更新知识库内容

网站内容更新后，知识库不会自动同步，需要手动重新导出、上传：

1. 导出最新内容

在项目根目录（跟 composer.json 同级）执行：

bash
# 本地 DDEV 环境
ddev drush scr modules/custom/site_rag_chat/scripts/export-content-for-rag.php

# 生产服务器
vendor/drush/drush/drush scr modules/custom/site_rag_chat/scripts/export-content-for-rag.php

导出结果会生成在：

web/sites/default/files/rag-export/       ← 逐篇 .txt 文件
web/sites/default/files/rag-export.zip    ← 打包文件

⚠️ 这是公开可访问目录，下载完成后务必删除：

bash
rm -rf web/sites/default/files/rag-export
rm -f web/sites/default/files/rag-export.zip
2. 上传到知识库
进入百炼控制台 → 知识库 → onebandsystem
点右上角「上传数据」，选择「文件」连接器
选择导出的 .txt 文件批量上传（一次最多 50 个，超过分批传）
等待文档状态变为「解析完成」（绿色勾）即可生效
3. 重新测试

回到「一、在控制台里直接测试问答」重新验证效果。

三、通过 API 调用（供 Drupal 站内问答窗口使用）

Drupal 网站前台的悬浮聊天窗口，通过后台的 site_rag_chat 模块调用这个 API 完成问答。

应用 API 详情：进入应用详情页 → 调用方式 → API
API Key：控制台「API KEY」页面创建/查看（密钥仅服务端保存，不对外暴露）
API 调试：控制台「API 调试」页面可以看到完整的请求示例（Endpoint、请求体格式、返回结构）

Drupal 后台配置路径：配置 → 服务 → Site RAG Chat 设置（/admin/config/services/site-rag-chat），需要填入：

API Endpoint
API Key

具体的接口字段格式，请参考 site_rag_chat 模块 README 中「关于 API 对接细节」章节，对照控制台「API 调试」页面的真实返回结构做核对调整。
