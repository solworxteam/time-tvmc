<?php

declare(strict_types=1);

date_default_timezone_set('Europe/London');

$pdo = new PDO(
    "mysql:host=127.0.0.1;dbname=timetvmcorg_mosquesuk;charset=utf8mb4",
    "root",
    "",
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 20,
    ]
);

function fetchHtml(string $url): string
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_TIMEOUT => 25,
        CURLOPT_USERAGENT => 'TVMC Prayer Sync Bot/1.0 (+https://time.tvmc.org.uk/)',
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    $html = curl_exec($ch);
    if ($html === false) {
        $err = curl_error($ch);
        curl_close($ch);
        throw new RuntimeException("Fetch failed: {$err}");
    }

    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status >= 400) {
        throw new RuntimeException("HTTP status {$status} from {$url}");
    }

    return $html;
}

function normalizeTime(?string $value): ?string
{
    if ($value === null) {
        return null;
    }

    $value = trim(str_replace(['Iqm', 'Iqamah', 'Jamaah', 'Begins'], '', $value));
    if ($value === '') {
        return null;
    }

    $formats = ['g:i A', 'g:i a', 'H:i', 'H.i', 'g.i A'];
    foreach ($formats as $format) {
        $dt = DateTime::createFromFormat($format, $value);
        if ($dt instanceof DateTime) {
            return $dt->format('H:i:s');
        }
    }

    return null;
}

function logRun(PDO $pdo, ?string $mosqueId, ?string $url, ?string $parser, string $status, string $message, int $rows, string $startedAt): void
{
    $stmt = $pdo->prepare("
        INSERT INTO scraper_runs
        (mosque_id, source_url, parser_type, run_started_at, run_finished_at, status, message, rows_written)
        VALUES
        (:mosque_id, :source_url, :parser_type, :started_at, NOW(), :status, :message, :rows_written)
    ");

    $stmt->execute([
        ':mosque_id' => $mosqueId,
        ':source_url' => $url,
        ':parser_type' => $parser,
        ':started_at' => $startedAt,
        ':status' => $status,
        ':message' => $message,
        ':rows_written' => $rows,
    ]);
}

function upsertPrayerRow(PDO $pdo, array $row): void
{
    $sql = "
        INSERT INTO prayertimes
        (mosque_id, date, fajar_start, zuhr_start, asr_start, maghrib, isha_start, fajar_jamaat, zuhr_jamaat, asr_jamaat, isha_jamaat)
        VALUES
        (:mosque_id, :date, :fajar_start, :zuhr_start, :asr_start, :maghrib, :isha_start, :fajar_jamaat, :zuhr_jamaat, :asr_jamaat, :isha_jamaat)
        ON DUPLICATE KEY UPDATE
            fajar_start = VALUES(fajar_start),
            zuhr_start = VALUES(zuhr_start),
            asr_start = VALUES(asr_start),
            maghrib = VALUES(maghrib),
            isha_start = VALUES(isha_start),
            fajar_jamaat = VALUES(fajar_jamaat),
            zuhr_jamaat = VALUES(zuhr_jamaat),
            asr_jamaat = VALUES(asr_jamaat),
            isha_jamaat = VALUES(isha_jamaat)
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':mosque_id' => $row['mosque_id'],
        ':date' => $row['date'],
        ':fajar_start' => $row['fajar_start'],
        ':zuhr_start' => $row['zuhr_start'],
        ':asr_start' => $row['asr_start'],
        ':maghrib' => $row['maghrib'],
        ':isha_start' => $row['isha_start'],
        ':fajar_jamaat' => $row['fajar_jamaat'],
        ':zuhr_jamaat' => $row['zuhr_jamaat'],
        ':asr_jamaat' => $row['asr_jamaat'],
        ':isha_jamaat' => $row['isha_jamaat'],
    ]);
}

