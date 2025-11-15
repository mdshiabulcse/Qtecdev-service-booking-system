<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'name' => $this->name,
            'email' => $this->email,
            'class' => $this->class,
            'section' => $this->section,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'parent_name' => $this->parent_name,
            'parent_phone' => $this->parent_phone,
            'address' => $this->address,
            'photo' => $this->photo ? asset('storage/' . $this->photo) : null,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'today_attendance' => $this->whenLoaded('attendances', function () {
                return $this->getTodayAttendance();
            }),
        ];
    }
}
