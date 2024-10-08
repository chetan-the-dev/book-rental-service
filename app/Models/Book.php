<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'author', 'isbn', 'genre','available'];

    public function scopeFilterByName($query, $name)
    {
        return $query->when($name, function ($q, $name) {
            $q->where('name', 'LIKE', '%' . $name . '%');
        });
    }

    public function scopeFilterByGenre($query, $genre)
    {
        return $query->when($genre, function ($q, $genre) {
            $q->where('genre', $genre);
        });
    }

    public function rentals() {
        return $this->hasMany(Rental::class);
    }
}