function parseJmicStyle(string $html, string $mosqueId): array
{
    if (!preg_match('/Begins\s+([0-9:\.apmAPM ]+)\s+([0-9:\.apmAPM ]+)\s+([0-9:\.apmAPM ]+)\s+([0-9:\.apmAPM ]+)\s+([0-9:\.apmAPM ]+)/', $html, $begins)) {
        throw new RuntimeException('Could not parse Begins row');
    }

    if (!preg_match('/Jamaah\s+([0-9:\.apmAPM ]+)\s+([0-9:\.apmAPM ]+)\s+([0-9:\.apmAPM ]+)\s+([0-9:\.apmAPM ]+)\s+([0-9:\.apmAPM ]+)/', $html, $jamaah)) {
        throw new RuntimeException('Could not parse Jamaah row');
    }

    return [[
        'mosque_id' => $mosqueId,
        'date' => date('Y-m-d'),
        'fajar_start' => normalizeTime($begins[1]),
        'zuhr_start' => normalizeTime($begins[2]),
        'asr_start' => normalizeTime($begins[3]),
        'maghrib' => normalizeTime($begins[4]),
        'isha_start' => normalizeTime($begins[5]),
        'fajar_jamaat' => normalizeTime($jamaah[1]),
        'zuhr_jamaat' => normalizeTime($jamaah[2]),
        'asr_jamaat' => normalizeTime($jamaah[3]),
        'isha_jamaat' => normalizeTime($jamaah[5]),
    ]];
}

function parseTiecmStyle(string $html, string $mosqueId): array
{
    preg_match('/Fajr\s+([0-9: ]+[APMapm]{2}).*?Iqm\s+([0-9: ]+[APMapm]{2})/s', $html, $fajr);
    preg_match('/Dhuhr\s+([0-9: ]+[APMapm]{2}).*?Iqm\s+([0-9: ]+[APMapm]{2})/s', $html, $zuhr);
    preg_match('/Asr\s+([0-9: ]+[APMapm]{2}).*?Iqm\s+([0-9: ]+[APMapm]{2})/s', $html, $asr);
    preg_match('/Maghrib\s+([0-9: ]+[APMapm]{2}).*?Iqm\s+([0-9: ]+[APMapm]{2})/s', $html, $maghrib);
    preg_match('/Isha\s+([0-9: ]+[APMapm]{2}).*?Iqm\s+([0-9: ]+[APMapm]{2})/s', $html, $isha);

    return [[
        'mosque_id' => $mosqueId,
        'date' => date('Y-m-d'),
        'fajar_start' => normalizeTime($fajr[1] ?? null),
        'zuhr_start' => normalizeTime($zuhr[1] ?? null),
        'asr_start' => normalizeTime($asr[1] ?? null),
        'maghrib' => normalizeTime($maghrib[1] ?? null),
        'isha_start' => normalizeTime($isha[1] ?? null),
        'fajar_jamaat' => normalizeTime($fajr[2] ?? null),
        'zuhr_jamaat' => normalizeTime($zuhr[2] ?? null),
        'asr_jamaat' => normalizeTime($asr[2] ?? null),
        'isha_jamaat' => normalizeTime($isha[2] ?? null),
    ]];
}

function parseAlJannahStyle(string $html, string $mosqueId): array
{
    preg_match('/FAJR.*?Jamaat.*?([0-9: ]+[APMapm]{2})/s', $html, $fajr);
    preg_match('/ZUHR.*?Jamaat.*?([0-9: ]+[APMapm]{2})/s', $html, $zuhr);
    preg_match('/ASAR.*?Jamaat.*?([0-9: ]+[APMapm]{2})/s', $html, $asr);
    preg_match('/MAGHRIB.*?Jamaat.*?([0-9: ]+[APMapm]{2})/s', $html, $maghrib);
    preg_match('/ISHA.*?Jamaat.*?([0-9: ]+[APMapm]{2})/s', $html, $isha);

    return [[
        'mosque_id' => $mosqueId,
        'date' => date('Y-m-d'),
        'fajar_start' => null,
        'zuhr_start' => null,
        'asr_start' => null,
        'maghrib' => normalizeTime($maghrib[1] ?? null),
        'isha_start' => null,
        'fajar_jamaat' => normalizeTime($fajr[1] ?? null),
        'zuhr_jamaat' => normalizeTime($zuhr[1] ?? null),
        'asr_jamaat' => normalizeTime($asr[1] ?? null),
        'isha_jamaat' => normalizeTime($isha[1] ?? null),
    ]];
}

