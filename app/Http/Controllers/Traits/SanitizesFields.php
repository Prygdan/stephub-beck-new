<?php

namespace App\Http\Controllers\Traits;

trait SanitizesFields
{
  protected array $allowedDescriptionTags = [
    'p', 'br', 'strong', 'b', 'em', 'i',
    'ul', 'ol', 'li',
    'a',
    'h2', 'h3', 'h4', 'h5', 'h6',
    'blockquote'
  ];

  public function sanitizeData(array $data): array
  {
    if (isset($data['title'])) {
      $data['title'] = $this->sanitizeDescription($data['title']);
    }
    if (isset($data['name'])) {
      $data['name'] = $this->sanitizeDescription($data['name']);
    }
    if (isset($data['description'])) {
      $data['description'] = $this->sanitizeDescription($data['description']);
    }
    if (isset($data['meta_title'])) {
      $data['meta_title'] = $this->sanitizeMetaTags($data['meta_title']);
    }
    if (isset($data['meta_description'])) {
      $data['meta_description'] = $this->sanitizeMetaTags($data['meta_description']);
    }
    if (isset($data['meta_keywords'])) {
      $data['meta_keywords'] = $this->sanitizeMetaTags($data['meta_keywords']);
    }

    return $data;
  }
  
  protected function sanitizeMetaTags(string $text): string
  {
    $clean = strip_tags($text);
    $clean = preg_replace('/U\+[0-9A-F]{3,4}|\\\u[0-9A-F]{4}/i', '', $clean);
    $clean = preg_replace('/[^\p{L}0-9\s.,\'"«»“”‘’\-–—]/u', ' ', $clean);
    $clean = preg_replace('/[\x{0000}-\x{001F}\x{007F}-\x{009F}\x{200B}-\x{200D}\x{FEFF}]/u', '', $clean);
    $clean = preg_replace('/\p{Zs}/u', ' ', $clean);
    $clean = preg_replace('/([^\s])([.,])/', '$1 $2', $clean);
    $clean = preg_replace('/([.,])([^\s])/', '$1 $2', $clean);
    $clean = preg_replace('/\s*([.,])\s*/u', '$1 ', $clean);
    $clean = preg_replace('/\s+/', ' ', $clean);
    $clean = trim($clean);
    $clean = preg_replace('/[\'"“”‘’]/u', '"', $clean);
    $clean = preg_replace_callback('/"([^"]+)"/u', function ($matches) {
        return '«' . $matches[1] . '»';
    }, $clean);

    $clean = preg_replace('/\s+/', ' ', $clean);
    $clean = trim($clean);
    $clean = str_replace("\xc2\xa0", ' ', $clean);
    $clean = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $clean);

    return $clean;
  }

  protected function sanitizeDescription(string $description): string
  {
    // 1. Дозволяємо тільки whitelist тегів
    $allowed = '<' . implode('><', $this->allowedDescriptionTags) . '>';
    $clean = strip_tags($description, $allowed);

    // 2. Вирізаємо XSS-атрибути
    $clean = preg_replace(
        '/(<[^>]+)(on\w+|style|javascript:|data:)([^>]*>)/i',
        '$1$3',
        $clean
    );

    // 3. Чистимо control-символи, але НЕ чіпаємо HTML
    $clean = preg_replace(
        '/[\x{0000}-\x{001F}\x{007F}-\x{009F}\x{200B}-\x{200D}\x{FEFF}]/u',
        '',
        $clean
    );

    // 4. Unicode normalize
    $clean = \Normalizer::normalize($clean, \Normalizer::FORM_C);

    return trim($clean);
  }
}
