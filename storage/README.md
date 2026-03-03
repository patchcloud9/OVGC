# Storage Directory

This directory contains runtime data and logs that are not committed to version control.

## Structure

```
storage/
├── logs/
│   ├── app.json           # Application logs (file backup)
│   ├── cron-error.log     # Weather cron errors and diagnostics
│   └── .gitkeep           # Preserves directory structure
└── cache/
    ├── weather-data.json  # NWS weather cache (refreshed every 30 min by cron)
    └── .gitkeep
```

## Logging System

The application uses a **dual persistence strategy** for logs:

### How It Works

1. **Primary**: Database (`logs` table) - Fast, queryable, production-ready
2. **Backup**: File (`storage/logs/app.json`) - Always available, survives database issues

### Normal Operation

- Logs are written to BOTH database AND file simultaneously
- Reading logs uses database (faster, more features)
- File acts as backup and ensures no logs are lost

### Database Unavailable

- File logging continues to work (guaranteed)
- UI shows yellow warning: "📁 File Backup (Database Unavailable)"
- All logs captured in `app.json` with sequential IDs
- When database recovers, use **"Sync to Database"** button to reconcile

### Sync Process

1. System detects file logs not present in database
2. Shows sync button with count: "Sync to Database (5 in file)"
3. Clicking sync compares logs by message+level to avoid duplicates
4. Reports synced count and skipped duplicates

## File Format

The `app.json` file contains an array of log entries:

```json
[
  {
    "id": 1,
    "level": "info",
    "message": "Application started",
    "context": {},
    "timestamp": "2026-02-03 10:30:45"
  }
]
```

## Maintenance

### View Logs
- Web UI: `/logs`
- File: `cat storage/logs/app.json | jq .`

### Clear All Logs
- Web UI: Click "Clear Logs" button (clears both sources)
- Manual: `echo '[]' > storage/logs/app.json` and `TRUNCATE TABLE logs;`

### Sync File to Database
- Web UI: Click "Sync to Database" button when shown
- Skips duplicates automatically

## Weather Cache

`storage/cache/weather-data.json` is written by `App\Services\WeatherService::updateCache()`, triggered by the cron endpoint `GET /cron-weather.php?key=<WEATHER_KEY>` (every 30 minutes). The homepage widget reads this file on every page load — zero external HTTP per request.

- If the file is missing or older than 60 minutes, the widget is hidden gracefully.
- Errors from the weather cron are appended to `storage/logs/cron-error.log`.
- Do **not** commit `weather-data.json` (covered by `.gitignore`).

## Best Practices

1. **Don't commit** `app.json` or `weather-data.json` - both are in `.gitignore`
2. **Monitor file size** - if `app.json` grows large, database may be down
3. **Sync regularly** - keeps database as primary source
4. **Back up file** - before clearing, especially if database was down

## Troubleshooting

### File logs not syncing
- Check database connection in `config/config.php`
- Verify `logs` table exists
- Check file permissions on `storage/logs/`

### Logs not appearing
- Check both database and file
- Verify LogService is being used (not direct error_log)
- Check PHP error logs for write failures

### File permission errors
```bash
chmod 755 storage/logs/
chmod 644 storage/logs/app.json
```
