<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    /** @use HasFactory<\Database\Factories\BlogPostFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'topic',
        'audience',
        'excerpt',
        'body',
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
            $this->title,
            $this->topic,
            $this->audience,
            $this->excerpt,
            $this->body,
        ]);
    }
}
