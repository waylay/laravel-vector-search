<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductManual extends Model
{
    /** @use HasFactory<\Database\Factories\ProductManualFactory> */
    use HasFactory;

    protected $fillable = [
        'product_name',
        'version',
        'section',
        'difficulty',
        'content',
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
            $this->product_name,
            $this->version,
            $this->section,
            $this->difficulty,
            $this->content,
        ]);
    }
}
