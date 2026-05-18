<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory;

    // Indispensable pour que Incident::create() fonctionne
    protected $fillable = ['user_id', 'resource_id', 'description', 'status'];

    public function resource() {
        return $this->belongsTo(Resource::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}