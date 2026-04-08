<?php

namespace App\Extensions;

use Prometheus\Counter;
use Prometheus\Gauge;
use Prometheus\Histogram;
use Prometheus\Summary;
use Spatie\Prometheus\Adapters\LaravelCacheAdapter;

/**
 * 修正 LaravelCacheAdapter::collect() 的 bug：
 *
 * 原始 collect() 雖然呼叫了 $this->fetch($type)，
 * 但回傳值完全沒有 assign 給 $this->counters / $this->histograms 等 protected 屬性，
 * 導致 parent (InMemory) 用空陣列輸出，跨 request 寫入的 Counter/Histogram 永遠看不到。
 *
 * Gauge 能正常運作是因為 Spatie 在 scrape 同一個 request 內呼叫了 updateGauge()，
 * 才正確設定了 $this->gauges。Counter/Histogram 沒有這個機會，所以需要此修正。
 */
class FixedPrometheusAdapter extends LaravelCacheAdapter
{
    public function collect(bool $sortMetrics = true): array
    {
        // 從 Redis 還原所有 metric 類型到 InMemory 的 protected 屬性
        $this->gauges     = $this->fetch(Gauge::TYPE);
        $this->counters   = $this->fetch(Counter::TYPE);
        $this->histograms = $this->fetch(Histogram::TYPE);
        $this->summaries  = $this->fetch(Summary::TYPE);

        return parent::collect($sortMetrics);
    }
}
