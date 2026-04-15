<?php

use App\Modules\Exam\Controllers\ExamController;
use App\Modules\Exam\Controllers\ExamBoardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('exams', ExamController::class);

    // Exam Board
    Route::get('/exam-schedules', [ExamBoardController::class, 'schedules'])->name('exam.schedules');
    Route::get('/exam-schedules/create', [ExamBoardController::class, 'createSchedule'])->name('exam.schedules.create');
    Route::post('/exam-schedules', [ExamBoardController::class, 'storeSchedule'])->name('exam.schedules.store');

    // Grading
    Route::get('/grading', [ExamBoardController::class, 'grading'])->name('exam.grading');
    Route::post('/grading/load-students', [ExamBoardController::class, 'loadStudentsForGrading'])->name('exam.grading.students');
    Route::post('/grading/save', [ExamBoardController::class, 'saveGrades'])->name('exam.grading.save');

    // Results
    Route::get('/result-publications', [ExamBoardController::class, 'results'])->name('exam.results');
    Route::post('/result-publications/publish', [ExamBoardController::class, 'publishResults'])->name('exam.results.publish');
});
