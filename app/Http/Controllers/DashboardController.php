<?php

namespace App\Http\Controllers;

use App\Models\Student;

class DashboardController extends Controller
{
    public function index()
    {
        $students = Student::latest()
            ->take(5)
            ->get();

        $totalStudents = Student::count();

        $totalDatabases = Student::whereNotNull('mysql_database')
            ->count();

        $todayStudents = Student::whereDate(
            'created_at',
            today()
        )->count();

        return view('dashboard', compact(
            'students',
            'totalStudents',
            'totalDatabases',
            'todayStudents'
        ));
    }
}