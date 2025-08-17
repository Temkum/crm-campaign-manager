<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Page not found - 404</title>
    <style>
        :root {
            color-scheme: light dark;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Helvetica Neue, Arial, "Apple Color Emoji", "Segoe UI Emoji";
        }

        .wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: #f8fafc;
        }

        .card {
            width: 100%;
            max-width: 720px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .header {
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header .badge {
            background: #dbeafe;
            color: #1e3a8a;
            font-weight: 600;
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 999px;
        }

        .content {
            padding: 28px 24px;
        }

        h1 {
            margin: 0 0 8px;
            font-size: 24px;
            color: #0f172a;
        }

        p {
            margin: 0;
            color: #334155;
            line-height: 1.6;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 20px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            font-weight: 600;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #e2e8f0;
        }

        .btn:hover {
            filter: brightness(0.98);
        }

        .muted {
            font-size: 13px;
            color: #64748b;
            margin-top: 10px;
        }

        @media (prefers-color-scheme: dark) {
            .wrap {
                background: #0b1220;
            }

            .card {
                background: #0f172a;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
            }

            .header {
                border-color: #1f2937;
            }

            h1 {
                color: #e5e7eb;
            }

            p,
            .muted {
                color: #94a3b8;
            }

            .btn-secondary {
                background: #111827;
                color: #e5e7eb;
                border-color: #374151;
            }
        }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="card">
            <div class="header">
                <span class="badge">Error 404</span>
                <strong>Page not found</strong>
            </div>
            <div class="content">
                <h1>We can't find that page.</h1>
                <p>The page you are looking for might have been removed, had its name changed, or is temporarily
                    unavailable.</p>
                <div class="actions">
                    <button class="btn btn-secondary" onclick="goBack()">← Go back</button>
                    <a class="btn btn-primary" href="{{ route('dashboard') }}">🏠 Dashboard</a>
                </div>
                <p class="muted">If you believe this is an error, please contact support.</p>
            </div>
        </div>
    </div>
    <script>
        function goBack() {
      if (document.referrer && window.history.length > 1) {
        window.history.back();
      } else {
        window.location.href = '{{ route('dashboard') }}';
      }
    }
    </script>
</body>

</html>