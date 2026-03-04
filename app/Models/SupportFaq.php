<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportFaq extends Model
{
    /** @use HasFactory<\Database\Factories\SupportFaqFactory> */
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'category',
        'product_line',
        'priority',
        'embedding',
    ];

    protected function casts(): array
    {
        return [
            'embedding' => 'array',
        ];
    }

    public function searchableText(): string
    {
        return implode("\n", [
            $this->question,
            $this->answer,
            $this->category,
            $this->product_line,
            $this->priority,
        ]);
    }
}
