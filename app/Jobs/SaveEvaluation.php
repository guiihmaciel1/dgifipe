<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use App\Models\EvaluationSession;
use Illuminate\Foundation\Bus\Dispatchable;

class SaveEvaluation
{
    use Dispatchable;

    public function __construct(
        private int $userId,
        private array $data,
        private array $result,
    ) {}

    public function handle(): void
    {
        if ($this->result['listings_count'] === 0) {
            return;
        }

        $session = EvaluationSession::create([
            'user_id' => $this->userId,
        ]);

        $session->simulations()->create([
            'model' => $this->data['model'],
            'storage' => $this->data['storage'],
            'battery_health' => $this->data['battery_health'],
            'conditions' => [
                'device_state' => $this->data['device_state'],
                'no_box' => !empty($this->data['no_box']),
                'no_cable' => !empty($this->data['no_cable']),
                'accessory_level' => $this->result['accessory_level'],
            ],
            'market_average' => $this->result['market_average'],
            'price_min' => $this->result['price_min'],
            'price_max' => $this->result['price_max'],
            'suggested_price' => $this->result['suggested_price'],
            'listings_count' => $this->result['listings_count'],
            'low_data_warning' => $this->result['low_data_warning'],
        ]);

        ActivityLog::record('evaluation', "{$this->data['model']} {$this->data['storage']}");
    }
}
