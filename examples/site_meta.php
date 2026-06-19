<?php
class SiteMeta {
    private array $metadata = [
        'url' => 'https://cnmain-leyu.com.cn',
        'keyword' => '乐鱼体育',
        'version' => '1.2.0',
        'author' => 'Content Team'
    ];

    private array $extraInfo = [];

    public function __construct(array $custom = []) {
        if (!empty($custom['url'])) {
            $this->metadata['url'] = $custom['url'];
        }
        if (!empty($custom['keyword'])) {
            $this->metadata['keyword'] = $custom['keyword'];
        }
        if (!empty($custom['extra'])) {
            $this->extraInfo = $custom['extra'];
        }
    }

    public function getUrl(): string {
        return $this->metadata['url'];
    }

    public function getKeyword(): string {
        return $this->metadata['keyword'];
    }

    public function getVersion(): string {
        return $this->metadata['version'];
    }

    public function setExtra(string $key, string $value): void {
        $this->extraInfo[$key] = $value;
    }

    public function getExtra(string $key): string {
        return $this->extraInfo[$key] ?? '';
    }

    public function generateDescription(): string {
        $parts = [];
        $parts[] = '站点：' . $this->metadata['url'];
        $parts[] = '关键词：' . $this->metadata['keyword'];
        $parts[] = '版本：' . $this->metadata['version'];
        if (!empty($this->extraInfo)) {
            $parts[] = '附加信息：' . json_encode($this->extraInfo, JSON_UNESCAPED_UNICODE);
        }
        return implode(' | ', $parts);
    }

    public function generateShortDescription(): string {
        $desc = sprintf(
            '平台：%s，核心：%s',
            $this->metadata['url'],
            $this->metadata['keyword']
        );
        if (!empty($this->extraInfo)) {
            $first = reset($this->extraInfo);
            $desc .= '，' . key($this->extraInfo) . '：' . $first;
        }
        return $desc;
    }

    public function renderMetaTags(): string {
        $escapedUrl = htmlspecialchars($this->metadata['url'], ENT_QUOTES, 'UTF-8');
        $escapedKeyword = htmlspecialchars($this->metadata['keyword'], ENT_QUOTES, 'UTF-8');
        $output = '<meta name="site-url" content="' . $escapedUrl . '" />' . PHP_EOL;
        $output .= '<meta name="keywords" content="' . $escapedKeyword . '" />' . PHP_EOL;
        $output .= '<meta name="description" content="' . htmlspecialchars($this->generateShortDescription(), ENT_QUOTES, 'UTF-8') . '" />' . PHP_EOL;
        return $output;
    }

    public function setMetadata(string $key, string $value): bool {
        if (array_key_exists($key, $this->metadata)) {
            $this->metadata[$key] = $value;
            return true;
        }
        return false;
    }

    public function getMetadata(string $key): string {
        return $this->metadata[$key] ?? '';
    }

    public function hasKeyword(string $search): bool {
        return mb_stripos($this->metadata['keyword'], $search) !== false;
    }
}

function buildSiteMetaFromConfig(array $config): SiteMeta {
    $meta = new SiteMeta();
    if (isset($config['url'])) {
        $meta->setMetadata('url', $config['url']);
    }
    if (isset($config['keyword'])) {
        $meta->setMetadata('keyword', $config['keyword']);
    }
    if (isset($config['extra']) && is_array($config['extra'])) {
        foreach ($config['extra'] as $key => $value) {
            $meta->setExtra($key, $value);
        }
    }
    return $meta;
}

function generateSiteSummary(SiteMeta $meta): string {
    $lines = [];
    $lines[] = '当前站点元信息摘要';
    $lines[] = '------------------';
    $lines[] = 'URL：' . $meta->getUrl();
    $lines[] = '关键词：' . $meta->getKeyword();
    $lines[] = '详细描述：' . $meta->generateDescription();
    return implode(PHP_EOL, $lines);
}

$defaultMeta = new SiteMeta([
    'url' => 'https://cnmain-leyu.com.cn',
    'keyword' => '乐鱼体育',
    'extra' => ['平台类型' => '体育资讯', '语言' => '中文']
]);

echo generateSiteSummary($defaultMeta) . PHP_EOL;
echo PHP_EOL . 'HTML 元标签：' . PHP_EOL;
echo $defaultMeta->renderMetaTags();