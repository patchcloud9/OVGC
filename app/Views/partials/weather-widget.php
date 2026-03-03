<?php
/**
 * Weather widget partial
 *
 * Expects $weatherData: array from WeatherService::getWidgetData(), or null.
 * Renders nothing if data is unavailable.
 */
if (empty($weatherData) || empty($weatherData['current']['temp_f'])) {
    return;
}

$current  = $weatherData['current'];
$forecast = $weatherData['forecast'] ?? [];

$curIcon = \App\Services\WeatherService::iconClass(
    $current['condition'] ?? 'ovc',
    (bool) ($current['daytime'] ?? true)
);
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/weather-icons/2.0.12/css/weather-icons.min.css">
<style>
.wx-widget {
    display: flex;
    align-items: center;
    gap: 2rem;
    max-width: 860px;
    margin: 0 auto;
    padding: 1.1rem 1.75rem;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 6px rgba(0,0,0,.09);
}
.wx-current {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 120px;
    gap: .15rem;
}
.wx-current .wi {
    font-size: 3.5rem;
    color: #9ca3af;
    line-height: 1;
}
.wx-current .wi-day-sunny,
.wx-current .wi-hot { color: #f59e0b; }
.wx-current-temp {
    font-size: 2.25rem;
    font-weight: 600;
    color: #374151;
    line-height: 1.1;
}
.wx-current-desc {
    font-size: .8rem;
    color: #6b7280;
    text-align: center;
    text-transform: lowercase;
    max-width: 130px;
}
.wx-divider {
    width: 1px;
    height: 80px;
    background: #e5e7eb;
    flex-shrink: 0;
}
.wx-forecast {
    display: flex;
    gap: .75rem;
    flex: 1;
}
.wx-day {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: .65rem .75rem;
    background: #f9fafb;
    border-radius: 6px;
    gap: .3rem;
}
.wx-day-name {
    font-size: .8rem;
    font-weight: 600;
    color: #374151;
    text-align: center;
}
.wx-day .wi {
    font-size: 2rem;
    color: #9ca3af;
    line-height: 1;
}
.wx-day .wi-day-sunny,
.wx-day .wi-hot { color: #f59e0b; }
.wx-day-temps {
    display: flex;
    gap: .45rem;
    font-size: .85rem;
    font-weight: 600;
}
.wx-hi { color: #dc2626; }
.wx-lo { color: #2563eb; }
@media (max-width: 600px) {
    .wx-widget   { flex-direction: column; gap: 1rem; }
    .wx-divider  { width: 80%; height: 1px; }
    .wx-forecast { width: 100%; }
}
</style>

<div class="wx-widget">

    <!-- Current conditions -->
    <div class="wx-current">
        <i class="wi <?= e($curIcon) ?>"></i>
        <div class="wx-current-temp"><?= e($current['temp_f']) ?>°F</div>
        <div class="wx-current-desc"><?= e($current['desc']) ?></div>
    </div>

    <?php if (!empty($forecast)): ?>
    <div class="wx-divider"></div>

    <!-- 3-day forecast -->
    <div class="wx-forecast">
        <?php foreach ($forecast as $day):
            $dayIcon = \App\Services\WeatherService::iconClass($day['condition'] ?? 'ovc');
        ?>
        <div class="wx-day">
            <div class="wx-day-name"><?= e($day['day']) ?></div>
            <i class="wi <?= e($dayIcon) ?>"></i>
            <div class="wx-day-temps">
                <span class="wx-hi"><?= e($day['high_f']) ?>°F</span>
                <?php if ($day['low_f'] !== null): ?>
                <span class="wx-lo"><?= e($day['low_f']) ?>°F</span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>
