cd "$(dirname "${BASH_SOURCE[0]}")/../"

if ! command -v php-scoper > /dev/null 2>&1; then
  echo "PHP Scoper is not installed. Installing..."
  composer global config --no-plugins allow-plugins.wpify/scoper true
  composer global require humbug/php-scoper -W
  composer global require wpify/scoper -W
fi
