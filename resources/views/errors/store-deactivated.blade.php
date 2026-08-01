<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Store Deactivated</title>
    <style>
        :root {
            color-scheme: light;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: Georgia, "Times New Roman", serif;
            background:
                radial-gradient(circle at top, rgba(245, 158, 11, 0.18), transparent 40%),
                linear-gradient(180deg, #fffaf0 0%, #f5efe4 100%);
            color: #1f1a14;
            padding: 1.5rem;
        }

        .card {
            width: min(100%, 36rem);
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid rgba(120, 80, 20, 0.12);
            border-radius: 1.25rem;
            padding: 2.25rem 2rem;
            box-shadow: 0 18px 50px rgba(70, 40, 0, 0.08);
            text-align: center;
        }

        .eyebrow {
            display: inline-block;
            margin-bottom: 0.85rem;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            background: rgba(220, 38, 38, 0.1);
            color: #b91c1c;
            font-family: ui-sans-serif, system-ui, sans-serif;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        h1 {
            margin: 0 0 0.85rem;
            font-size: clamp(1.6rem, 4vw, 2rem);
            line-height: 1.15;
        }

        p {
            margin: 0;
            font-family: ui-sans-serif, system-ui, sans-serif;
            font-size: 1rem;
            line-height: 1.6;
            color: #4b4034;
        }

        .actions {
            margin-top: 1.75rem;
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.15rem;
            border-radius: 0.75rem;
            font-family: ui-sans-serif, system-ui, sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
        }

        .primary {
            background: #b45309;
            color: #fff7ed;
        }

        .secondary {
            background: transparent;
            color: #7c2d12;
            border: 1px solid rgba(124, 45, 18, 0.25);
        }
    </style>
</head>
<body>
    <main class="card">
        <div class="eyebrow">Account suspended</div>
        <h1>Store deactivated</h1>
        <p>{{ $message }}</p>
        @if (! empty($store?->name))
            <p style="margin-top: 0.75rem;">Store: <strong>{{ $store->name }}</strong></p>
        @endif
        <div class="actions">
            <a class="primary" href="{{ url('/admin/login') }}">Back to login</a>
            <a class="secondary" href="mailto:support@example.com">Contact support</a>
        </div>
    </main>
</body>
</html>
