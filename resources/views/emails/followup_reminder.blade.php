<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Client Follow-up Digest</title>
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
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
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
            color: #94a3b8;
        }
        .content {
            padding: 32px;
        }
        .section-title {
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 0;
            margin-bottom: 16px;
        }
        .section-title.today {
            color: #d97706;
        }
        .section-title.overdue {
            color: #dc2626;
        }
        .card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
            background-color: #ffffff;
        }
        .card.today-card {
            border-left: 4px solid #f59e0b;
        }
        .card.overdue-card {
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
            background-color: #2563eb;
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
            background-color: #1d4ed8;
        }
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 32px 0;
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
        .badge-today {
            background-color: #fef3c7;
            color: #92400e;
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
                <h1>Follow-up Reminders</h1>
                <p>TailAdmin CRM • {{ now()->format('l, F j, Y') }}</p>
            </div>
            
            <div class="content">
                @if($upcoming->isEmpty() && $overdue->isEmpty())
                    <div style="text-align: center; padding: 40px 0;">
                        <h2 style="color: #1f2937; margin: 0 0 8px 0; font-size: 20px;">All Caught Up!</h2>
                        <p style="color: #6b7280; margin: 0; font-size: 14px;">No client follow-ups are scheduled for the next few days, and there are no overdue tasks.</p>
                    </div>
                @endif

                @if($upcoming->isNotEmpty())
                    <h2 class="section-title today">
                        <span class="badge badge-today" style="margin-right: 8px; vertical-align: middle;">{{ $upcoming->count() }}</span>
                        Upcoming Follow-ups (Next 3 Days)
                    </h2>
                    
                    @foreach($upcoming as $client)
                        <div class="card today-card">
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
                                    <div class="grid-cell grid-value">{{ $client->status }}</div>
                                </div>
                                <div class="grid-row">
                                    <div class="grid-cell grid-label">Next Follow-up</div>
                                    <div class="grid-cell grid-value" style="font-weight: 600; color: #d97706;">
                                        {{ $client->next_followup_date ? $client->next_followup_date->format('M d, Y') : 'N/A' }} 
                                        ({{ $client->next_followup_date ? $client->next_followup_date->diffForHumans(['parts' => 1]) : '' }})
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
                                <div class="grid-row">
                                    <div class="grid-cell grid-label">Last Contacted</div>
                                    <div class="grid-cell grid-value">{{ $client->last_contacted_date ? $client->last_contacted_date->format('M d, Y') : 'Never' }} (every {{ $client->follow_up_days }} days)</div>
                                </div>
                                @if($client->assignedUser)
                                <div class="grid-row">
                                    <div class="grid-cell grid-label">Assigned To</div>
                                    <div class="grid-cell grid-value">{{ $client->assignedUser->name }}</div>
                                </div>
                                @endif
                            </div>

                            @if($client->notes)
                                <div class="notes-box">
                                    <strong>Latest Notes:</strong><br>
                                    {{ Str::limit($client->notes, 160) }}
                                </div>
                            @endif

                            <a href="{{ url('/clients/' . $client->id) }}" class="btn">View Client Profile</a>
                        </div>
                    @endforeach
                @endif

                @if($upcoming->isNotEmpty() && $overdue->isNotEmpty())
                    <div class="divider"></div>
                @endif

                @if($overdue->isNotEmpty())
                    <h2 class="section-title overdue">
                        <span class="badge badge-overdue" style="margin-right: 8px; vertical-align: middle;">{{ $overdue->count() }}</span>
                        Overdue Follow-ups
                    </h2>
                    
                    @foreach($overdue as $client)
                        <div class="card overdue-card">
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
                                        {{ $client->next_follow_up_date ? $client->next_follow_up_date->format('M d, Y') : 'N/A' }} 
                                        ({{ $client->next_follow_up_date ? $client->next_follow_up_date->diffForHumans() : '' }})
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
                                @if($client->assignedUser)
                                <div class="grid-row">
                                    <div class="grid-cell grid-label">Assigned To</div>
                                    <div class="grid-cell grid-value">{{ $client->assignedUser->name }}</div>
                                </div>
                                @endif
                            </div>

                            @if($client->notes)
                                <div class="notes-box">
                                    <strong>Latest Notes:</strong><br>
                                    {{ Str::limit($client->notes, 160) }}
                                </div>
                            @endif

                            <a href="{{ url('/clients/' . $client->id) }}" class="btn" style="background-color: #dc2626;">Take Action Now</a>
                        </div>
                    @endforeach
                @endif
            </div>
            
            <div class="footer">
                <p>This is an automated reminder sent from your <strong>TailAdmin Dashboard</strong>.</p>
                <p>&copy; {{ date('Y') }} TailAdmin. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
