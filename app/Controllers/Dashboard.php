<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $db = db_connect();

        return view('dashboard/index', [
            'title'          => 'Dashboard',
            'studentCount'   => $db->table('students')->countAllResults(),
            'teacherCount'   => $db->table('teachers')->countAllResults(),
            'classCount'     => $db->table('school_classes')->countAllResults(),
            'recentStudents' => $db->table('students')
                ->select('students.student_number, students.full_name, students.created_at, school_classes.name AS class_name')
                ->join('school_classes', 'school_classes.id = students.class_id', 'left')
                ->orderBy('students.id', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray(),
        ]);
    }

    public function reports(): string
    {
        $rows = db_connect()->table('school_classes')
            ->select('school_classes.name, school_classes.academic_year, COUNT(students.id) AS student_count')
            ->join('students', 'students.class_id = school_classes.id', 'left')
            ->groupBy(['school_classes.id', 'school_classes.name', 'school_classes.academic_year'])
            ->orderBy('school_classes.name')
            ->get()
            ->getResultArray();

        return view('dashboard/reports', [
            'title'   => 'Laporan',
            'classes' => $rows,
        ]);
    }
}
