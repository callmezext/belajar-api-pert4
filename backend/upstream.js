const https = require('https');

const UPSTREAM_URL = 'https://router.enchant.id/v1';
const UPSTREAM_KEY = process.env.UPSTREAM_KEY || 'sk-523986b71009dd92-crh5ba-b7bec5b7';

function forwardChatCompletion(body, onChunk, onDone, onError) {
  const postData = JSON.stringify(body);
  const url = new URL(`${UPSTREAM_URL}/chat/completions`);

  const req = https.request(
    url,
    {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${UPSTREAM_KEY}`,
        'Content-Type': 'application/json',
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
      }
    },
    (res) => {
      let totalTokens = 0;
      let buffer = '';

      res.on('data', (chunk) => {
        onChunk(chunk);
        buffer += chunk.toString();
      });

      res.on('end', () => {
        // Estimate or extract token usage from chunks
        for (const line of buffer.split('\n')) {
          const trimmed = line.trim();
          if (trimmed.startsWith('data: ') && trimmed !== 'data: [DONE]') {
            try {
              const parsed = JSON.parse(trimmed.slice(6));
              if (parsed.usage && parsed.usage.total_tokens) {
                totalTokens = parsed.usage.total_tokens;
              }
            } catch (e) {}
          }
        }

        // If no explicit usage from upstream stream, estimate based on length
        if (totalTokens === 0) {
          const estimatedChars = buffer.length;
          totalTokens = Math.max(25, Math.ceil(estimatedChars / 4));
        }

        onDone({ totalTokens, statusCode: res.statusCode });
      });
    }
  );

  req.on('error', (err) => {
    onError(err);
  });

  req.write(postData);
  req.end();
}

module.exports = {
  forwardChatCompletion,
  UPSTREAM_URL
};
