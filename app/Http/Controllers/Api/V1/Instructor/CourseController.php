<?php

namespace App\Http\Controllers\Api\V1\Instructor;

use App\Http\Controllers\Controller;
use App\Models\DrivingSchool\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    // Add or Update Course
    public function addOrUpdateCourse(Request $request, $id = null)
    {
        $instructorId = auth('driver')->id(); // Get the authenticated instructor ID

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'curriculum_title' => 'nullable',
            'curriculum' => 'nullable',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5000',
            'duration' => 'nullable|string|max:50',

            'lessons' => 'nullable|array', // Array of lessons
            'lessons.*.title' => 'required_with:lessons|string|max:255',
            'lessons.*.duration' => 'nullable|string|max:50',
            'lessons.*.resources' => 'nullable|array', // Array of resources for each lesson
            'lessons.*.resources.*' => 'nullable|string', // Each resource can be a URL or file
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Handle banner image upload if present
        $bannerImagePath = $request->hasFile('banner_image')
            ? uploadImage($request->file('banner_image'), 'courses/banners')
            : null;

        $courseData = $request->only([
            'title',
            'subtitle',
            'price',
            'description',
            'features',
            'curriculum_title',
            'curriculum',
            'duration'
        ]);

        // Add banner image path if uploaded
        if ($bannerImagePath) {
            $courseData['banner_image'] = $bannerImagePath;
        }

        // Create or update the course
        $course = Course::updateOrCreate(
            ['id' => $id, 'instructor_id' => $instructorId],
            $courseData
        );

        // Handle lessons if provided
        if ($request->has('lessons')) {
            $this->addOrUpdateLessons($course, $request->input('lessons'));
        }

        return jsonResponseWithData(true, 'Course saved successfully.', ['course' => $course->load('lessons')]);
    }

    // Add or Update Lessons for a Course
    private function addOrUpdateLessons(Course $course, array $lessons)
    {
        foreach ($lessons as $lessonData) {
            $lessonData['resources'] = $this->processResources($lessonData['resources'] ?? []);
            $course->lessons()->updateOrCreate(['title' => $lessonData['title']], $lessonData);
        }
    }

    // Process resources for each lesson (handle URLs or file uploads)
    private function processResources(array $resources)
    {
        $processedResources = [];
        foreach ($resources as $resource) {
            if (filter_var($resource, FILTER_VALIDATE_URL)) {
                // Resource is a valid URL, add directly
                $processedResources[] = $resource;
            } elseif (is_file($resource)) {
                // Handle file upload
                $filePath = uploadDocument($resource, 'lessons/resources');
                $processedResources[] = url($filePath);
            }
        }
        return $processedResources;
    }
    // Delete Course
    public function deleteCourse($id)
    {
        $instructorId = auth('driver')->id();

        // Find the course
        $course = Course::where('id', $id)->where('instructor_id', $instructorId)->first();

        if (!$course) {
            return jsonResponse(false, 'Course not found or unauthorized access.');
        }

        // Delete the course
        $course->delete();

        return jsonResponse(true, 'Course deleted successfully.');
    }

    // Show All Courses
    public function showAllCourses()
    {
        $instructorId = auth('driver')->id();

        $courses = Course::select(['id', 'title', 'subtitle', 'total_enrollments', 'status', 'banner_image'])->where('instructor_id', $instructorId)->get();

        return jsonResponseWithData(true, 'Courses fetched successfully.', ['courses' => $courses]);
    }

    // Show Particular Course
    public function showCourse($id)
    {
        $instructorId = auth('driver')->id();

        $course = Course::where('id', $id)->where('instructor_id', $instructorId)->with('lessons', 'ratings')->first();

        if (!$course) {
            return jsonResponse(false, 'Course not found or unauthorized access.');
        }

        return jsonResponseWithData(true, 'Course fetched successfully.', ['course' => $course]);
    }
}