function parseWindsorStyle(string $html, string $mosqueId): array
{
    preg_match('/Fajr\s+([0-9: ]+[apm]{2})\s+([0-9: ]+[apm]{2})/i', $html, $fajr);
    preg_match('/Zuhr\s+([0-9: ]+[apm]{2})\s+([0-9: ]+[apm]{2})/i', $html, $zuhr);
    preg_match('/Asr\s+([0-9: ]+[apm]{2})\s+([0-9: ]+[apm]{2})/i', $html, $asr);
    preg_match('/Maghrib\s+([0-9: ]+[apm]{2})\s+([0-9: ]+[apm]{2})/i', $html, $maghrib);
    preg_match('/Isha\s+([0-9: ]+[apm]{2})\s+([0-9: ]+[apm]{2})/i', $html, $isha);

    return [[
        'mosque_id' => $mosqueId,
        'date' => date('Y-m-d'),
        'fajar_start' => normalizeTime($fajr[1] ?? null),
        'zuhr_start' => normalizeTime($zuhr[1] ?? null),
        'asr_start' => normalizeTime($asr[1] ?? null),
        'maghrib' => normalizeTime($maghrib[1] ?? null),
        'isha_start' => normalizeTime($isha[1] ?? null),
        'fajar_jamaat' => normalizeTime($fajr[2] ?? null),
        'zuhr_jamaat' => normalizeTime($zuhr[2] ?? null),
        'asr_jamaat' => normalizeTime($asr[2] ?? null),
        'isha_jamaat' => normalizeTime($isha[2] ?? null),
    ]];
}

$sourceRows = $pdo->query("
    SELECT s.mosque_id, s.source_url, s.parser_type, m.name
    FROM scraper_sources s
    JOIN mosques m ON m.id = s.mosque_id
    WHERE s.is_active = 1
    ORDER BY m.name
")->fetchAll();

foreach ($sourceRows as $source) {
    $startedAt = date('Y-m-d H:i:s');
    $rowsWritten = 0;

    try {
        if (in_array($source['parser_type'], ['pdf_or_notice', 'link_page', 'js_app'], true)) {
            throw new RuntimeException('Parser not implemented yet for this source type');
        }

        $html = fetchHtml($source['source_url']);

        switch ($source['parser_type']) {
            case 'jmic_html':
                $rows = parseJmicStyle($html, $source['mosque_id']);
                break;
            case 'tiecm_html':
                $rows = parseTiecmStyle($html, $source['mosque_id']);
                break;
            case 'aljannah_html':
                $rows = parseAlJannahStyle($html, $source['mosque_id']);
                break;
            case 'windsor_html':
                $rows = parseWindsorStyle($html, $source['mosque_id']);
                break;
            default:
                throw new RuntimeException('Unknown parser type: ' . $source['parser_type']);
        }

        foreach ($rows as $row) {
            upsertPrayerRow($pdo, $row);
            $rowsWritten++;
        }

        logRun(
            $pdo,
            $source['mosque_id'],
            $source['source_url'],
            $source['parser_type'],
            'success',
            'Sync completed',
            $rowsWritten,
            $startedAt
        );

        echo "[OK] {$source['name']} - {$rowsWritten} row(s)\n";
        sleep(3);

    } catch (Throwable $e) {
        logRun(
            $pdo,
            $source['mosque_id'],
            $source['source_url'],
            $source['parser_type'],
            'failed',
            $e->getMessage(),
            $rowsWritten,
            $startedAt
        );

        echo "[FAIL] {$source['name']} - {$e->getMessage()}\n";
        sleep(3);
    }
}