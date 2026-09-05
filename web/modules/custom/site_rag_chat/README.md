# Site RAG Chat

基于阿里云 OpenSearch LLM 智能问答版（或其他兼容 REST API 的 RAG 服务）的站内智能问答悬浮窗。

## 安装

1. 把 `site_rag_chat` 整个文件夹放到 `web/modules/custom/` 下。
2. 后台「扩展」里启用 **Site RAG Chat** 模块（或命令行 `drush en site_rag_chat -y`）。
3. 进入 **配置 > 服务 > Site RAG Chat 设置**（`/admin/config/services/site-rag-chat`），填写：
   - **API Endpoint**：你的 OpenSearch LLM 智能问答版实例的问答接口地址（控制台「接入信息」里能找到）
   - **API Key**：你的密钥
   - 限流、最大问题长度、聊天窗口标题、欢迎语按需调整
4. 进入 **结构 > 区块布局**，把 **站内智能问答窗口** 这个 Block 放到「页面底部」或任意区域，通常整站生效即可（不需要挑页面）。
5. 保存后前台右下角会出现悬浮问答按钮。

## 权限

- **使用站内智能问答窗口**（`access site rag chat`）：需要开放给「匿名用户」角色，访客才能用。
- **管理站内智能问答设置**（`administer site rag chat`）：只给管理员角色。

记得去 **人员 > 权限** 页面把这两个权限勾给对应角色，模块默认不会自动开放。

## 关于 API 对接细节

因为你的具体实例还没建好，`src/Controller/ChatController.php` 里的请求体字段名（`query`）和鉴权 header（`Authorization: Bearer`）先按常见写法预留了，等你拿到实例的真实 API 文档 / Postman 测试结果后，可能需要调整：

- `callRagApi()` 方法里的请求体字段（比如可能是 `question` 而不是 `query`）
- 鉴权方式（Bearer Token / 自定义 Header / 阿里云 AK-SK 签名，三选一，如果是 AK-SK 签名会复杂一些，需要额外引入签名逻辑）
- `extractAnswer()` 方法里从返回 JSON 提取回答文本的字段路径

建议先在阿里云控制台的「问答测试」页面调通，然后用浏览器开发者工具或 Postman 抓一次真实的请求/响应，对照着改这两个方法就行——如果拿到实际接口文档，可以发给我，我按实际结构再调整代码。

## 内容同步（让知识库跟着 Drupal 内容更新）

当前版本只负责「前端问答窗口 + 转发请求」。若要让知识库自动跟随 Drupal 内容更新（而不是手动去控制台导入网页），
可以再加一个 `hook_entity_insert` / `hook_entity_update` 的实现，把节点内容推送到 OpenSearch 的数据接口——如果需要这部分，告诉我，我再补一版。

## 安全提醒

- API Key 只存在服务端配置（`site_rag_chat.settings`），前端 JS 拿不到，问题都是先发到 Drupal 后端再由后端转发。
- 已内置基于 IP 的限流（默认 60 秒内最多 20 次），防止被刷调用额度，可在设置页调整。
- 建议正式上线前，先在测试环境用几个真实网站内容的问题验证一下回答准确性。
