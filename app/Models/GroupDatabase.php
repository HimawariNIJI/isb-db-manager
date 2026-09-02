<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;

class GroupDatabase extends Model
{
    use HasFactory;

    protected $fillable = ['database_name', 'password'];

    public function members()
    {
        return $this->belongsToMany(
            Student::class,
            'group_database_student',
            'group_database_id',
            'student_id'
        );
    }

    // Backwards-compatible alias: some controllers expect `students` relation
    public function students()
    {
        return $this->members();
    }
}
