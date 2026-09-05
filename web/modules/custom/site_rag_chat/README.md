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

已经按控制台「API 调试」页面导出的真实调用示例、以及一次真实返回数据对接完成：

- **Endpoint**：形如 `https://ws-xxxxxx.cn-beijing.maas.aliyuncs.com/api/v2/apps/knowledge/chat`（每个业务空间的域名前缀不同，请填你自己控制台里看到的完整地址）
- **Agent ID**：在应用详情页顶部复制，形如 `aid-xxxxxxxxxxxx`
- **鉴权**：`Authorization: Bearer <API Key>`
- **请求体**：
  ```json
  {
    "input": { "messages": [ {"role": "user", "content": "用户问题"} ] },
    "parameters": { "agent_options": { "agent_id": "aid-xxx" } },
    "stream": true
  }
  ```
- **返回格式**：这个接口返回的是 **SSE 流**（一行行 `data:{...}`），不是单个 JSON。已经在 `ChatController::extractAnswer()` 里实现了完整解析：
  - 从 `role: "assistant"` 的事件里按顺序拼接出完整回答文字
  - 从 `role: "tool"` 的事件（语义检索结果）里提取参考来源。因为我们导出内容时在每个文件里写入了「原文链接：https://...」这行，解析时会正则提取出真实的 Drupal 文章 URL 作为参考链接，而不是用阿里云临时的 OSS 下载地址
  - Guzzle 用的是普通阻塞式请求（不是真正的长连接流式转发），会等整个流结束后一次性拿到完整文本再解析，所以前端体验是"转圈等待，然后一次性出现完整回答"，不是打字机效果。如果想要打字机效果，需要用 SSE 转发（更复杂），目前版本先不做

以上均已写入 `SettingsForm.php`（多了 Agent ID 一个配置项）和 `ChatController.php`，理论上现在配置好 Endpoint / API Key / Agent ID 就可以直接用。如果实际测试发现回答解析不对（比如某个 role 的字段名跟这次样例不一样），把返回内容发我再调整 `extractAnswer()`。

## 内容同步（让知识库跟着 Drupal 内容更新）

当前版本只负责「前端问答窗口 + 转发请求」。若要让知识库自动跟随 Drupal 内容更新（而不是手动去控制台导入网页），
可以再加一个 `hook_entity_insert` / `hook_entity_update` 的实现，把节点内容推送到 OpenSearch 的数据接口——如果需要这部分，告诉我，我再补一版。

## 安全提醒

- API Key 只存在服务端配置（`site_rag_chat.settings`），前端 JS 拿不到，问题都是先发到 Drupal 后端再由后端转发。
- 已内置基于 IP 的限流（默认 60 秒内最多 20 次），防止被刷调用额度，可在设置页调整。
- 建议正式上线前，先在测试环境用几个真实网站内容的问题验证一下回答准确性。
