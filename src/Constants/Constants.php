<?php

define('FOOINO_PER_PAGE', 30);
define('FOOINO_PRIORITY_STEP', 1000);
define('FOOINO_IMAGE_EXTENSION', ['png', 'jpg', 'jpeg', 'svg', 'gif', 'webp']);
define('FOOINO_IMAGE_AND_VIDEO_EXTENSION', ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp', 'mp4']);
define('FOOINO_VIDEO_EXTENSION', ['mp4']);
define('FOOINO_EXCEL_EXTENSION', ['xlsx', 'xls']);


define('FOOINO_TEXT_PRIMARY', 'text-primary');
define('FOOINO_TEXT_SECONDARY', 'text-secondary');
define('FOOINO_TEXT_SUCCESS', 'text-success');
define('FOOINO_TEXT_INFO', 'text-info');
define('FOOINO_TEXT_DANGER', 'text-danger');
define('FOOINO_TEXT_WARNING', 'text-warning');


define('FOOINO_ACTIVE_LANGUAGES_CACHE_KEY', 'fooino-active-languages');
define('FOOINO_MODELS_CACHE_KEY', 'fooino-models');
define('FOOINO_ALL_COUNTRIES_CACHE_KEY', 'fooino-all-countries-cache-key');
define('FOOINO_ACTIVE_COUNTRIES_CACHE_KEY', 'fooino-active-countries-cache-key');




define('FOOINO_VERY_LOW_TTL_TIME', (60 * 5)); // 5 minutes
define('FOOINO_LOW_TTL_TIME', (60 * 60)); // 1 hour
define('FOOINO_MEDIUM_TTL_TIME', (60 * 60 * 24)); // 1 day
define('FOOINO_HIGH_TTL_TIME', (60 * 60 * 24 * 7)); // 1 week
define('FOOINO_VERY_HIGH_TTL_TIME', (60 * 60 * 24 * 30));// 1 month