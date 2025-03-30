










class SynapseAI {
  constructor() {
    this.elements = {
      askButton: document.getElementById('askAI'),
      promptInput: document.getElementById('aiPrompt'),
      fileUpload: document.getElementById('fileUpload'),
      uploadLabel: document.getElementById('uploadLabel'),
      responseContainer: document.getElementById('aiResponse'),
      statusIndicator: document.getElementById('apiStatus'),
      rateLimitDisplay: document.getElementById('rateLimitDisplay')
    };

    if (!this.elements.askButton || !this.elements.promptInput || !this.elements.responseContainer) {
      console.error('Missing required elements');
      return;
    }

    this.state = {
      retryCount: 0,
      maxRetries: 3,
      baseRetryDelay: 1000,
      isOnline: false,
      lastRequestTime: null,
      activeRequests: 0,
      loadingInterval: null
    };

    this.initEventListeners();
    this.setupInactivityMonitor();
    this.checkAPIStatus();
  }

  initEventListeners() {
    this.elements.askButton.addEventListener('click', () => this.handleQuery());
    this.elements.promptInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        this.handleQuery();
      }
    });

    if (this.elements.fileUpload) {
      this.elements.fileUpload.addEventListener('change', (e) => this.handleFileUpload(e));
    }

    if (this.elements.uploadLabel) {
      this.elements.uploadLabel.addEventListener('dragover', (e) => {
        e.preventDefault();
        this.elements.uploadLabel.classList.add('dragover');
      });
      this.elements.uploadLabel.addEventListener('dragleave', () => {
        this.elements.uploadLabel.classList.remove('dragover');
      });
      this.elements.uploadLabel.addEventListener('drop', (e) => {
        e.preventDefault();
        this.elements.uploadLabel.classList.remove('dragover');
        if (e.dataTransfer.files.length > 0) {
          this.handleFileUpload({ target: { files: e.dataTransfer.files } });
        }
      });
    }
  }

  async checkAPIStatus() {
    try {
      const response = await this.fetchWithTimeout('healthcheck.php', { method: 'GET' }, 5000);
      if (!response.ok) throw new Error('Service unavailable');
      const data = await response.json();
      this.updateStatus(data.status === 'OK');
    } catch (error) {
      this.updateStatus(false);
    }
  }

  updateStatus(isOnline) {
    this.state.isOnline = isOnline;
    if (this.elements.statusIndicator) {
      this.elements.statusIndicator.textContent = isOnline ? '🟢 Online' : '🔴 Offline';
      this.elements.statusIndicator.style.color = isOnline ? '#00F5E9' : '#ff6b6b';
    }
  }

  async handleQuery() {
    const prompt = this.elements.promptInput.value.trim();
    const file = this.elements.fileUpload?.files[0];
    
    if (!prompt && !file) {
      this.showError('Please enter a question or upload a file');
      return;
    }

    this.state.activeRequests++;
    this.showLoading();
    this.elements.askButton.disabled = true;

    try {
      const formData = new FormData();
      if (prompt) formData.append('prompt', prompt);
      if (file) formData.append('file', file);

      const response = await this.makeRequestWithRetry({
        url: 'ai-handler.php',
        method: 'POST',
        body: formData
      });

      if (response) {
        this.showResponse(response);
        this.state.retryCount = 0;
      }
    } catch (error) {
      this.showError(error.message);
      this.state.retryCount++;
    } finally {
      this.state.activeRequests--;
      this.elements.askButton.disabled = false;
    }
  }

  async makeRequestWithRetry(requestConfig, currentRetry = 0) {
    try {
      const response = await this.fetchWithTimeout(
        requestConfig.url,
        {
          method: requestConfig.method,
          body: requestConfig.body
        },
        30000
      );

      if (response.status === 429) {
        const retryAfter = parseInt(response.headers.get('Retry-After')) || 5;
        this.showRateLimitWarning(retryAfter);
        await this.delay(retryAfter * 1000);
        return this.makeRequestWithRetry(requestConfig, currentRetry + 1);
      }

      if (!response.ok) {
        const errorData = await response.json().catch(() => ({}));
        throw new Error(errorData.error || `Request failed with status ${response.status}`);
      }

      return await response.json();
    } catch (error) {
      if (currentRetry < this.state.maxRetries) {
        const delay = this.calculateBackoffDelay(currentRetry);
        await this.delay(delay);
        return this.makeRequestWithRetry(requestConfig, currentRetry + 1);
      }
      throw error;
    }
  }

  calculateBackoffDelay(retryCount) {
    const baseDelay = Math.min(this.state.baseRetryDelay * Math.pow(2, retryCount), 30000);
    return baseDelay * (0.8 + Math.random() * 0.4);
  }

  delay(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
  }

  async fetchWithTimeout(resource, options = {}, timeout = 10000) {
    const controller = new AbortController();
    const id = setTimeout(() => controller.abort(), timeout);
    const response = await fetch(resource, {
      ...options,
      signal: controller.signal
    });
    clearTimeout(id);
    return response;
  }

  showLoading() {
    if (this.state.loadingInterval) clearInterval(this.state.loadingInterval);
    
    this.elements.responseContainer.innerHTML = `
      <div class="loading-state">
        <div class="loading-spinner"></div>
        <p>Processing request...</p>
      </div>
    `;
  }

  showResponse(data) {
    if (this.state.loadingInterval) clearInterval(this.state.loadingInterval);
    
    this.elements.responseContainer.innerHTML = `
      <div class="ai-response">
        <div class="response-content">
          ${this.formatResponse(data.response)}
        </div>
      </div>
    `;
  }

  showError(message) {
    if (this.state.loadingInterval) clearInterval(this.state.loadingInterval);
    
    this.elements.responseContainer.innerHTML = `
      <div class="error-state">
        <p>${message}</p>
        <button class="retry-btn">Try Again</button>
      </div>
    `;
    
    document.querySelector('.retry-btn').addEventListener('click', () => this.handleQuery());
  }

  showRateLimitWarning(retryAfter) {
    if (!this.elements.rateLimitDisplay) return;
    
    this.elements.rateLimitDisplay.textContent = `Please wait ${retryAfter} seconds`;
    this.elements.rateLimitDisplay.style.display = 'block';
    
    setTimeout(() => {
      this.elements.rateLimitDisplay.style.display = 'none';
    }, retryAfter * 1000);
  }

  formatResponse(text) {
    return text
      .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
      .replace(/\*(.*?)\*/g, '<em>$1</em>')
      .replace(/`(.*?)`/g, '<code>$1</code>')
      .replace(/\n/g, '<br>');
  }

  handleFileUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    if (this.elements.uploadLabel) {
      this.elements.uploadLabel.querySelector('.file-name').textContent = file.name;
      this.elements.uploadLabel.classList.add('has-file');
    }
  }

  setupInactivityMonitor() {
    let timeout;
    const resetTimer = () => {
      clearTimeout(timeout);
      timeout = setTimeout(() => this.checkAPIStatus(), 30000);
    };
    ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(
      event => document.addEventListener(event, resetTimer)
    );
    resetTimer();
  }
}

document.addEventListener('DOMContentLoaded', () => {
  try {
    new SynapseAI();
  } catch (error) {
    console.error('Initialization failed:', error);
    document.body.innerHTML = `
      <div class="error-message">
        <p>Application failed to load. Please refresh.</p>
      </div>
    `;
  }
});