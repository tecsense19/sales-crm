<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Overdue Client Follow-ups</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            background-color: #f3f4f6;
            padding: 40px 20px;
            box-sizing: border-box;
        }
        .container {
            max-width: 680px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            padding: 32px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.025em;
        }
        .header p {
            margin: 8px 0 0 0;
            font-size: 14px;
            color: #cbd5e1;
        }
        .content {
            padding: 32px;
        }
        .intro {
            font-size: 15px;
            color: #374151;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .section-title {
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 0;
            margin-bottom: 16px;
            color: #dc2626;
        }
        .card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
            background-color: #ffffff;
            border-left: 4px solid #ef4444;
        }
        .client-info {
            margin-bottom: 16px;
        }
        .client-name {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 4px 0;
        }
        .client-meta {
            font-size: 13px;
            color: #6b7280;
            margin: 0;
        }
        .meta-item {
            display: inline-block;
            margin-right: 12px;
        }
        .grid {
            display: table;
            width: 100%;
            margin-bottom: 16px;
        }
        .grid-row {
            display: table-row;
        }
        .grid-cell {
            display: table-cell;
            padding: 6px 0;
            font-size: 13px;
            vertical-align: top;
        }
        .grid-label {
            font-weight: 600;
            color: #4b5563;
            width: 140px;
        }
        .grid-value {
            color: #1f2937;
        }
        .notes-box {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 12px;
            font-size: 13px;
            color: #4b5563;
            border: 1px solid #f3f4f6;
            margin-top: 10px;
        }
        .btn {
            display: inline-block;
            background-color: #dc2626;
            color: #ffffff !important;
            text-decoration: none;
            padding: 10px 18px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            margin-top: 12px;
            text-align: center;
        }
        .btn:hover {
            background-color: #b91c1c;
        }
        .footer {
            background-color: #f9fafb;
            padding: 24px 32px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
        }
        .footer p {
            margin: 4px 0;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            border-radius: 9999px;
        }
        .badge-overdue {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>Action Required: Overdue Follow-ups</h1>
                <p>Sales CRM • {{ now()->format('l, F j, Y') }}</p>
            </div>
            
            <div class="content">
                <p class="intro">
                    Hello, <br><br>
                    You have <strong>{{ $overdue->count() }}</strong> client follow-up tasks that are currently overdue. Please review and update the status of these clients as soon as possible.
                </p>

                <h2 class="section-title">
                    <span class="badge badge-overdue" style="margin-right: 8px; vertical-align: middle;">{{ $overdue->count() }}</span>
                    Overdue Clients
                </h2>
                
                @foreach($overdue as $client)
                    <div class="card">
                        <div class="client-info">
                            <h3 class="client-name">{{ $client->name }}</h3>
                            <p class="client-meta">
                                <span class="meta-item">📍 {{ $client->location ?: 'Global / Remote' }}</span>
                                <span class="meta-item">💻 {{ $client->technology ?: 'Tech Stack N/A' }}</span>
                            </p>
                        </div>
                        
                        <div class="grid">
                            <div class="grid-row">
                                <div class="grid-cell grid-label">Status</div>
                                <div class="grid-cell grid-value" style="color: #dc2626; font-weight: 600;">{{ $client->status }}</div>
                            </div>
                            <div class="grid-row">
                                <div class="grid-cell grid-label">Next Follow-up Was</div>
                                <div class="grid-cell grid-value" style="color: #dc2626; font-weight: 600;">
                                    {{ $client->next_followup_date ? $client->next_followup_date->format('M d, Y') : 'N/A' }} 
                                    ({{ $client->next_followup_date ? $client->next_followup_date->diffForHumans() : '' }})
                                </div>
                            </div>
                            <div class="grid-row">
                                <div class="grid-cell grid-label">Email</div>
                                <div class="grid-cell grid-value">{{ $client->email ?: 'N/A' }}</div>
                            </div>
                            <div class="grid-row">
                                <div class="grid-cell grid-label">Mobile / Phone</div>
                                <div class="grid-cell grid-value">{{ $client->mobile_no ?: 'N/A' }}</div>
                            </div>
                        </div>

                        @if($client->notes)
                            <div class="notes-box">
                                <strong>Latest Notes:</strong><br>
                                {{ Str::limit($client->notes, 160) }}
                            </div>
                        @endif

                        <a href="{{ url('/clients/' . $client->id) }}" class="btn">Update Client Profile</a>
                    </div>
                @endforeach
            </div>
            
            <div class="footer">
                <p>This is an automated reminder sent from your <strong>Sales CRM Dashboard</strong>.</p>
                <p>&copy; {{ date('Y') }} Sales CRM. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
