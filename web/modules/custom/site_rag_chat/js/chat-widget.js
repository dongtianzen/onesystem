/**
 * @file
 * Site RAG Chat 悬浮问答窗口。
 */
(function (Drupal, drupalSettings, once) {
  'use strict';

  Drupal.behaviors.siteRagChat = {
    attach: function (context, settings) {
      once('site-rag-chat', '#site-rag-chat-root', context).forEach(function (root) {
        initWidget(root, settings.siteRagChat || {});
      });
    },
  };

  function initWidget(root, config) {
    var endpoint = config.endpoint;
    var title = config.title || '智能助手';
    var welcomeMessage = config.welcomeMessage || '';
    var maxLength = config.maxLength || 300;

    // 简单的会话 ID，用于后端可选的多轮上下文（如目标 API 支持）。
    var sessionId = 'sr-' + Date.now() + '-' + Math.random().toString(36).slice(2, 8);
    var isOpen = false;
    var isSending = false;

    // --- 构建 DOM ---
    var toggleBtn = document.createElement('button');
    toggleBtn.type = 'button';
    toggleBtn.className = 'site-rag-chat-toggle';
    toggleBtn.setAttribute('aria-label', title);
    toggleBtn.innerHTML = '<span class="site-rag-chat-toggle-icon">💬</span>';

    var panel = document.createElement('div');
    panel.className = 'site-rag-chat-panel';
    panel.setAttribute('role', 'dialog');
    panel.setAttribute('aria-label', title);
    panel.hidden = true;

    panel.innerHTML =
      '<div class="site-rag-chat-header">' +
        '<span class="site-rag-chat-header-title"></span>' +
        '<button type="button" class="site-rag-chat-close" aria-label="关闭">&times;</button>' +
      '</div>' +
      '<div class="site-rag-chat-messages"></div>' +
      '<form class="site-rag-chat-form">' +
        '<textarea class="site-rag-chat-input" rows="1" placeholder="输入你的问题…"></textarea>' +
        '<button type="submit" class="site-rag-chat-send">发送</button>' +
      '</form>';

    panel.querySelector('.site-rag-chat-header-title').textContent = title;

    root.appendChild(toggleBtn);
    root.appendChild(panel);

    var messagesEl = panel.querySelector('.site-rag-chat-messages');
    var formEl = panel.querySelector('.site-rag-chat-form');
    var inputEl = panel.querySelector('.site-rag-chat-input');
    var closeBtn = panel.querySelector('.site-rag-chat-close');

    if (welcomeMessage) {
      appendMessage('bot', welcomeMessage);
    }

    // --- 事件绑定 ---
    toggleBtn.addEventListener('click', function () {
      isOpen = !isOpen;
      panel.hidden = !isOpen;
      if (isOpen) {
        inputEl.focus();
      }
    });

    closeBtn.addEventListener('click', function () {
      isOpen = false;
      panel.hidden = true;
    });

    formEl.addEventListener('submit', function (e) {
      e.preventDefault();
      sendQuestion();
    });

    inputEl.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendQuestion();
      }
    });

    // --- 核心逻辑 ---
    function sendQuestion() {
      var question = inputEl.value.trim();
      if (!question || isSending) {
        return;
      }
      if (question.length > maxLength) {
        appendMessage('bot', '问题过长，请控制在 ' + maxLength + ' 字以内。');
        return;
      }

      appendMessage('user', question);
      inputEl.value = '';
      isSending = true;
      var typingEl = appendMessage('bot', '正在思考…', true);

      fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ question: question, session_id: sessionId }),
      })
        .then(function (response) {
          return response.json().then(function (data) {
            return { ok: response.ok, data: data };
          });
        })
        .then(function (result) {
          typingEl.remove();
          if (!result.ok) {
            appendMessage('bot', result.data.error || '出错了，请稍后再试。');
            return;
          }
          appendMessage('bot', result.data.answer || '（没有获取到回答）');
          renderReferences(result.data.references);
        })
        .catch(function () {
          typingEl.remove();
          appendMessage('bot', '网络异常，请稍后再试。');
        })
        .finally(function () {
          isSending = false;
        });
    }

    function appendMessage(role, text, isTyping) {
      var wrap = document.createElement('div');
      wrap.className = 'site-rag-chat-message site-rag-chat-message--' + role + (isTyping ? ' site-rag-chat-message--typing' : '');
      wrap.textContent = text;
      messagesEl.appendChild(wrap);
      messagesEl.scrollTop = messagesEl.scrollHeight;
      return wrap;
    }

    function renderReferences(references) {
      if (!references || !references.length) {
        return;
      }
      var wrap = document.createElement('div');
      wrap.className = 'site-rag-chat-message site-rag-chat-message--bot site-rag-chat-references';

      var label = document.createElement('div');
      label.className = 'site-rag-chat-references-label';
      label.textContent = '参考来源：';
      wrap.appendChild(label);

      references.forEach(function (ref) {
        var url = typeof ref === 'string' ? ref : (ref.url || ref.link || '');
        var text = typeof ref === 'string' ? ref : (ref.title || ref.name || url);
        if (!url) {
          return;
        }
        var a = document.createElement('a');
        a.href = url;
        a.target = '_blank';
        a.rel = 'noopener noreferrer';
        a.textContent = text;
        wrap.appendChild(a);
      });

      messagesEl.appendChild(wrap);
      messagesEl.scrollTop = messagesEl.scrollHeight;
    }
  }
})(Drupal, drupalSettings, once);
