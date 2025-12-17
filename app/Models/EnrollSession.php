<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnrollSession extends Model {
    protected $table = 'enroll_sessions';
    protected $fillable = ['siswa_id','token','status','fingerprint_id'];
}
