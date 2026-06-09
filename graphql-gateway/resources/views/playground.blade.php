<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Retobluto GraphQL Playground</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f6f7fb;
            color: #222;
        }

        header {
            background: #111827;
            color: white;
            padding: 18px 28px;
        }

        main {
            padding: 24px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        textarea {
            width: 100%;
            height: 420px;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-family: Consolas, monospace;
            font-size: 14px;
            box-sizing: border-box;
        }

        pre {
            width: 100%;
            height: 420px;
            overflow: auto;
            background: #0f172a;
            color: #d1fae5;
            padding: 14px;
            border-radius: 10px;
            box-sizing: border-box;
        }

        button {
            margin-top: 12px;
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            background: #2563eb;
            color: white;
            cursor: pointer;
        }

        input {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-sizing: border-box;
            margin-bottom: 10px;
        }

        .panel {
            background: white;
            padding: 18px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
        }
    </style>
</head>
<body>
<header>
    <h2>Retobluto GraphQL Gateway</h2>
    <p>Manual GraphQL Gateway berbasis Laravel Backend Framework.</p>
</header>

<main>
    <section class="panel">
        <label>Authorization Bearer Token Optional</label>
        <input id="token" placeholder="Bearer token admin/member">

        <label>GraphQL Query</label>
        <textarea id="query">query {
  health {
    auth {
      ok
      status
      message
    }
    field {
      ok
      status
      message
    }
  }
}</textarea>

        <button onclick="runQuery()">Run Query</button>
    </section>

    <section class="panel">
        <label>Response</label>
        <pre id="response">{}</pre>
    </section>
</main>

<script>
async function runQuery() {
    const query = document.getElementById('query').value;
    const token = document.getElementById('token').value.trim();
    const responseBox = document.getElementById('response');

    responseBox.textContent = 'Loading...';

    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    };

    if (token) {
        headers['Authorization'] = token.startsWith('Bearer ')
            ? token
            : `Bearer ${token}`;
    }

    try {
        const response = await fetch('/api/graphql', {
            method: 'POST',
            headers,
            body: JSON.stringify({ query })
        });

        const data = await response.json();
        responseBox.textContent = JSON.stringify(data, null, 2);
    } catch (error) {
        responseBox.textContent = JSON.stringify({
            error: error.message
        }, null, 2);
    }
}
</script>
</body>
</html>