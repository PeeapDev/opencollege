@extends('core::layouts.app')
@section('title', 'Grading')
@section('page_title', 'Grade Entry')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-xl border p-6 mb-6" x-data="gradingForm()">
        <h3 class="font-semibold text-gray-900 mb-4">Select Course & Exam</h3>
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                <select x-model="courseId" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Select course...</option>
                    @foreach($courses as $c)<option value="{{ $c->id }}">{{ $c->code }} — {{ $c->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Exam Type</label>
                <select x-model="examTypeId" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Select...</option>
                    @foreach($examTypes as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                <select x-model="semesterId" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Select...</option>
                    @foreach($semesters as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                </select>
            </div>
        </div>
        <button @click="loadStudents()" :disabled="!courseId || !examTypeId || !semesterId" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
            <i class="fas fa-users mr-1"></i> Load Students
        </button>

        <div x-show="students.length > 0" x-cloak class="mt-6">
            <form method="POST" action="{{ route('exam.grading.save') }}">
                @csrf
                <input type="hidden" name="course_id" :value="courseId">
                <input type="hidden" name="exam_type_id" :value="examTypeId">
                <input type="hidden" name="semester_id" :value="semesterId">

                <table class="w-full text-sm border rounded-lg overflow-hidden">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">#</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Student</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Matric</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Score (0-100)</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Grade</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <template x-for="(s, i) in students" :key="s.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-gray-400" x-text="i+1"></td>
                                <td class="px-4 py-2 font-medium text-gray-900" x-text="s.name"></td>
                                <td class="px-4 py-2 font-mono text-xs text-gray-500" x-text="s.matric"></td>
                                <td class="px-4 py-2 text-center">
                                    <input type="number" :name="'grades['+s.id+']'" min="0" max="100" step="0.1"
                                           :value="existingGrades[s.id] || ''"
                                           @input="s.score = $event.target.value"
                                           class="w-20 px-2 py-1.5 border border-gray-300 rounded text-center text-sm focus:ring-2 focus:ring-blue-500">
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <span x-text="calcGrade(s.score)" class="px-2 py-0.5 text-xs font-bold rounded"
                                          :class="gradeColor(s.score)"></span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <div class="mt-4 flex items-center gap-3">
                    <button type="submit" class="px-6 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-save mr-1"></i> Save Grades
                    </button>
                    <span class="text-xs text-gray-400" x-text="students.length + ' students loaded'"></span>
                </div>
            </form>
        </div>

        <div x-show="loading" x-cloak class="mt-6 text-center py-8"><i class="fas fa-spinner fa-spin text-blue-500 text-xl"></i></div>
        <div x-show="error" x-cloak class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700" x-text="error"></div>
    </div>
</div>

@push('scripts')
<script>
function gradingForm() {
    return {
        courseId: '', examTypeId: '', semesterId: '',
        students: [], existingGrades: {}, loading: false, error: '',
        loadStudents() {
            this.loading = true; this.error = ''; this.students = [];
            fetch('{{ route("exam.grading.students") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ course_id: this.courseId, exam_type_id: this.examTypeId })
            })
            .then(r => r.json())
            .then(d => {
                this.students = d.students.map(s => ({...s, score: d.existing_grades[s.id] || ''}));
                this.existingGrades = d.existing_grades || {};
                this.loading = false;
                if (!this.students.length) this.error = 'No students found for this course.';
            })
            .catch(() => { this.error = 'Error loading students'; this.loading = false; });
        },
        calcGrade(score) {
            if (!score && score !== 0) return '—';
            score = parseFloat(score);
            if (score >= 90) return 'A+'; if (score >= 85) return 'A'; if (score >= 80) return 'A-';
            if (score >= 75) return 'B+'; if (score >= 70) return 'B'; if (score >= 65) return 'B-';
            if (score >= 60) return 'C+'; if (score >= 55) return 'C'; if (score >= 50) return 'C-';
            if (score >= 45) return 'D+'; if (score >= 40) return 'D'; return 'F';
        },
        gradeColor(score) {
            if (!score && score !== 0) return '';
            score = parseFloat(score);
            if (score >= 70) return 'bg-green-100 text-green-700';
            if (score >= 50) return 'bg-amber-100 text-amber-700';
            return 'bg-red-100 text-red-700';
        }
    }
}
</script>
@endpush
@endsection
