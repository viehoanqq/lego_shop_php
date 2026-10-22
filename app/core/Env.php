<?php
/**
 * Đọc file .env ở thư mục gốc dự án (KEY=VALUE, dòng bắt đầu bằng # là chú thích).
 * Biến đã có sẵn trong môi trường (VD: bộ test tự đặt DB_NAME) sẽ KHÔNG bị ghi đè.
 * Dùng: env('DB_HOST', 'localhost')
 */
class Env {
    private static $loaded = false;

    public static function load($path) {
        if (self::$loaded) return;
        self::$loaded = true;
        if (!is_readable($path)) return;

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) continue;

            [$key, $value] = array_map('trim', explode('=', $line, 2));
            // Bỏ chú thích cuối dòng nếu giá trị không nằm trong dấu nháy
            if ($value !== '' && $value[0] !== '"' && $value[0] !== "'") {
                $value = trim(preg_replace('/\s+#.*$/', '', $value));
            }
            // Bỏ dấu nháy bao quanh
            if (strlen($value) >= 2 && ($value[0] === '"' || $value[0] === "'") && substr($value, -1) === $value[0]) {
                $value = substr($value, 1, -1);
            }

            if (getenv($key) !== false) continue;
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

function env($key, $default = null) {
    $value = getenv($key);
    if ($value === false) return $default;
    switch (strtolower($value)) {
        case 'true':  return true;
        case 'false': return false;
        case 'null':  return null;
    }
    return $value;
}
